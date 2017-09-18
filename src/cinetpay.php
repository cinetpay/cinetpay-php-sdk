<?php
/**
 * CinetPay
 *
 * LICENSE
 *
 * This source file is subject to the MIT License that is bundled
 * with this package in the file LICENSE.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@cinetpay.com so we can send you a copy immediately.
 *
 * @category   CinetPay
 * @package    cinetpay
 * @version    1.7.0
 * @license    MIT
 */

/**
 * Class CinetPay
 * @category   CinetPay
 * @package    cinetpay
 * @copyright  Copyright (c) 20015-2016 CinetPay Inc. (https://www.cinetpay.com)
 */
class CinetPay
{

    const URI_WEBSITE_PROD = 'www.cinetpay.com';
    const URI_WEBSITE_DEV = 'www.sandbox.cinetpay.com';
    const URI_CASH_DESK_PROD = 'secure.cinetpay.com';
    const URI_CASH_DESK_DEV = 'secure.sandbox.cinetpay.com';
    /**
     * An identifier
     * @var string
     */
    public $_cfg_cpm_page_action = "PAYMENT";
    /**
     * An identifier
     * @var string
     */
    public $_cfg_cpm_payment_config = "SINGLE";
    /**
     * An identifier
     * @var string
     */
    public $_cfg_cpm_version = null;
    /**
     * An identifier
     * @var string
     */
    public $_cfg_cpm_language = "fr";
    /**
     * An identifier
     * @var string
     */
    public $_cfg_cpm_currency = "CFA";
    /**
     * An identifier
     * @var string
     */
    public $_cfg_cpm_trans_date = null;
    /**
     * An identifier
     * @var string
     */
    public $_cfg_cpm_trans_id = null;
    /**
     * An identifier
     * @var string
     */
    public $_cfg_cpm_designation = null;
    /**
     * An identifier
     * @var string
     */
    public $_cfg_cpm_custom = null;
    /**
     * An identifier
     * @var string
     */
    public $_cfg_cpm_amount = null;
    /**
     * An identifier
     * @var string
     */
    public $_cfg_cpm_site_id = null;
    /**
     * An identifier
     * @var string
     */
    public $_cfg_notify_url = null;
    /**
     * An identifier
     * @var string
     */
    public $_cfg_return_url = null;
    /**
     * An identifier
     * @var string
     */
    public $_cfg_cancel_url = null;
    /**
     * An identifier
     * @var string
     */
    public $_cashDeskUri = null;
    public $_signature = null;
    public $_cpm_site_id = null;
    public $_cpm_amount = null;
    public $_cpm_trans_date = null;
    public $_cpm_trans_id = null;
    public $_cpm_custom = null;
    public $_cpm_currency = null;
    public $_cpm_payid = null;
    public $_cpm_payment_date = null;
    public $_cpm_payment_time = null;
    public $_cpm_error_message = null;
    public $_payment_method = null;
    public $_cpm_phone_prefixe = null;
    public $_cel_phone_num = null;
    public $_cpm_ipn_ack = null;
    public $_created_at = null;
    public $_updated_at = null;
    public $_cpm_result = null;
    public $_cpm_trans_status = null;
    public $_cpm_designation = null;
    public $_buyer_name = null;
    public $platformUrl = null;
    /**
     *  If true, an SSL secure connection (port 443) is used for the post back
     *  as recommended by cinetpay. If false, a standard HTTP (port 80) connection
     *  is used. Default true.
     *
     * @var boolean
     */
    public $_use_ssl = false;
    /**
     *  If true, the cinetpay sandbox URI www.sandbox.cinetpay.com is used for the
     *  post back. If false, the live URI www.cinetpay.com is used. Default false.
     *
     * @var boolean
     */
    public $_use_sandbox = false;
    /**
     * An identifier
     * @var string
     */
    protected $_cfg_apikey = null;
    /**
     * An identifier
     * @var string
     */
    protected $_signatureUri = null;
    /**
     * An identifier
     * @var string
     */
    protected $_checkPayStatusUri = null;
    /**
     * An identifier
     * @var string
     */
    protected $_webSiteUri = null;
    /**
     * @var bool
     */
    private $_debug = false;

    private $_URI_GET_SIGNATURE_PROD = null;
    private $_URI_GET_SIGNATURE_DEV = null;
    private $_URI_CHECK_PAY_STATUS_PROD = null;
    private $_URI_CHECK_PAY_STATUS_DEV = null;

