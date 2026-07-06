<?php
/**
 * TAPIFY AI Growth Center — prompt builder.
 *
 * Prompts are NEVER hardcoded in controllers/services. Each feature owns one
 * file under prompts/<feature>.php that defines two functions:
 *   ai_prompt_<feature>(array $input): string   -> the prompt to send
 *   ai_shape_<feature>(array $decoded): array    -> normalise the parsed JSON
 * ("<feature>" is the endpoint key with dashes replaced by underscores.)
 */
class PromptBuilder
{
    /** Build the prompt string for a feature. */
    public static function build($feature, array $input)
    {
        $slug = self::slug($feature);
        self::load($slug);
        $fn = 'ai_prompt_' . $slug;
        if (!function_exists($fn)) {
            throw new AiException('The AI service is misconfigured.', 500, "Missing prompt builder {$fn}");
        }
        return $fn($input);
    }

    /** Normalise a feature's parsed JSON into the shape the app expects. */
    public static function shape($feature, array $decoded)
    {
        $slug = self::slug($feature);
        self::load($slug);
        $fn = 'ai_shape_' . $slug;
        return function_exists($fn) ? $fn($decoded) : $decoded;
    }

    private static function slug($feature)
    {
        return str_replace('-', '_', strtolower(trim($feature)));
    }

    private static function load($slug)
    {
        // Guard against path traversal via the feature name.
        if (!preg_match('/^[a-z0-9_]+$/', $slug)) {
            throw new AiException('Unknown AI feature.', 400, "Invalid feature slug '{$slug}'");
        }
        $file = __DIR__ . '/prompts/' . $slug . '.php';
        if (!is_file($file)) {
            throw new AiException('Unknown AI feature.', 404, "No prompt file for '{$slug}'");
        }
        require_once $file;
    }

    /**
     * Escape a user value for safe embedding as prompt data. Keeps the model
     * from treating input as instructions (basic prompt-injection hygiene).
     */
    public static function field(array $input, $key, $fallback = 'Not provided')
    {
        $v = isset($input[$key]) ? trim((string) $input[$key]) : '';
        if ($v === '') return $fallback;
        // Neutralise fence/instruction breakers.
        return str_replace(['```', '"""'], "'''", $v);
    }
}
