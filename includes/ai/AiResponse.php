<?php
/**
 * TAPIFY AI Growth Center — response parsing helpers.
 *
 * Models sometimes wrap JSON in ```json fences or add stray prose. This turns
 * raw model text into a clean associative array, or throws AiException.
 */
class AiResponse
{
    /**
     * Parse the model's text as a JSON object.
     *
     * @throws AiException when no valid JSON object can be recovered.
     */
    public static function json($text)
    {
        $clean = self::stripFences($text);

        $decoded = json_decode($clean, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // Fallback: grab the outermost {...} block and try again.
        $start = strpos($clean, '{');
        $end   = strrpos($clean, '}');
        if ($start !== false && $end !== false && $end > $start) {
            $slice   = substr($clean, $start, $end - $start + 1);
            $decoded = json_decode($slice, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        AiLogger::error('response.parse_failed', ['snippet' => substr((string) $text, 0, 300)]);
        throw new AiException('The AI returned an unexpected format. Please try Regenerate.', 502,
            'JSON parse failed');
    }

    /** Remove ```json ... ``` / ``` ... ``` fences and trim. */
    public static function stripFences($text)
    {
        $t = trim((string) $text);
        if (strpos($t, '```') !== false) {
            $t = preg_replace('/^```[a-zA-Z0-9]*\s*/', '', $t);
            $t = preg_replace('/\s*```$/', '', $t);
        }
        return trim($t);
    }

    /**
     * Ensure $value is a clean list of non-empty strings (used for keyword /
     * tagline arrays that models occasionally return as a delimited string).
     */
    public static function stringList($value)
    {
        if (is_string($value)) {
            $value = preg_split('/\r\n|\r|\n|,/', $value);
        }
        if (!is_array($value)) {
            return [];
        }
        $out = [];
        foreach ($value as $item) {
            if (is_array($item)) {
                $item = reset($item);
            }
            $s = trim((string) $item);
            $s = preg_replace('/^\s*(?:\d+[\.\)]|[-*•])\s*/', '', $s); // strip bullets/numbering
            $s = trim($s, " \t\"'");
            if ($s !== '') {
                $out[] = $s;
            }
        }
        return array_values(array_unique($out));
    }
}