    /**
     * CinetPay constructor.
     * @param $site_id
     * @param $apikey
     * @param string $mode
     * @param string $version
     * @param array $params
     */
    public function __construct($site_id, $apikey, $mode = "PROD", $version = 'v2', $params = null)
    {

        if ($mode == "PROD") {
            $this->_use_sandbox = false;
            $this->_use_ssl = true;
        } else {
            $this->_use_sandbox = true;
        }

        $this->_URI_GET_SIGNATURE_PROD = sprintf('api.cinetpay.com/%s/?method=getSignatureByPost', strtolower($version));
        $this->_URI_GET_SIGNATURE_DEV = sprintf('api.sandbox.cinetpay.com/%s/?method=getSignatureByPost', strtolower($version));
        $this->_URI_CHECK_PAY_STATUS_PROD = sprintf('api.cinetpay.com/%s/?method=checkPayStatus', strtolower($version));
        $this->_URI_CHECK_PAY_STATUS_DEV = sprintf('api.sandbox.cinetpay.com/%s/?method=checkPayStatus', strtolower($version));
        if (is_null($params) || (!empty($params['style']) && $params['style'] == true)) {
            $style = '<style>.cinetpay-button { white-space: nowrap; }.cinetpay-button .field-error {  border: 1px solid #FF0000; }.cinetpay-button .hide { display: none; }.cinetpay-button .error-box { background: #FFFFFF; border: 1px solid #DADADA; border-radius: 5px; padding: 8px; display: inline-block; }.cinetpay-button button { white-space: nowrap; overflow: hidden; border-radius: 13px; font-family: "Arial", bold, italic; font-weight: bold; font-style: italic; border: 1px solid #2ECC71; color: #000000; background: #2ECC71; position: relative; text-shadow: 0 1px 0 rgba(255,255,255,.5); cursor: pointer; z-index: 0; }.cinetpay-button button:before { content: " "; position: absolute; width: 100%; height: 100%; border-radius: 11px; top: 0; left: 0; background: #2ECC71; background: -webkit-linear-gradient(top, #28B463 0%,#28B463 80%,#FFF8FC 100%); background: -moz-linear-gradient(top, #28B463 0%,#28B463 80%,#FFF8FC 100%); background: -ms-linear-gradient(top, #28B463 0%,#28B463 80%,#FFF8FC 100%); background: linear-gradient(top, #28B463 0%,#28B463 80%,#FFF8FC 100%); z-index: -2; }.cinetpay-button button:after { content: " "; position: absolute; width: 98%; height: 60%; border-radius: 40px 40px 38px 38px; top: 0; left: 0; background: -webkit-linear-gradient(top, #fefefe 0%, #28B463 100%); background: -moz-linear-gradient(top, #fefefe 0%, #28B463 100%); background: -ms-linear-gradient(top, #fefefe 0%, #28B463 100%); background: linear-gradient(top, #fefefe 0%, #28B463 100%); z-index: -1; -webkit-transform: translateX(1%);-moz-transform: translateX(1%); -ms-transform: translateX(1%); transform: translateX(1%); }.cinetpay-button button.small { padding: 3px 15px; font-size: 12px; }.cinetpay-button button.large { padding: 4px 19px; font-size: 14px; }.cinetpay-button button.larger { padding: 5px 30px; font-size: 20px; }</style>';
            print($style);
        }
        $this->_cfg_cpm_site_id = $site_id;
        $this->_cfg_cpm_version = strtoupper($version);
        $this->_cfg_apikey = $apikey;
        $htpp_prefixe = ($this->_use_ssl) ? 'https://' : 'http://';
        $this->_cashDeskUri = $htpp_prefixe . $this->getCashDeskHost();
        $this->_signatureUri = $htpp_prefixe . $this->getSignatureHost();
        $this->_checkPayStatusUri = $htpp_prefixe . $this->getCheckPayStatusHost();
        $this->_webSiteUri = $htpp_prefixe . $this->getWebSiteHost();
    }

    /**
     * @return string
     */
    private function getCashDeskHost()
    {
        if ($this->_use_sandbox)
            return self::URI_CASH_DESK_DEV;
        else
            return self::URI_CASH_DESK_PROD;
    }

    /**
     * @return string
     */
    private function getSignatureHost()
    {
        if ($this->_use_sandbox)
            return $this->_URI_GET_SIGNATURE_DEV;
        else
            return $this->_URI_GET_SIGNATURE_PROD;
    }

