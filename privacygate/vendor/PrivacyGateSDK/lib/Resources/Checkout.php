<?php
namespace PrivacyGateSDK\Resources;

use PrivacyGateSDK\Operations\CreateMethodTrait;
use PrivacyGateSDK\Operations\DeleteMethodTrait;
use PrivacyGateSDK\Operations\ReadMethodTrait;
use PrivacyGateSDK\Operations\SaveMethodTrait;
use PrivacyGateSDK\Operations\UpdateMethodTrait;

class Checkout extends ApiResource implements ResourcePathInterface
{
    use ReadMethodTrait, CreateMethodTrait, UpdateMethodTrait, DeleteMethodTrait, SaveMethodTrait;

    /**
     * @return string
     */
    public static function getResourcePath()
    {
        return 'checkouts';
    }
}
