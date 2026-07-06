<?php
/**
 * TAPIFY AI Growth Center — endpoint bootstrap + shared handler.
 *
 * config/database.php sets the JSON header, sessions and CORS (incl. OPTIONS
 * preflight), so feature endpoints only declare their fields and call
 * ai_run_feature(). All validation, provider calls, caching, history and error
 * mapping are centralised here.
 */
require_once __DIR__ . '/../../config/database.php';
// Keep PHP warnings/fatals out of the JSON body (they would corrupt the
// envelope and leak file paths). Errors are still logged via error_reporting.
ini_set('display_errors', '0');
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/ai/autoload.php';

/**
 * Handle a "generate" request for one feature.
 *
 * @param string $feature   endpoint key, e.g. 'business-description'
 * @param array  $required  fields that must be non-empty
 * @param array  $fields    all accepted input field names
 * @param array  $caps      optional per-field max lengths (default 2000)
 */
function ai_run_feature($feature, array $required, array $fields, array $caps = [])
{
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendError('Method not allowed', 405);
    }

    requireAuth();
    $userId = getCurrentUserId();

    $input = getInput();

    // --- Validate required fields (DTO-style) ---
    $missing = [];
    foreach ($required as $f) {
        if (trim((string) ($input[$f] ?? '')) === '') {
            $missing[] = $f;
        }
    }
    if ($missing) {
        sendError('Please fill in: ' . implode(', ', array_map('ai_label', $missing)), 422);
    }

    // --- Collect + bound accepted fields ---
    $data = [];
    foreach ($fields as $f) {
        if (!array_key_exists($f, $input)) continue;
        $v = $input[$f];
        if (is_string($v)) {
            $max = isset($caps[$f]) ? (int) $caps[$f] : 2000;
            $v = trim($v);
            $v = function_exists('mb_substr') ? mb_substr($v, 0, $max) : substr($v, 0, $max);
        }
        $data[$f] = $v;
    }

    $regenerate = !empty($input['regenerate']);

    try {
        $service = new AiService(getDB());
        $result  = $service->generate($userId, $feature, $data, $regenerate);
        sendSuccess($result['cached'] ? 'Loaded saved result' : 'Generated', $result);
    } catch (AiException $e) {
        sendError($e->getSafeMessage(), $e->getHttpStatus());
    } catch (Exception $e) {
        AiLogger::error('endpoint.unexpected', ['feature' => $feature, 'error' => $e->getMessage()]);
        sendError('Something went wrong while generating. Please try again.', 500);
    }
}

/** Human-friendly field label for validation messages. */
function ai_label($field)
{
    return ucwords(str_replace('_', ' ', $field));
}
