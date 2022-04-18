<?php
namespace PrivacyGateSDK\Resources;

use PrivacyGateSDK\Operations\CreateMethodTrait;
use PrivacyGateSDK\Operations\ReadMethodTrait;
use PrivacyGateSDK\Operations\SaveMethodTrait;

class Charge extends ApiResource implements ResourcePathInterface
{
    use CreateMethodTrait, ReadMethodTrait, SaveMethodTrait;

    /**
     * @return string
     */
    public static function getResourcePath()
    {
        return 'charges';
    }

    public function hasMetadataParam($key)
    {
        return isset($this->attributes['metadata'][$key]);
    }

    public function getMetadataParam($key)
    {
        return isset($this->attributes['metadata'][$key]) ? $this->attributes['metadata'][$key] : null;
    }
}
