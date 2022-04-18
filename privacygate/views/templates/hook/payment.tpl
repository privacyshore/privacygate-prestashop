<style>
	p.payment_module a.privacygate:after {
		display: block;
		content: "\f054";
		position: absolute;
		right: 15px;
		margin-top: -11px;
		top: 50%;
		font-family: "FontAwesome";
		font-size: 25px;
		height: 22px;
		width: 14px;
		color: #777;
	}
</style>

<p class="payment_module">
	<a class="privacygate" href="{$link->getModuleLink('privacygate', 'process', [], true)|escape:'html'}" title="{l s='Pay by PrivacyGate' mod='privacygate'}">
		{l s='Pay by PrivacyGate' mod='cheque'} <span>{l s='(pay using cryptocurrencies)' mod='privacygate'}</span>
	</a>
</p>
