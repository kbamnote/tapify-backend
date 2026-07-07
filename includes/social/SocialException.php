<?php
/**
 * TAPIFY Social Publishing — exception with a user-safe message + HTTP status.
 * The safe message never contains tokens or raw upstream payloads.
 */
class SocialException extends Exception
{
    private $httpStatus;
    private $safeMessage;

    public function __construct($safeMessage, $httpStatus = 502, $internalMessage = null, $previous = null)
    {
        parent::__construct($internalMessage ?: $safeMessage, 0, $previous);
        $this->safeMessage = $safeMessage;
        $this->httpStatus  = $httpStatus;
    }

    public function getSafeMessage() { return $this->safeMessage; }
    public function getHttpStatus()  { return $this->httpStatus; }
}
