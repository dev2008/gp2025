<?php
$deploy_page = <<<'EOD'
<h1>Publish & Deploy</h1>

<p>AppifyText.ai created a <span class="tooltip3">DaDaBIK<span class="tooltip3text">DaDaBIK is a low-code no-code platform, AppifyText.ai, as AI agent, uses DaDaBIK to build an application for you. Think of AppifyText.ai as an AI layer on top of DaDaBIK.</span></span> application for you and we are temporarily hosting your app here, with some limitations.

<p><b>Do you want to remove such limitations, publish your app and make it available to your users?</b><br>
<p>You need a DaDaBIK license, then you can <b>import your app into DaDaBIK</b> and deploy it to your preferred platform, such as Amazon AWS, MS Azure, your Linux or Windows Intranet Server, any hosting provider, or even your laptop (check the <span class="tooltip3">requirements<span class="tooltip3text"><strong>Main requirements:</strong> <br>1) PHP 8.1 or 8.2 with the mbstring and IonCube loader extensions enabled <br><br>2) MySQL version >= 5.0 or MariaDB</span></span>).<br><br>With DaDaBIK, you can continue editing your application (no-code) and also add custom JavaScript and PHP code (low-code).

<p><strong>Start a 30-day free trial of DaDaBIK Enterprise now, easily cancel anytime</strong>


<script>
function decorateURL(url) {
window.clsid = {
 client_id: '',
 session_id: ''
};
gtag('get', 'G-TXKXP0WVPN', 'client_id', function(r) {
 window.clsid.client_id = r
});
gtag('get', 'G-TXKXP0WVPN', 'session_id', function(r) {
 window.clsid.session_id = r
});
var _pl = '&clientId=' + window.clsid.client_id + '&sessionId=' + window.clsid.session_id;
return url + '?' + _pl;
}
</script>

<script
id="fsc-api"
src="https://sbl.onfastspring.com/sbl/0.9.6/fastspring-builder.min.js"
type="text/javascript"
data-storefront="dadabik.onfastspring.com/popup-dadabik"
data-popup-closed="onFSPopupClosed"
data-data-callback="dataCallback"
data-decorate-callback=”decorateURL”>
</script>




<script>
var s = {};
s.paymentContact = {};

tag1 = '0-99';
s.tags =  {
"tag1": tag1
};
fastspring.builder.push(s);

if (true){

}

function change_newsletter()
{
tag1 = '0-99';
if (document.getElementById('newsletter').checked == true){
tag1 = '1-99';
};
s.tags =  {
"tag1": tag1
};
fastspring.builder.push(s);
}

function onFSPopupClosed(orderReference) {
if (orderReference)
{
fastspring.builder.reset();
window.location.replace("index.php?function=thanks_order");
}
else{
window.location.reload();
}

}



var a = '<p  class="fs-lg"><button  onclick="if (document.getElementById(\'privacy\').checked == false || document.getElementById(\'privacy_2\').checked == false ){alert(\'You have to accept the terms to proceed.\');this.setAttribute(\'data-fsc-action\', \'\');}else{this.setAttribute(\'data-fsc-action\', \'Add, Checkout\');}" data-fsc-item-path-value="dadabik-enterprise-monthly" data-fsc-action="Add, Checkout" class="btn btn-lg btn-success green" id="buy_button">Start free trial</button>  ';



 a = a + '<br/><br/><input type="checkbox" id="privacy" name="privacy" value="1"> I accept the <a href="https://www.iubenda.com/privacy-policy/875935" target="_blank">privacy policy</a> '
    
    a =  a + '<br/><input type="checkbox" id="privacy_2" name="privacy_2" value="1"> I accept the <a target="_blank" href="https://dadabik.com/index.php?function=show_license">license</a>.';
    
    a = a+'  <br/><input type="checkbox" id="newsletter" name="newsletter" value="1" onclick="javascript:change_newsletter();"> Subscribe me to the DaDaBIK newsletter (<strong>strongly suggested </strong> <i class="bx bx-info-circle fs-lg" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="We hate spam and we won\'t spam you. The newsletter informs about new releases, security issues and gives other announcements about DaDaBIK, approximately 10 messages/year. Unsubscribe with one click." ></i>)  </p>';
     
    document.write(a);
    
    
    
</script>
<noscript>
<br/><strong>You have to enable Javascript in order to proceed.</strong>
</noscript>

The package you'll download contains instructions on how to import your app into DaDaBIK. After the first 30 days, the monthly fee is $ 19, you can cancel your subscription anytime, during or after the free trial. You can find more information about DaDaBIK low-code no-code at <a href="https://dadabik.com" target="_blank">DaDaBIK.com</a>. If you already have a DaDaBIK license, just follow the installation instructions.



EOD;