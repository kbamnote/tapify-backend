<?php
/**
 * Database-backed PHP session handler.
 *
 * Keeps session data in the `sessions` table so logins survive Railway
 * redeploys/restarts — the container filesystem where default file sessions
 * live is wiped on every deploy. If the `sessions` table doesn't exist yet
 * (migration not run), tableExists() returns false and database.php falls back
 * to the default file handler, so login still works.
 *
 * Relies on getDB() being defined (config/database.php).
 */
class DBSessionHandler implements SessionHandlerInterface
{
    private static $tableChecked = null;

    /** Cheap one-time check (per request) that the sessions table is present. */
    public static function tableExists()
    {
        if (self::$tableChecked !== null) {
            return self::$tableChecked;
        }
        try {
            $r = getDB()->query("SHOW TABLES LIKE 'sessions'");
            self::$tableChecked = (bool)$r->fetch();
        } catch (Throwable $e) {
            self::$tableChecked = false;
        }
        return self::$tableChecked;
    }

    #[\ReturnTypeWillChange]
    public function open($path, $name) { return true; }

    #[\ReturnTypeWillChange]
    public function close() { return true; }

    #[\ReturnTypeWillChange]
    public function read($id)
    {
        try {
            $stmt = getDB()->prepare("SELECT data FROM sessions WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            return $row ? (string)$row['data'] : '';
        } catch (Throwable $e) {
            return '';
        }
    }

    #[\ReturnTypeWillChange]
    public function write($id, $data)
    {
        try {
            $stmt = getDB()->prepare("REPLACE INTO sessions (id, data, last_activity) VALUES (?, ?, ?)");
            return $stmt->execute([$id, $data, time()]);
        } catch (Throwable $e) {
            return false;
        }
    }

    #[\ReturnTypeWillChange]
    public function destroy($id)
    {
        try {
            getDB()->prepare("DELETE FROM sessions WHERE id = ?")->execute([$id]);
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    #[\ReturnTypeWillChange]
    public function gc($maxlifetime)
    {
        try {
            $stmt = getDB()->prepare("DELETE FROM sessions WHERE last_activity < ?");
            $stmt->execute([time() - (int)$maxlifetime]);
            return $stmt->rowCount();
        } catch (Throwable $e) {
            return false;
        }
    }
}
