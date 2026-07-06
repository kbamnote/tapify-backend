<?php
/**
 * TAPIFY Google Business Profile — logger (never logs tokens/secrets).
 */
class GoogleLogger
{
    public static function info($event, array $ctx = [])  { self::write('INFO', $event, $ctx); }
    public static function warn($event, array $ctx = [])  { self::write('WARN', $event, $ctx); }
    public static function error($event, array $ctx = []) { self::write('ERROR', $event, $ctx); }

    private static function write($level, $event, array $ctx)
    {
        $safe = [];
        foreach ($ctx as $k => $v) {
            if (preg_match('/token|secret|key|authorization|refresh/i', (string) $k)) continue;
            $safe[$k] = is_scalar($v) ? $v : json_encode($v);
        }
        $line = '[GBP][' . $level . '] ' . $event;
        if ($safe) $line .= ' ' . json_encode($safe);
        error_log($line);
    }
}