    /**
     * @return string
     */
    private function getCheckPayStatusHost()
    {
        if ($this->_use_sandbox)
            return $this->_URI_CHECK_PAY_STATUS_DEV;
        else
            return $this->_URI_CHECK_PAY_STATUS_PROD;
    }

    private function getWebSiteHost()
    {
        if ($this->_use_sandbox)
            return self::URI_WEBSITE_DEV;
        else
            return self::URI_WEBSITE_PROD;
    }

    /**
     * generate transId
     * @return int
     */
    public static function generateTransId()
    {
        $timestamp = time();
        $parts = explode(' ', microtime());
        $id = ($timestamp + $parts[0] - strtotime('today 00:00')) * 10;
        $id = sprintf('%06d', $id) . mt_rand(100, 9999);

        return $id;
    }

    /**
     * print cinetpay pay form button
     * @param $formName
     * @param int $btnType
     * @param string $size
     */
    public function displayPayButton($formName, $btnType = 1, $size = "large")
    {
        print $this->getPayButton($formName, $btnType, $size);
    }

    /**
     * get the cinetpay pay form
     * @param $formName
     * @param int $btnType
     * @param string $size
     * @return string
     * @throws Exception
     */
    public function getPayButton($formName, $btnType = 1, $size = "large")
    {
        $this->getSignature();
        if (empty($this->_cfg_apikey))
            throw new Exception("Erreur: ApiKey non definie");
        if (empty($this->_cashDeskUri))
            throw new Exception("Erreur: Url de paiement non definie");
        if (empty($this->_cfg_cpm_site_id))
            throw new Exception("Erreur: Site ID non definie");
        if (empty($this->_cfg_cpm_currency))
            throw new Exception("Erreur: Devise non definie");
        if (empty($this->_cfg_cpm_page_action))
            throw new Exception("Erreur: Page action non definie");
        if (empty($this->_cfg_cpm_payment_config))
            throw new Exception("Erreur: Payment config non definie");
        if (empty($this->_cfg_cpm_version))
            throw new Exception("Erreur: Version non definie");
        if (empty($this->_cfg_cpm_language))
            throw new Exception("Erreur: Langue non definie");
        if (empty($this->_cfg_cpm_trans_date))
            throw new Exception("Erreur: Date de la transaction non definie");
        if (empty($this->_cfg_cpm_trans_id))
            throw new Exception("Erreur: ID de la transaction non definie");
        if (empty($this->_cfg_cpm_designation))
            throw new Exception("Erreur: Designation de la transaction non definie");
        if (empty($this->_cfg_cpm_amount))
            throw new Exception("Erreur: Montant de la transaction non definie");
        if (empty($this->_signature))
            throw new Exception("Erreur: Signature de la transaction non trouvee");
        if (empty($formName))
            throw new Exception("Erreur: Nom du formulaire non definie");

        $form = "<form id='" . $formName . "' name='" . $formName . "' action='" . $this->_cashDeskUri . "' class='cinetpay-button' method='post'>";
        $form .= "<input type='hidden' name='apikey' value='" . $this->_cfg_apikey . "'>";
        $form .= "<input type='hidden' name='cpm_site_id' value='" . $this->_cfg_cpm_site_id . "'>";
        $form .= "<input type='hidden' name='cpm_currency' value='" . $this->_cfg_cpm_currency . "'>";
        $form .= "<input type='hidden' name='cpm_page_action' value='" . $this->_cfg_cpm_page_action . "'>";
        $form .= "<input type='hidden' name='cpm_payment_config' value='" . $this->_cfg_cpm_payment_config . "'>";
        $form .= "<input type='hidden' name='cpm_version' value='" . $this->_cfg_cpm_version . "'>";
        $form .= "<input type='hidden' name='cpm_language' value='" . $this->_cfg_cpm_language . "'>";
        $form .= "<input type='hidden' name='cpm_trans_date' value='" . $this->_cfg_cpm_trans_date . "'>";
        $form .= "<input type='hidden' name='cpm_trans_id' value='" . $this->_cfg_cpm_trans_id . "'>";
        $form .= "<input type='hidden' name='cpm_designation' value='" . $this->_cfg_cpm_designation . "'>";
        $form .= "<input type='hidden' name='cpm_amount' value='" . $this->_cfg_cpm_amount . "'>";
        $form .= "<input type='hidden' name='signature' value='" . $this->_signature . "'>";

        if (!empty($this->_cfg_cpm_custom))
            $form .= "<input type='hidden' name='cpm_custom' value='" . $this->_cfg_cpm_custom . "'>";
        if (!empty($this->_cfg_notify_url))
            $form .= "<input type='hidden' name='notify_url' value='" . $this->_cfg_notify_url . "'>";
        if (!empty($this->_cfg_return_url))
            $form .= "<input type='hidden' name='return_url' value='" . $this->_cfg_return_url . "'>";
        if (!empty($this->_cfg_cancel_url))
            $form .= "<input type='hidden' name='cancel_url' value='" . $this->_cfg_cancel_url . "'>";
        if ($this->_debug == true)
            $form .= "<input type='hidden' name='debug' value='1'>";

        $form .= $this->getOnlyPayButtonToSubmit($formName, $btnType, $size);
        $form .= "</form>";
        return $form;
    }

