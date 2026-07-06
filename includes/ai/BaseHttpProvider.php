<?php
/**
 * TAPIFY AI Growth Center — shared HTTP transport for AI providers.
 *
 * Centralises the concerns the spec calls out (rate limits, API failures,
 * timeouts, retry logic, logging) so every concrete provider only has to
 * describe its endpoint, headers, request body and how to read the answer.
 */
abstract class BaseHttpProvider implements AiProviderInterface
{
    /**
     * POST JSON to $url and return the decoded response array.
     * Retries with exponential backoff on timeouts, HTTP 429 and 5xx.
     *
     * @throws AiException
     */
    protected function postJson($url, array $headers, array $body, $providerLabel)
    {
        $timeout    = defined('AI_HTTP_TIMEOUT') ? (int) AI_HTTP_TIMEOUT : 45;
        $maxRetries = defined('AI_MAX_RETRIES') ? (int) AI_MAX_RETRIES : 2;
        $payload    = json_encode($body);

        $attempt = 0;
        $lastErr = 'Unknown error';

        while ($attempt <= $maxRetries) {
            $attempt++;

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_HTTPHEADER     => array_merge(['Content-Type: application/json'], $headers),
                CURLOPT_TIMEOUT        => $timeout,
                CURLOPT_CONNECTTIMEOUT => 10,
            ]);

            $raw      = curl_exec($ch);
            $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErrN = curl_errno($ch);
            $curlErr  = curl_error($ch);
            curl_close($ch);

            // --- Transport-level failure (timeout, DNS, connection reset) ---
            if ($raw === false || $curlErrN !== 0) {
                $lastErr = "transport error ({$curlErrN}): {$curlErr}";
                $isTimeout = in_array($curlErrN, [CURLE_OPERATION_TIMEOUTED, CURLE_COULDNT_CONNECT], true);
                AiLogger::warn('provider.transport_error', [
                    'provider' => $providerLabel, 'attempt' => $attempt, 'error' => $lastErr,
                ]);
                if ($attempt <= $maxRetries) { $this->backoff($attempt); continue; }
                throw new AiException(
                    $isTimeout ? 'The AI service timed out. Please try again.'
                               : 'Could not reach the AI service. Please try again.',
                    504, $lastErr
                );
            }

            // --- Retryable HTTP statuses (rate limit / upstream errors) ---
            if ($httpCode === 429 || $httpCode >= 500) {
                $lastErr = "HTTP {$httpCode}: " . substr((string) $raw, 0, 500);
                AiLogger::warn('provider.retryable_status', [
                    'provider' => $providerLabel, 'attempt' => $attempt, 'status' => $httpCode,
                ]);
                if ($attempt <= $maxRetries) { $this->backoff($attempt); continue; }
                throw new AiException(
                    $httpCode === 429 ? 'The AI service is busy right now. Please try again in a moment.'
                                      : 'The AI service is temporarily unavailable. Please try again.',
                    503, $lastErr
                );
            }

            $decoded = json_decode($raw, true);
            if (!is_array($decoded)) {
                throw new AiException('Received an invalid response from the AI service.', 502,
                    'non-JSON response: ' . substr((string) $raw, 0, 500));
            }

            // --- Non-retryable client errors (bad key, bad request) ---
            if ($httpCode >= 400) {
                $apiMsg = $this->extractApiError($decoded);
                AiLogger::error('provider.client_error', [
                    'provider' => $providerLabel, 'status' => $httpCode, 'error' => $apiMsg,
                ]);
                // Never echo raw upstream text to the client — it can leak org
                // ids, model names or billing detail. Keep it in the internal
                // (logged) message only.
                $safe = ($httpCode === 401 || $httpCode === 403)
                    ? 'The AI service rejected the request (check the API key).'
                    : 'The AI request was rejected. Please adjust your input and try again.';
                throw new AiException($safe, $httpCode, "HTTP {$httpCode}: {$apiMsg}");
            }

            return $decoded; // success
        }

        throw new AiException('The AI service is unavailable. Please try again.', 503, $lastErr);
    }

    /** Exponential backoff: ~0.4s, 0.8s, 1.6s ... capped. */
    private function backoff($attempt)
    {
        $ms = (int) min(2000, 200 * pow(2, $attempt));
        usleep($ms * 1000);
    }

    /** Best-effort extraction of an error message from a provider payload. */
    protected function extractApiError(array $decoded)
    {
        if (isset($decoded['error']['message'])) return $decoded['error']['message'];
        if (isset($decoded['error']) && is_string($decoded['error'])) return $decoded['error'];
        if (isset($decoded['message'])) return $decoded['message'];
        return 'unknown provider error';
    }
}
