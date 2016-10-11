<?php
require_once __DIR__ . '/../cinetpay_loader.php';

try {
    // generate transaction identifiant, you can also use Cinetpay::generateTransId()
    $id_transaction = date("YmdHis");

    // Payment description
    $description_du_paiement = "Mon produit de ref: $id_transaction";

    // Payment Date must be on date format
    $date_transaction = date("Y-m-d H:i:s");

    // Amount
    $montant_a_payer = rand(5, 10);

    // put a value that can you use to identify the buyer in your system
    $identifiant_du_payeur = "payeur@domaine.ci";

    // your notify url
    $notify_url = "";

    // your return url
    $return_url = "";

    // your cancel url
    $cancel_url = "";

    //put your cinetpay api key here
    $apiKey = "39955468c7a8c0cef1.68322505";

    //put your cinetpay site id here
    $site_id = "485179";

    //platform ,  use PROD if you created your account in www.cinetpay.com  or TEST if you created your account in www.sandbox.cinetpay.com
    $plateform = "TEST";

    // name of your cinetpay form
    $formName = "nom_formulaire_exemple";

    // cinetpay button type, must be 1, 2, 3, 4 or 5
    $btnType = 2;

    // button width
    $btnWidth = "120px";

    //button height
    $btnHeight = "";

    // create html form for your basket
    // you must save this information into your db before write this
    $CinetPay = new CinetPay($site_id, $apiKey, $plateform);
    $CinetPay->setTransId($id_transaction)
        ->setDesignation($description_du_paiement)
        ->setTransDate($date_transaction)
        ->setAmount($montant_a_payer)
        ->setCustom($identifiant_du_payeur)// optional
        ->setNotifyUrl($notify_url)// optional
        ->setReturnUrl($return_url)// optional
        ->setCancelUrl($cancel_url)// optional
        ->displayPayButton($formName, $btnType, $btnWidth, $btnHeight);
} catch (Exception $e) {
    throw new Exception($e);
}


