<?php
namespace PrivacyGateSDK\Exceptions;

class PrivacyGateException extends \Exception
{
    public static function getClassName()
    {
        return get_called_class();
    }
}
