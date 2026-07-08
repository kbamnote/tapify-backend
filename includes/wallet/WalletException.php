<?php
/**
 * TAPIFY Wallet — exception with a user-safe message + HTTP status.
 */
class WalletException extends Exception
{
    private $httpStatus;
    private $safeMessage;

    public function __construct($safeMessage, $httpStatus = 400, $internalMessage = null, $previous = null)
    {
        parent::__construct($internalMessage ?: $safeMessage, 0, $previous);
        $this->safeMessage = $safeMessage;
        $this->httpStatus  = $httpStatus;
    }

    public function getSafeMessage() { return $this->safeMessage; }
    public function getHttpStatus()  { return $this->httpStatus; }
}
