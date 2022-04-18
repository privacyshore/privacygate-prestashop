<?php

if (!defined('_PS_VERSION_')) {
    exit();
}

if (defined('_PS_MODULE_DIR_')) {
    require_once _PS_MODULE_DIR_ . 'privacygate/classes/OrderManager.php';
    require_once _PS_MODULE_DIR_ . 'privacygate/vendor/PrivacyGateSDK/init.php';
    require_once _PS_MODULE_DIR_ . 'privacygate/vendor/PrivacyGateSDK/const.php';
}

class PrivacyGateProcessModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        // Check that payment module is active, to prevent users from 
        // calling this controller when payment method is inactive. 
        if (!$this->isModuleActive()) {
            die($this->module->l('This payment method is not available.', 'payment'));
        }

        $cart = $this->context->cart;
        $customer = new Customer($cart->id_customer);
        $total = OrderManager::getCartTotal($cart);

        $this->module->validateOrder(
            $cart->id,
            Configuration::get('PRIVACYGATE_NEW'),
            $total,
            $this->module->displayName,
            null,
            null,
            (int)$cart->id_currency,
            false,
            $customer->secure_key
        );

        $chargeObj = $this->apiCreateCharge($cart);
        header('Location: ' . $chargeObj->hosted_url);
    }

    /**
     * Check if the current module is an active payment module.
     */
    public function isModuleActive()
    {
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'privacygate') {
                $authorized = true;
                break;
            }
        }

        return $authorized;
    }

    public function apiCreateCharge($cart)
    {
        $products = array_map(function ($item) {
            return $item['cart_quantity'] . ' × ' . $item['name'];
        }, $cart->getProducts());

        $orderId = method_exists('Order', 'getOrderByCartId') ?
            Order::getOrderByCartId($cart->id) : Order::getIdByCartId($cart->id);

        $chargeData = array(
            'local_price' => array(
                'amount' => OrderManager::getCartTotal($cart),
                'currency' => OrderManager::getCurrencyIsoById($cart->id_currency)
            ),
            'pricing_type' => 'fixed_price',
            'name' => Configuration::get('PS_SHOP_NAME') . ' order #' . $orderId,
            'description' => join($products, ', '),
            'metadata' => [
                METADATA_SOURCE_PARAM => METADATA_SOURCE_VALUE,
                METADATA_INVOICE_ID_PARAM => $orderId,
                METADATA_CLIENT_ID_PARAM => $cart->id_customer,
                METADATA_CART_ID_PARAM => $cart->id
            ],
            'redirect_url' => OrderManager::getOrderConfirmationUrl($this->context, $cart->id, $this->module->id),
            'cancel_url' => OrderManager::getOrderCancelUrl($this->context, $this->module->name)
        );

        $apiKey = Configuration::get('PRIVACYGATE_API_KEY');
        \PrivacyGateSDK\ApiClient::init($apiKey);

        return \PrivacyGateSDK\Resources\Charge::create($chargeData);
    }
}
