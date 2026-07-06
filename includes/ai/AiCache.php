<?php
/**
 * TAPIFY AI Growth Center — cache repository.
 *
 * One row per (user, feature, input-hash) holding the last successful result.
 * If the ai_cache table is missing (migration not run), every method degrades
 * to a no-op so the feature still works (just without caching).
 */
class AiCache
{
    /** @var PDO */
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /** @return array|null ['output','provider','model','history_id','created_at'] */
    public function get($userId, $feature, $hash)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT output_json, provider, model, history_id, updated_at
                 FROM ai_cache
                 WHERE user_id = ? AND feature = ? AND input_hash = ?
                 LIMIT 1"
            );
            $stmt->execute([$userId, $feature, $hash]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) return null;

            $output = json_decode($row['output_json'], true);
            if (!is_array($output)) return null;

            return [
                'output'     => $output,
                'provider'   => $row['provider'],
                'model'      => $row['model'],
                'history_id' => $row['history_id'] !== null ? (int) $row['history_id'] : null,
                'created_at' => $row['updated_at'],
            ];
        } catch (Exception $e) {
            AiLogger::warn('cache.get_failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function put($userId, $feature, $hash, array $input, array $output, $provider, $model, $historyId)
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO ai_cache (user_id, feature, input_hash, input_json, output_json, provider, model, history_id)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE
                    input_json = VALUES(input_json),
                    output_json = VALUES(output_json),
                    provider = VALUES(provider),
                    model = VALUES(model),
                    history_id = VALUES(history_id),
                    updated_at = CURRENT_TIMESTAMP"
            );
            $stmt->execute([
                $userId, $feature, $hash,
                json_encode($input, JSON_UNESCAPED_UNICODE),
                json_encode($output, JSON_UNESCAPED_UNICODE),
                $provider, $model, $historyId,
            ]);
        } catch (Exception $e) {
            AiLogger::warn('cache.put_failed', ['error' => $e->getMessage()]);
        }
    }
}
