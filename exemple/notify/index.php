<?php
// IPN acces for payment validation

//check if there is a cinetpay post value
if (isset($_POST['cpm_trans_id'])) {
    // call required lib
    require_once __DIR__ . '/../../src/cinetpay.php';

    // sample class for simulate payment validation
    require_once __DIR__ . '/../commande.php';

    $commande = new Commande();
    try {
        // cinetpay class initialisation and transaction identify
        $id_transaction = $_POST['cpm_trans_id'];
        $apiKey = _VOTRE_APIKEY_;
        $site_id = _VOTRE_SITEID_;
        //platform ,  use PROD if you created your account in www.cinetpay.com
        //  or TEST if you created your account in www.sandbox.cinetpay.com
        $plateform = "TEST";

        //version ,  use V1 if you want to use api v1
        $version = "V2";

        $CinetPay = new CinetPay($site_id, $apiKey, $plateform, $version);
        // get correct values for this transactions
        $CinetPay->setTransId($id_transaction)->getPayStatus();
        $cpm_site_id = $CinetPay->_cpm_site_id;
        $signature = $CinetPay->_signature;
        $cpm_amount = $CinetPay->_cpm_amount;
        $cpm_trans_id = $CinetPay->_cpm_trans_id;
        $cpm_custom = $CinetPay->_cpm_custom;
        $cpm_currency = $CinetPay->_cpm_currency;
        $cpm_payid = $CinetPay->_cpm_payid;
        $cpm_payment_date = $CinetPay->_cpm_payment_date;
        $cpm_payment_time = $CinetPay->_cpm_payment_time;
        $cpm_error_message = $CinetPay->_cpm_error_message;
        $payment_method = $CinetPay->_payment_method;
        $cpm_phone_prefixe = $CinetPay->_cpm_phone_prefixe;
        $cel_phone_num = $CinetPay->_cel_phone_num;
        $cpm_ipn_ack = $CinetPay->_cpm_ipn_ack;
        $created_at = $CinetPay->_created_at;
        $updated_at = $CinetPay->_updated_at;
        $cpm_result = $CinetPay->_cpm_result;
        $cpm_trans_status = $CinetPay->_cpm_trans_status;
        $cpm_designation = $CinetPay->_cpm_designation;
        $buyer_name = $CinetPay->_buyer_name;

        // get actual transaction's status in your db
        $commande->setTransId($id_transaction);
        $commande->getCommandeByTransId();
        // check if transaction is already validated
        if ($commande->getStatut() == '00') {
            // transaction is already validated, don't do anything
            die();
        }
        // set news values in the class
        $commande->setMethode($payment_method);
        $commande->setPayId($cpm_payid);
        $commande->setBuyerName($buyer_name);
        $commande->setSignature($signature);
        $commande->setPhone($cel_phone_num);
        $commande->setDatePaiement($cpm_payment_date . ' ' . $cpm_payment_time);

        // check if amount of transaction correspond of the amount in our db
        if ($commande->getMontant() == $cpm_amount) {
            // correct, we continue
            $commande->setErrorMessage($cpm_error_message);
            $commande->setStatut($cpm_result);
            $commande->setTransStatus($cpm_trans_status);
            if ($cpm_result == '00') {
                // transaction is valid
                // send mail...
            } else {
                // transaction is not valid
            }
        } else {
            // Fraud : amount is not what expected
            $commande->setStatut('-1');
            $commande->setTransStatus('REFUSED');
        }
        // update transaction in our db
        $commande->update();
    } catch (Exception $e) {
        echo "Erreur :" . $e->getMessage();
    }
} else {
    // direct acces on IPN
}