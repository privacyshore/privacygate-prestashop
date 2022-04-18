<?php
namespace PrivacyGateSDK\Exceptions;

class ApiException extends PrivacyGateException
{
    public function __construct($message, $code)
    {
        parent::__construct($message, $code);
    }
}
