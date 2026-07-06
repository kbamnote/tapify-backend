<?php
/**
 * TAPIFY AI Growth Center — AI-layer exception.
 *
 * Carries an HTTP status so endpoints can surface a sensible code, plus a
 * `$safeMessage` that is always OK to show to the end user (never leaks keys,
 * upstream payloads or stack details).
 */
class AiException extends Exception
{
    /** @var int HTTP status the endpoint should return. */
    private $httpStatus;

    /** @var string User-safe message. */
    private $safeMessage;

    public function __construct($safeMessage, $httpStatus = 502, $internalMessage = null, $previous = null)
    {
        parent::__construct($internalMessage ?: $safeMessage, 0, $previous);
        $this->safeMessage = $safeMessage;
        $this->httpStatus  = $httpStatus;
    }

    public function getSafeMessage()
    {
        return $this->safeMessage;
    }

    public function getHttpStatus()
    {
        return $this->httpStatus;
    }
}
