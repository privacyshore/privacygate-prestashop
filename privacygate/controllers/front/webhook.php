<?php
if (!defined('_PS_VERSION_')) {
    exit();
}

if (defined('_PS_MODULE_DIR_')) {
    require_once _PS_MODULE_DIR_ . 'privacygate/vendor/PrivacyGateSDK/init.php';
    require_once _PS_MODULE_DIR_ . 'privacygate/vendor/PrivacyGateSDK/const.php';
}

class PrivacyGateWebhookModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        $event = $this->constructEvent();
        \PrivacyGateSDK\ApiClient::init(Configuration::get('PRIVACYGATE_API_KEY'));
        $chargeId = $event->data->id;
        $chargeObj = \PrivacyGateSDK\Resources\Charge::retrieve($chargeId);
        if (empty($chargeObj->timeline)) {
            throw new Exception('Invalid charge');
        }
        $lastTimeLine = end($chargeObj->timeline);
        $orderId = (int)$chargeObj->getMetadataParam(METADATA_INVOICE_ID_PARAM);
        $cartId = (int)$chargeObj->getMetadataParam(METADATA_CART_ID_PARAM);
        $order = new Order($orderId);

        if (!$order || $order->id_cart != $cartId) {
            throw new Exception('Order not exists');
        }

        $status = $this->getStatusByTimeLine($lastTimeLine);

        if (is_null($status)) {
            throw new Exception('Invalid status');
        }

        //Update order status
        $history = new OrderHistory();
        $history->id_order = $order->id;
        $history->changeIdOrderState((int)Configuration::get($status), $order->id);

        // If charge payment exists then update transaction id with charge id
        $chargePayment = end($chargeObj->payments);
        $payments = $order->getOrderPaymentCollection();
        if ($payments->count() > 0 && $chargePayment && isset($chargePayment['transaction_id'])) {
            $payments[0]->transaction_id = $chargeId;
            $payments[0]->update();
        }

        die(0);
    }

    private function getStatusByTimeLine($timeline)
    {
        switch ($timeline['status']) {
            case 'NEW':
                return 'PRIVACYGATE_NEW';
            case 'PENDING':
                return 'PRIVACYGATE_PENDING';
            case 'EXPIRED':
                return 'PS_OS_ERROR';
            case 'COMPLETED':
                return 'PS_OS_PAYMENT';
            case 'CANCELED':
                return 'PS_OS_CANCELED';
            case 'UNRESOLVED':
                // mark order as paid on overpaid
                if ($timeline['context'] === 'OVERPAID') {
                    return 'PS_OS_PAYMENT';
                } else {
                    return 'PS_OS_ERROR';
                }
            case 'RESOLVED':
                return 'PS_OS_PAYMENT';
            default:
                return null;
        }
    }

    private function constructEvent()
    {
        $payload = trim(file_get_contents('php://input'));

        // if test mode don't run validation
        if ((bool)Configuration::get('PRIVACYGATE_SANDBOX')) {
            $data = \json_decode($payload, true);
            return new \PrivacyGateSDK\Resources\Event($data['event']);
        }

        $sharedSecret = Configuration::get('PRIVACYGATE_SHARED_SECRET');
        $headers = array_change_key_case(getallheaders());
        $signatureHeader = isset($headers[SIGNATURE_HEADER]) ? $headers[SIGNATURE_HEADER] : null;

        return \PrivacyGateSDK\Webhook::buildEvent($payload, $signatureHeader, $sharedSecret);
    }
}
