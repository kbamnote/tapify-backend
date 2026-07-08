<?php
/**
 * TAPIFY Meta Ads — exception with user-safe message + HTTP status.
 */
class AdsException extends Exception
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