    /**
     * get CinetPay unique signature for your basket
     * @return mixed|null
     * @throws Exception
     */
    public function getSignature()
    {
        $data = (array)$this->getPaySignatureArray();
        $flux_json = $this->callCinetpayWsMethod($data, $this->_signatureUri);
        if ($flux_json === false)
            throw new Exception("Un probleme est survenu lors de l'appel du WS !");

        $this->_signature = json_decode($flux_json, true);
        if (is_array($this->_signature)) {
            if (!isset($this->_signature['status']))
                $message = 'La plateforme CINETPAY est temporairement indisponible.';
            else
                $message = 'Une erreur est survenue, Code: ' . $this->_signature['status']['code'] . ', Message: ' . $this->_signature['status']['message'];

            throw new Exception($message);
        }
        return $this->_signature;
    }

    private function getPaySignatureArray()
    {
        $dataArray = array(
            'apikey' => $this->_cfg_apikey,
            'cpm_site_id' => $this->_cfg_cpm_site_id,
            'cpm_currency' => $this->_cfg_cpm_currency,
            'cpm_payment_config' => $this->_cfg_cpm_payment_config,
            'cpm_page_action' => $this->_cfg_cpm_page_action,
            'cpm_version' => $this->_cfg_cpm_version,
            'cpm_language' => $this->_cfg_cpm_language,
            'cpm_trans_date' => $this->_cfg_cpm_trans_date,
            'cpm_trans_id' => $this->_cfg_cpm_trans_id,
            'cpm_designation' => $this->_cfg_cpm_designation,
            'cpm_amount' => $this->_cfg_cpm_amount
        );

        if (!empty($this->_cfg_cpm_custom)) $dataArray['cpm_custom'] = $this->_cfg_cpm_custom;
        return $dataArray;
    }

