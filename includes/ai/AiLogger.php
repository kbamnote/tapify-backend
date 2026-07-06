<?php
/**
 * TAPIFY AI Growth Center — lightweight logger.
 *
 * Writes structured lines to PHP's error_log so failures/timeouts/rate-limits
 * are observable in production without pulling in a logging framework. Never
 * logs API keys.
 */
class AiLogger
{
    public static function info($event, array $context = [])
    {
        self::write('INFO', $event, $context);
    }

    public static function warn($event, array $context = [])
    {
        self::write('WARN', $event, $context);
    }

    public static function error($event, array $context = [])
    {
        self::write('ERROR', $event, $context);
    }

    private static function write($level, $event, array $context)
    {
        $safe = [];
        foreach ($context as $k => $v) {
            if (preg_match('/key|secret|token|authorization/i', (string) $k)) {
                continue; // never log secrets
            }
            $safe[$k] = is_scalar($v) ? $v : json_encode($v);
        }
        $line = '[AI][' . $level . '] ' . $event;
        if ($safe) {
            $line .= ' ' . json_encode($safe);
        }
        error_log($line);
    }
}
