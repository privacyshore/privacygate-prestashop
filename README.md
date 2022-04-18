# PrivacyGate Prestashop Payment Module

# Installation
1. Signup for an account at [PrivacyGate](https://dash.privacygate.io/).
2. Create an API Key by going to the Settings tab in the PrivacyGate dashboard.
3. Copy the `privacygate/` folder to your Prestashop `modules/` folder.
4. Login to your Prestashop Back Office, navigate to the Modules tab, go to the "Installed Modules" tab and search for "PrivacyGate". Click Install to activate the plugin.
5. Click Configure to go to the settings page of the plugin. Set the API Key, Shared Secret Key from PrivacyGate Dashboard.
6. Copy webhook url from settings page of the plugin to PrivacyGate DashBoard Settings. 

**NOTE:** There is a setting for "Unsafe" mode on the plugins settings page. This should never be set to "Enabled" on a production website. 
It is only used for making testing easier during development, since it will deactivate any validation of the requests that is send to the webhook, which 
will allow the developer to emulate POST requests to the webhook without generating the `X-CC-Webhook-Signature` header.

# Localization
All text strings, labels and descriptions found in the plugin is translatable. You can translate the plugin from the 
International/Translations tab in the Prestashop Back Office.

# Integrate with other e-commerce platforms

[PrivacyGate Integrations](https://privacygate.io/docs)