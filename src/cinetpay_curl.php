<?php
/**
 * CinetPay
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@cinetpay.com so we can send you a copy immediately.
 *
 * @category   CinetPay
 * @package    cinetpay
 * @version    1.0.6
 */

/**
 * Class CinetPay using curl
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
    const URI_GET_SIGNATURE_PROD = 'api.cinetpay.com/v1/?method=getSignatureByPost';
    const URI_GET_SIGNATURE_DEV = 'api.sandbox.cinetpay.com/v2/?method=getSignatureByPost';
    const URI_CHECK_PAY_STATUS_PROD = 'api.cinetpay.com/v1/?method=checkPayStatus';
    const URI_CHECK_PAY_STATUS_DEV = 'api.sandbox.cinetpay.com/v2/?method=checkPayStatus';
    /**
     * An indentifier
     * @var string
     */
    public $_cfg_cpm_page_action = "PAYMENT";
    /**
     * An indentifier
     * @var string
     */
    public $_cfg_cpm_payment_config = "SINGLE";
    /**
     * An indentifier
     * @var string
     */
    public $_cfg_cpm_version = "V2";
    /**
     * An indentifier
     * @var string
     */
    public $_cfg_cpm_language = "fr";
    /**
     * An indentifier
     * @var string
     */
    public $_cfg_cpm_currency = "CFA";
    /**
     * An indentifier
     * @var string
     */
    public $_cfg_cpm_trans_date = null;
    /**
     * An indentifier
     * @var string
     */
    public $_cfg_cpm_trans_id = null;
    /**
     * An indentifier
     * @var string
     */
    public $_cfg_cpm_designation = null;
    /**
     * An indentifier
     * @var string
     */
    public $_cfg_cpm_custom = null;
    /**
     * An indentifier
     * @var string
     */
    public $_cfg_cpm_amount = null;
    /**
     * An indentifier
     * @var string
     */
    public $_cfg_cpm_site_id = null;
    /**
     * An indentifier
     * @var string
     */
    public $_cfg_notify_url = null;
    /**
     * An indentifier
     * @var string
     */
    public $_cfg_return_url = null;
    /**
     * An indentifier
     * @var string
     */
    public $_cfg_cancel_url = null;
    /**
     * An indentifier
     * @var string
     */
    public $_cashDeskUri = null;
    public $_signature = null;
    public $_cpm_site_id = null;
    public $_cpm_amount = null;
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
     * An indentifier
     * @var string
     */
    protected $_cfg_apikey = null;
    /**
     * An indentifier
     * @var string
     */
    protected $_signatureUri = null;
    /**
     * An indentifier
     * @var string
     */
    protected $_checkPayStatusUri = null;
    /**
     * An indentifier
     * @var string
     */
    protected $_webSiteUri = null;

    public function CinetPay($site_id, $apikey, $mode = "PROD")
    {

        if ($mode == "PROD") {
            $this->_use_sandbox = false;
            $this->_use_ssl = true;
        } else {
            $this->_use_sandbox = true;
        }

        $this->_cfg_cpm_site_id = $site_id;
        $this->_cfg_apikey = $apikey;
        $htpp_prefixe = ($this->_use_ssl) ? 'https://' : 'http://';
        $this->_cashDeskUri = $htpp_prefixe . $this->getCashDeskHost();
        $this->_signatureUri = $htpp_prefixe . $this->getSignatureHost();
        $this->_checkPayStatusUri = $htpp_prefixe . $this->getCheckPayStatusHost();
        $this->_webSiteUri = $htpp_prefixe . $this->getWebSiteHost();
    }

    private function getCashDeskHost()
    {
        if ($this->_use_sandbox)
            return self::URI_CASH_DESK_DEV;
        else
            return self::URI_CASH_DESK_PROD;
    }

    private function getSignatureHost()
    {
        if ($this->_use_sandbox)
            return self::URI_GET_SIGNATURE_DEV;
        else
            return self::URI_GET_SIGNATURE_PROD;
    }

    private function getCheckPayStatusHost()
    {
        if ($this->_use_sandbox)
            return self::URI_CHECK_PAY_STATUS_DEV;
        else
            return self::URI_CHECK_PAY_STATUS_PROD;
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

    public function displayPayButton($formName, $btnType = 1, $btnWidth = "120px", $btnHeight = "")
    {
        print $this->getPayButton($formName, $btnType, $btnWidth, $btnHeight);
    }

    public function getPayButton($formName, $btnType = 1, $btnWidth = "120px", $btnHeight = "")
    {

        $signature = $this->getSignature();

        if (empty($this->_cfg_apikey))
            throw new Exception("ApiKey non definie");
        if (empty($this->_cashDeskUri))
            throw new Exception("Url de paiement non definie");
        if (empty($this->_cfg_cpm_site_id))
            throw new Exception("Site ID non definie");
        if (empty($this->_cfg_cpm_currency))
            throw new Exception("Devise non definie");
        if (empty($this->_cfg_cpm_page_action))
            throw new Exception("Page action non definie");
        if (empty($this->_cfg_cpm_payment_config))
            throw new Exception("Payment config non definie");
        if (empty($this->_cfg_cpm_version))
            throw new Exception("Version non definie");
        if (empty($this->_cfg_cpm_language))
            throw new Exception("Langue non definie");
        if (empty($this->_cfg_cpm_trans_date))
            throw new Exception("Date de la transaction non definie");
        if (empty($this->_cfg_cpm_trans_id))
            throw new Exception("ID de la transaction non definie");
        if (empty($this->_cfg_cpm_designation))
            throw new Exception("Designation de la transaction non definie");
        if (empty($this->_cfg_cpm_amount))
            throw new Exception("Montant de la transaction non definie");
        if (empty($this->_signature))
            throw new Exception("Signature de la transaction non trouvee");
        if (empty($formName))
            throw new Exception("Nom du formulaire non definie");

        $form = "<div style='display: none;'><form id='" . $formName . "' name='" . $formName . "' action='" . $this->_cashDeskUri . "' method='post'>";
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
        $form .= '</div>';
        $form .= $this->getOnlyPayButtonToSubmit($formName, $btnType, $btnWidth, $btnHeight);

        return $form;
    }

    public function getSignature()
    {

        $data = array();
        $data = $this->getPaySignatureArray();
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
        try {
            $curl = curl_init();

            settype($params, 'array');

            $data = (count($params) > 0) ? $params : false;

            $httpHeader = array();

            switch ($method) {
                case 'POST':
                    curl_setopt($curl, CURLOPT_POST, 1);
                    if ($data) {
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                        $httpHeader[] = 'Content-Length: ' . strlen($data);
                    }
                    break;
                case 'GET':
                    break;
                case 'PUT':
                    curl_setopt($curl, CURLOPT_PUT, 1);
                    break;
                default:
            }
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $httpHeader);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($curl);
            print_r(curl_error($curl));
            curl_close($curl);

            return $result;

        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

    public function getOnlyPayButtonToSubmit($formName, $btnType = 1, $btnWidth = "120px", $btnHeight = "")
    {
        $w = (!empty($btnWidth)) ? "width=$btnWidth" : "";
        $h = (!empty($btnHeight)) ? "height=$btnHeight" : "";
        $btnType = (int)$btnType;

        if (!empty($formName) && $btnType == 1)
            $btn = "<div style='display: none;'><input $w $h src='" . $this->_webSiteUri . "/btn/fr_FR/acheter.png' onclick='document.forms['" . $formName . "'].submit()' title=\"Effectuer des paiement avec CinetPay - C'est rapide, gratuit et s&eacute;curis&eacute;!\" name='submit' type='image' /></div>";
        elseif (!empty($formName) && $btnType == 2)
            $btn = "<div style='display: none;'><input $w $h src='" . $this->_webSiteUri . "/btn/fr_FR/payer.png' onclick='document.forms['" . $formName . "'].submit()' title=\"Effectuer des paiement avec CinetPay - C'est rapide, gratuit et s&eacute;curis&eacute;!\" name='submit' type='image' /></div>";
        elseif (!empty($formName) && $btnType == 3)
            $btn = "<div style='display: none;'><input $w $h src='" . $this->_webSiteUri . "/btn/fr_FR/faire-don.png' onclick='document.forms['" . $formName . "'].submit()' title=\"Effectuer des paiement avec CinetPay - C'est rapide, gratuit et s&eacute;curis&eacute;!\" name='submit' type='imag</div>e' />";
        elseif (!empty($formName) && $btnType == 4)
            $btn = "<div style='display: none;'><input $w $h src='" . $this->_webSiteUri . "/btn/fr_FR/faire-un-don.png' onclick='document.forms['" . $formName . "'].submit()' title=\"Effectuer des paiement avec CinetPay - C'est rapide, gratuit et s&eacute;curis&eacute;!\" name='submit' type='ima</div>ge' />";
        elseif (!empty($formName) && $btnType == 5)
            $btn = "<div style='display: none;'><input $w $h src='" . $this->_webSiteUri . "/btn/fr_FR/payer-avec-cinetpay.png' onclick='document.forms['" . $formName . "'].submit()' title=\"Effectuer des paiement avec CinetPay - C'est rapide, gratuit et s&eacute;curis&eacute;!\" name='submit' type='ima</div>ge' />";
        else
            $btn = "<div style='display: none;'><input $w $h src='" . $this->_webSiteUri . "/btn/fr_FR/acheter.png' onclick='document.forms['" . $formName . "'].submit()' title=\"Effectuer des paiement avec CinetPay - C'est rapide, gratuit et s&eacute;curis&eacute;!\" name='submit' type='image' /></div>";

        return $btn;
    }

    public function submitCinetPayForm()
    {

        $signature = $this->getSignature();

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
            $dataArray = array();
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
        }
    }

    public function getPayStatus()
    {

        $data = array();
        $data = $this->getPayStatusArray();

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

    public function setTransId($id)
    {
        $this->_cfg_cpm_trans_id = $id;
        return $this;
    }

    public function setNotifyUrl($notify_url)
    {
        $this->_cfg_notify_url = $notify_url;
        return $this;
    }

    public function setReturnUrl($return_url)
    {
        $this->_cfg_return_url = $return_url;
        return $this;
    }

    public function setCancelUrl($cancel_url)
    {
        $this->_cfg_cancel_url = $cancel_url;
        return $this;
    }

    public function setDesignation($designation)
    {
        $this->_cfg_cpm_designation = $designation;
        return $this;
    }

    public function setAmount($amount)
    {
        $this->_cfg_cpm_amount = $amount;
        return $this;
    }

    public function setCustom($custom)
    {
        $this->_cfg_cpm_custom = $custom;
        return $this;
    }

    public function setTransDate($date)
    {
        if ($this->IsDate($date)) {
            $date = new DateTime($date);
            $this->_cfg_cpm_trans_date = $date->format('YmdHis');
            return $this;
            /*$date = date_create('2000-01-01');
              echo date_format($date, 'YmdHis');*/
        }
        throw new Exception("Method [setTransDate] need a good Date");
    }

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
     * Return the HTML form to send to the payment gateway.
     *
     * @param string $form_add
     * @param string $input_type
     * @param string $input_add
     * @param string $btn_type
     * @param string $btn_value
     * @param string $btn_add
     * @return string
     */
    public function getRequestHtmlForm($form_add = '', $input_type = 'hidden', $input_add = '', $btn_type = 'submit', $btn_value = 'Pay', $btn_add = '')
    {
        $html = '';
        $html .= '<form action="' . $this->platformUrl . '" method="POST" ' . $form_add . '>';
        $html .= "\n";
        $html .= $this->getRequestHtmlFields($input_type, $input_add);
        $html .= '<input type="' . $btn_type . '" value="' . $btn_value . '" ' . $btn_add . '/>';
        $html .= "\n";
        $html .= '</form>';

        return $html;
    }

    /**
     * Return the HTML inputs of fields to send to the payment page.
     *
     * @param string $input_type
     * @param string $input_add
     * @return string
     */
    public function getRequestHtmlFields($input_type = 'hidden', $input_add = '')
    {
        $fields = $this->getRequestFields();

        $html = '';
        $format = '<input name="%s" value="%s" type="' . $input_type . '" ' . $input_add . "/>\n";
        foreach ($fields as $field) {
            if (!$field->isFilled())
                continue;

            // convert special chars to HTML entities to avoid data troncation
            $value = htmlspecialchars($field->getValue(), ENT_QUOTES, 'UTF-8');
            $html .= sprintf($format, $field->getName(), $value);
        }
        return $html;
    }
}