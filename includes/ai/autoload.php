<?php
/**
 * TAPIFY AI Growth Center — module loader.
 *
 * No Composer autoloader exists in this project, so the AI classes are wired up
 * explicitly (in dependency order). Endpoints just:
 *   require_once __DIR__ . '/../../config/database.php';
 *   require_once __DIR__ . '/../../includes/functions.php';
 *   require_once __DIR__ . '/../../includes/ai/autoload.php';
 */
$__ai_dir = __DIR__;

require_once $__ai_dir . '/AiException.php';
require_once $__ai_dir . '/AiLogger.php';
require_once $__ai_dir . '/AiResponse.php';
require_once $__ai_dir . '/AiProviderInterface.php';
require_once $__ai_dir . '/BaseHttpProvider.php';
require_once $__ai_dir . '/GeminiProvider.php';
require_once $__ai_dir . '/OpenAiProvider.php';
require_once $__ai_dir . '/ClaudeProvider.php';
require_once $__ai_dir . '/OpenRouterProvider.php';
require_once $__ai_dir . '/AiProviderFactory.php';
require_once $__ai_dir . '/PromptBuilder.php';
require_once $__ai_dir . '/AiCache.php';
require_once $__ai_dir . '/AiHistory.php';
require_once $__ai_dir . '/AiRateLimiter.php';
require_once $__ai_dir . '/AiService.php';

unset($__ai_dir);
