<?php
/**
 * TAPIFY AI Growth Center — history repository.
 *
 * Records every generation (success or error) and powers the per-card History
 * view and the Save/bookmark action. Degrades to a no-op if ai_history is
 * missing so generation never fails just because logging can't write.
 */
class AiHistory
{
    /** @var PDO */
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /** @return int|null inserted history id */
    public function log($userId, $feature, $input, $output, $provider, $model, $status = 'success', $error = null)
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO ai_history (user_id, feature, input_json, output_json, provider, model, status, error)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $userId,
                $feature,
                $input !== null ? json_encode($input, JSON_UNESCAPED_UNICODE) : null,
                $output !== null ? json_encode($output, JSON_UNESCAPED_UNICODE) : null,
                $provider,
                $model,
                $status,
                $error,
            ]);
            return (int) $this->db->lastInsertId();
        } catch (Exception $e) {
            AiLogger::warn('history.log_failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /** @return array list of history rows (output decoded) for the user. */
    public function listForUser($userId, $feature = null, $savedOnly = false, $limit = 30)
    {
        try {
            $limit = max(1, min(100, (int) $limit));
            $sql = "SELECT id, feature, input_json, output_json, provider, model, status, is_saved, created_at
                    FROM ai_history
                    WHERE user_id = ? AND status = 'success'";
            $args = [$userId];
            if ($feature !== null && $feature !== '') {
                $sql .= " AND feature = ?";
                $args[] = $feature;
            }
            if ($savedOnly) {
                $sql .= " AND is_saved = 1";
            }
            $sql .= " ORDER BY id DESC LIMIT " . $limit;

            $stmt = $this->db->prepare($sql);
            $stmt->execute($args);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map([$this, 'hydrate'], $rows);
        } catch (Exception $e) {
            AiLogger::warn('history.list_failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /** Toggle/set the saved flag for one row owned by the user. */
    public function setSaved($userId, $id, $saved)
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE ai_history SET is_saved = ? WHERE id = ? AND user_id = ?"
            );
            $stmt->execute([$saved ? 1 : 0, (int) $id, $userId]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            AiLogger::warn('history.save_failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function hydrate($row)
    {
        return [
            'id'         => (int) $row['id'],
            'feature'    => $row['feature'],
            'input'      => $row['input_json'] ? json_decode($row['input_json'], true) : null,
            'result'     => $row['output_json'] ? json_decode($row['output_json'], true) : null,
            'provider'   => $row['provider'],
            'model'      => $row['model'],
            'is_saved'   => (int) $row['is_saved'] === 1,
            'created_at' => $row['created_at'],
        ];
    }
}
