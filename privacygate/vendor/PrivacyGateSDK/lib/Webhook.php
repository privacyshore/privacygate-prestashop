<?php
namespace PrivacyGateSDK;

use PrivacyGateSDK\Exceptions\InvalidResponseException;
use PrivacyGateSDK\Exceptions\SignatureVerificationException;
use PrivacyGateSDK\Resources\Event;
use PrivacyGateSDK\Util;

class Webhook
{
    public static function buildEvent($payload, $sigHeader, $secret)
    {
        $data = null;

        $data = \json_decode($payload, true);

        if (json_last_error()) {
            throw new InvalidResponseException('Invalid payload provided. No JSON object could be decoded.', $payload);
        }

        if (!isset($data['event'])) {
            throw new InvalidResponseException('Invalid payload provided.', $payload);
        }

        self::verifySignature($payload, $sigHeader, $secret);

        return new Event($data['event']);
    }

    public static function verifySignature($payload, $sigHeader, $secret)
    {
        $computedSignature = \hash_hmac('sha256', $payload, $secret);

        if (!Util::hashEqual($sigHeader, $computedSignature)) {
            throw new SignatureVerificationException($computedSignature, $payload);
        }
    }
}
