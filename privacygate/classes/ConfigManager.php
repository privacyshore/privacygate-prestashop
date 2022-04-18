<?php

if (!defined('_PS_VERSION_')) {
    exit();
}

/**
 * For testing purposes we wrap the Configuration in a wrapper class
 * so that we can easily mock it.
 */
class ConfigManager
{
    public function addFields()
    {
        $orderNew = $this->createOrderStatus('Privacygate awaiting status', '#D0CA64');
        $orderPending = $this->createOrderStatus('Privacygate pending status', '#007FFF');

        if (
            Configuration::updateValue('PRIVACYGATE_API_KEY', null)
            && Configuration::updateValue('PRIVACYGATE_SANDBOX', null)
            && Configuration::updateValue('PRIVACYGATE_SHARED_SECRET', null)
            && Configuration::updateValue('PRIVACYGATE_NEW', $orderNew->id)
            && Configuration::updateValue('PRIVACYGATE_PENDING', $orderPending->id)
        ) {
            return true;
        }

        return false;
    }

    public function createOrderStatus($name, $color)
    {
        $order = new OrderState();
        $order->name = array_fill(0, 10, $name);
        $order->send_email = 0;
        $order->invoice = 0;
        $order->color = $color;
        $order->unremovable = false;
        $order->logable = 0;
        $order->add();

        return $order;
    }

    public function deleteFields()
    {
        $orderNew = new OrderState(Configuration::get('PRIVACYGATE_NEW'));
        $orderPending = new OrderState(Configuration::get('PRIVACYGATE_PENDING'));

        if (
            Configuration::deleteByName('PRIVACYGATE_API_KEY')
            && Configuration::deleteByName('PRIVACYGATE_SANDBOX')
            && Configuration::deleteByName('PRIVACYGATE_SHARED_SECRET')
            && $orderNew->delete()
            && $orderPending->delete()
        ) {
            return true;
        }

        return false;
    }
}