    private function callCinetpayWsMethod($params, $url, $method = 'POST')
    {
        if (function_exists('curl_version')) {
            try {
                $curl = curl_init();
                if ($method == 'POST') {
                    $postfield = '';
                    foreach ($params as $index => $value) {
                        $postfield .= $index . '=' . $value . "&";
                    }
                    $postfield = substr($postfield, 0, -1);
                } else {
                    $postfield = null;
                }
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 45,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => $method,
                    CURLOPT_POSTFIELDS => $postfield,
                    CURLOPT_HTTPHEADER => array(
                        "cache-control: no-cache",
                        "content-type: application/x-www-form-urlencoded",
                    ),
                ));
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                if ($err) {
                    throw new Exception("Error :" . $err);
                } else {
                    return $response;
                }
            } catch (Exception $e) {
                throw new Exception($e);
            }
        } else if (ini_get('allow_url_fopen')) {
            try {
                // Build Http query using params
                $query = http_build_query($params);
                // Create Http context details
                $options = array(
                    'http' => array(
                        'header' => "Content-Type: application/x-www-form-urlencoded\r\n" .
                            "Content-Length: " . strlen($query) . "\r\n" .
                            "User-Agent:MyAgent/1.0\r\n",
                        'method' => "POST",
                        'content' => $query,
                    ),
                );
                // Create context resource for our request
                $context = stream_context_create($options);
                // Read page rendered as result of your POST request
                $result = file_get_contents(
                    $url, // page url
                    false, $context);
                return trim($result);
            } catch (Exception $e) {
                throw new Exception($e);
            }
        } else {
            throw new Exception("Vous devez activer curl ou allow_url_fopen pour utiliser CinetPay");
        }
    }

    /**
     * get only button of cinetpay pay form
     * @param $formName
     * @param int $btnType
     * @param $size
     * @return string
     */
    public function getOnlyPayButtonToSubmit($formName, $btnType = 1, $size)
    {
        $size = ($size == 'small') ? 'small' : (($size == 'larger') ? 'larger' : 'large');

        if (!empty($formName) && $btnType == 1)
            $btn = "<button class='cinetpay-button " . $size . "' onclick='javascript:document.forms['" . $formName . "'].submit()'> Acheter </button>";
        elseif (!empty($formName) && $btnType == 2)
            $btn = "<button class='cinetpay-button " . $size . "' onclick='javascript:document.forms['" . $formName . "'].submit()'> Payer </button>";
        elseif (!empty($formName) && $btnType == 3)
            $btn = "<button class='cinetpay-button " . $size . "' onclick='javascript:document.forms['" . $formName . "'].submit()'> Faire un don </button>";
        elseif (!empty($formName) && $btnType == 4)
            $btn = "<button class='cinetpay-button " . $size . "' onclick='javascript:document.forms['" . $formName . "'].submit()'> Payer avec CinetPay</button>";
        else
            $btn = "<button class='cinetpay-button " . $size . "' onclick='javascript:document.forms['" . $formName . "'].submit()'> Payer maintenant </button>";
        return $btn;
    }

    /**
     * @throws Exception
     */
    public function submitCinetPayForm()
    {
        $this->getSignature();
        if (empty($this->_cfg_apikey))
            throw new Exception("Erreur: ApiKey non definie");
        if (empty($this->_cashDeskUri))
            throw new Exception("Erreur: Url de paiement non definie");
        if (empty($this->_cfg_cpm_site_id))
            throw new Exception("Erreur: Site ID non definie");
        if (empty($this->_cfg_cpm_currency))
            throw new Exception("Erreur: Devise non definie");
        if (empty($this->_cfg_cpm_page_action))
            throw new Exception("Erreur: Page action non definie");
        if (empty($this->_cfg_cpm_payment_config))
            throw new Exception("Erreur: Payment config non definie");
        if (empty($this->_cfg_cpm_version))
            throw new Exception("Erreur: Version non definie");
        if (empty($this->_cfg_cpm_language))
            throw new Exception("Erreur: Langue non definie");
        if (empty($this->_cfg_cpm_trans_date))
            throw new Exception("Erreur: Date de la transaction non definie");
        if (empty($this->_cfg_cpm_trans_id))
            throw new Exception("Erreur: ID de la transaction non definie");
        if (empty($this->_cfg_cpm_designation))
            throw new Exception("Erreur: Designation de la transaction non definie");
        if (empty($this->_cfg_cpm_amount))
            throw new Exception("Erreur: Montant de la transaction non definie");
        if (empty($this->_signature))
            throw new Exception("Erreur: Signature de la transaction non trouvee");

        $form = "<form id='form_paiement_cinetpay' name='form_paiement_cinetpay' action='" . $this->_cashDeskUri . "' method='post'>";
        $form .= "<input type='hidden' name='apikey' value='" . $this->_cfg_apikey . "'>";
        $form .= "<input type='hidden' name='cpm_site_id' value='" . $this->_cfg_cpm_site_id . "'>";
        $form .= "<input type='hidden' name='cpm_currency' value='" . $this->_cfg_cpm_currency . "'>";
        $form .= "<input type='hidden' name='cpm_page_action' value='" . $this->_cfg_cpm_page_action . "'>";
        $form .= "<input type='hidden' name='cpm_payment_config' value='" . $this->_cfg_cpm_payment_config . "'>";
        $form .= "<input type='hidden' name='cpm_version' value='" . $this->_cfg_cpm_version . "'>";
        $form .= "<input type='hidden' name='cpm_language' value='" . $this->_cfg_cpm_language . "'>";
        $form .= "<input type='hidden' name='cpm_trans_date' value='" . $this->_cfg_cpm_trans_date . "'>";
        $form .= "<input type='hidden' name='cpm_trans_id' value='" . $this->_cfg_cpm_trans_id . "'>";
        $form .= "<input type='hidden' name='cpm_designation' value='" . $this->_cfg_cpm_designation . "'>";
        $form .= "<input type='hidden' name='cpm_amount' value='" . $this->_cfg_cpm_amount . "'>";
        $form .= "<input type='hidden' name='signature' value='" . $this->_signature . "'>";

        if (!empty($this->_cfg_cpm_custom))
            $form .= "<input type='hidden' name='cpm_custom' value='" . $this->_cfg_cpm_custom . "'>";
        if (!empty($this->_cfg_notify_url))
            $form .= "<input type='hidden' name='notify_url' value='" . $this->_cfg_notify_url . "'>";
        if (!empty($this->_cfg_return_url))
            $form .= "<input type='hidden' name='return_url' value='" . $this->_cfg_return_url . "'>";
        if (!empty($this->_cfg_cancel_url))
            $form .= "<input type='hidden' name='cancel_url' value='" . $this->_cfg_cancel_url . "'>";

        $form .= '<script type="text/javascript">document.forms["form_paiement_cinetpay"].submit();</script>';//We submit data here

        print $form;
    }

    public function isAuthentified()
    {

        if ($this->getPayStatus()) {
            $dataArray = array(
                'apikey' => $this->_cfg_apikey,
                'cpm_site_id' => $this->_cfg_cpm_site_id,
                'cpm_currency' => $this->_cfg_cpm_currency,
                'cpm_payment_config' => $this->_cfg_cpm_payment_config,
                'cpm_page_action' => $this->_cfg_cpm_page_action,
                'cpm_version' => $this->_cfg_cpm_version,
                'cpm_language' => $this->_cfg_cpm_language,
                'cpm_trans_date' => $this->_cpm_trans_date,
                'cpm_trans_id' => $this->_cpm_trans_id,
                'cpm_designation' => $this->_cpm_designation,
                'cpm_amount' => $this->_cpm_amount
            );

            if (!empty($this->_cpm_custom)) $dataArray['cpm_custom'] = $this->_cpm_custom;

            $flux_json = $this->callCinetpayWsMethod($dataArray, $this->_signatureUri);
            if ($flux_json === false)
                throw new Exception("Un probleme est survenu lors de l'appel du WS !");

            $signature = json_decode($flux_json, true);
            if (is_array($signature)) {
                if (!isset($signature['status']))
                    $message = 'La plateforme CINETPAY est temporairement indisponible.';
                else
                    $message = 'Une erreur est survenue, Code: ' . $signature['status']['code'] . ', Message: ' . $signature['status']['message'];

                throw new Exception($message);
            }

            return ($signature === $this->_signature) ? true : false;
        } else {
            return false;
        }
    }

    /**
     * get proper value for your payment's variables
     * @return bool
     * @throws Exception
     */
    public function getPayStatus()
    {

        $data = (array)$this->getPayStatusArray();

        $flux_json = $this->callCinetpayWsMethod($data, $this->_checkPayStatusUri);
        if ($flux_json === false)
            throw new Exception("Un probleme est survenu lors de l'appel du WS !");

        $decodeText = html_entity_decode($flux_json);
        $array_flux_json = json_decode($decodeText, true);

        $this->_cpm_site_id = $array_flux_json['transaction']['cpm_site_id'];
        $this->_signature = $array_flux_json['transaction']['signature'];
        $this->_cpm_amount = $array_flux_json['transaction']['cpm_amount'];
        $this->_cpm_trans_date = $array_flux_json['transaction']['cpm_trans_date'];
        $this->_cpm_trans_id = $array_flux_json['transaction']['cpm_trans_id'];
        $this->_cpm_custom = $array_flux_json['transaction']['cpm_custom'];
        $this->_cpm_currency = $array_flux_json['transaction']['cpm_currency'];
        $this->_cpm_payid = $array_flux_json['transaction']['cpm_payid'];
        $this->_cpm_payment_date = $array_flux_json['transaction']['cpm_payment_date'];
        $this->_cpm_payment_time = $array_flux_json['transaction']['cpm_payment_time'];
        $this->_cpm_error_message = $array_flux_json['transaction']['cpm_error_message'];
        $this->_payment_method = $array_flux_json['transaction']['payment_method'];
        $this->_cpm_phone_prefixe = $array_flux_json['transaction']['cpm_phone_prefixe'];
        $this->_cel_phone_num = $array_flux_json['transaction']['cel_phone_num'];
        $this->_cpm_ipn_ack = $array_flux_json['transaction']['cpm_ipn_ack'];
        $this->_created_at = $array_flux_json['transaction']['created_at'];
        $this->_updated_at = $array_flux_json['transaction']['updated_at'];
        $this->_cpm_result = $array_flux_json['transaction']['cpm_result'];
        $this->_cpm_trans_status = $array_flux_json['transaction']['cpm_trans_status'];
        $this->_cpm_designation = $array_flux_json['transaction']['cpm_designation'];
        $this->_buyer_name = $array_flux_json['transaction']['buyer_name'];

        if ($this->_cpm_site_id != $this->_cfg_cpm_site_id)
            throw new Exception("Desol&eacute;, aucune donn&eacute;e trouv&eacute;e !");

        return true;
    }

    private function getPayStatusArray()
    {
        return $dataArray = array(
            'apikey' => $this->_cfg_apikey,
            'cpm_site_id' => $this->_cfg_cpm_site_id,
            'cpm_trans_id' => $this->_cfg_cpm_trans_id);
    }

    /**
     * @return array
     */
    public function getPayDataArray()
    {
        $dataArray = array(
            'apikey' => $this->_cfg_apikey,
            'cpm_site_id' => $this->_cfg_cpm_site_id,
            'cpm_currency' => $this->_cfg_cpm_currency,
            'cpm_payment_config' => $this->_cfg_cpm_payment_config,
            'cpm_page_action' => $this->_cfg_cpm_page_action,
            'cpm_version' => $this->_cfg_cpm_version,
            'cpm_language' => $this->_cfg_cpm_language,
            'cpm_trans_date' => $this->_cfg_cpm_trans_date,
            'cpm_trans_id' => $this->_cfg_cpm_trans_id,
            'cpm_designation' => $this->_cfg_cpm_designation,
            'cpm_amount' => $this->_cfg_cpm_amount,
            'cpm_custom' => $this->_cfg_cpm_custom,
            'notify_url' => $this->_cfg_notify_url,
            'return_url' => $this->_cfg_return_url,
            'cancel_url' => $this->_cfg_cancel_url
        );
        if (!empty($this->_cfg_cpm_custom)) $dataArray['cpm_custom'] = $this->_cfg_cpm_custom;
        return $dataArray;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setTransId($id)
    {
        $this->_cfg_cpm_trans_id = $id;
        return $this;
    }

    /**
     * @param string $cfg_cpm_version
     * @return $this
     */
    public function setVersion($cfg_cpm_version)
    {
        $this->_cfg_cpm_version = $cfg_cpm_version;
        return $this;
    }

    /**
     * @param $notify_url
     * @return $this
     */
    public function setNotifyUrl($notify_url)
    {
        $this->_cfg_notify_url = $notify_url;
        return $this;
    }

    /**
     * @param $return_url
     * @return $this
     */
    public function setReturnUrl($return_url)
    {
        $this->_cfg_return_url = $return_url;
        return $this;
    }

    /**
     * @param $cancel_url
     * @return $this
     */
    public function setCancelUrl($cancel_url)
    {
        $this->_cfg_cancel_url = $cancel_url;
        return $this;
    }

    /**
     * @param $designation
     * @return $this
     */
    public function setDesignation($designation)
    {
        $this->_cfg_cpm_designation = $designation;
        return $this;
    }

    /**
     * @param $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->_cfg_cpm_amount = $amount;
        return $this;
    }

    /**
     * @param $custom
     * @return $this
     */
    public function setCustom($custom)
    {
        $this->_cfg_cpm_custom = $custom;
        return $this;
    }

    /**
     * @param string $debug
     * @return $this
     */
    public function setDebug($debug)
    {
        $this->_debug = $debug;
        return $this;
    }

    /**
     * @param $date
     * @return $this
     * @throws Exception
     */
    public function setTransDate($date)
    {
        if ($this->IsDate($date)) {
            $date = new DateTime($date);
            $this->_cfg_cpm_trans_date = $date->format('YmdHis');
            return $this;
        }
        throw new Exception("Method [setTransDate] need a good Date");
    }

    /**
     * @param $date
     * @param string $format
     * @return bool
     */
    private function IsDate($date, $format = 'Y-m-d H:i:s')
    {
        $version = explode('.', phpversion());
        if (((int)$version[0] >= 5 && (int)$version[1] >= 2 && (int)$version[2] > 17)) {
            $d = DateTime::createFromFormat($format, $date);
        } else {
            $d = new DateTime(date($format, strtotime($date)));
        }
        return $d && $d->format($format) == $date;
    }
}