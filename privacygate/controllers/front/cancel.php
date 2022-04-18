<?php
if (!defined('_PS_VERSION_')) {
    exit();
}

class PrivacyGateCancelModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        Tools::redirect('index.php?controller=order&step=1');
    }
}
