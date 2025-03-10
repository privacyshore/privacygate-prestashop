<?php
namespace PrivacyGateSDK\Operations;

use PrivacyGateSDK\Util;

trait DeleteMethodTrait
{
    public function delete($headers = [])
    {
        $id = $this->getPrimaryKeyValue();

        if (!\is_scalar($id)) {
            throw new \Exception('id is not set.');
        }

        $path = Util::joinPath(static::getResourcePath(), $id);
        $client = static::getClient();
        $client->delete($path, $headers);
        $this->clearAttributes();
    }

    public static function deleteById($id, $headers = [])
    {
        if (!\is_scalar($id)) {
            throw new \Exception('Invalid id provided.');
        }

        $path = Util::joinPath(static::getResourcePath(), $id);
        $client = static::getClient();
        $client->delete($path, $headers);

        return new static;
    }
}
