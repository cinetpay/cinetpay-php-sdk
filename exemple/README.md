# Exemple Simple d'intégration CinetPay

On suppose que le nom de domaine du site est : www.mondomaine.ci

Assurez vous toujours que la classe CinetPay est bien chargée

## Etape 1 : Préparation des pages de notification, de retour et d'annulation

### Page de Notification
Dans un fichier notify.php à la racine de votre site par exemple
```php
<?php
// Verification que des données sont envoyées par CinetPay
if (isset($_POST['cpm_trans_id'])) {
    // SDK PHP de CinetPay 
    require_once __DIR__ . '/../src/cinetpay.php';
    try {
        // Initialisation de CinetPay et Identification du paiement
        $id_transaction = $_POST['cpm_trans_id'];
        //Veuillez entrer votre apiKey et site ID
        $apiKey = "21585943f75164bbc2.38014639";
        $site_id = "296911";
        $plateform = "PROD";
        $version = "V1";
        $CinetPay = new CinetPay($site_id, $apiKey, $plateform, $version);
        //Prise des données chez CinetPay correspondant à ce paiement
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

       if($cpm_result == '00'){
            //Le paiement est bon
            // Verifier que le montant correspond à la transaction dans votre système
            // Traitez dans la base de donnée et delivrez le service au client
       }else{
             //Le paiement a échoué
       }
    } catch (Exception $e) {
        echo "Erreur :" . $e->getMessage();
        // Une erreur s'est produite
    }
} else {
    // Tentative d'accès direct au lien IPN
}
?>
```
### Page de Retour
Dans un fichier return.php à la racine de votre site par exemple
```php
<?php
if (isset($_POST['cpm_trans_id'])) {
    // SDK PHP de CinetPay 
     require_once __DIR__ . '/../src/cinetpay.php';

    try {
        // Initialisation de CinetPay et Identification du paiement
        $id_transaction = $_POST['cpm_trans_id'];
        //Veuillez entrer votre apiKey et site ID
        $apiKey = "21585943f75164bbc2.38014639";
        $site_id = "296911";
        $plateform = "PROD";
        $version = "V1";
        $CinetPay = new CinetPay($site_id, $apiKey, $plateform, $version);
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
        // Aucun enregistrement dans la base de donnée ici
        if($cpm_result == '00'){
            // une page HTML de paiement bon
             echo 'Felicitation, votre paiement a été effectué avec succès';
             die();
        }else{
            // une page HTML de paiement echoué
              echo 'Echec, votre paiement a échoué';
              die();
        }
    } catch (Exception $e) {
        echo "Erreur :" . $e->getMessage();
        // Une erreur s'est produite
    }
} else {
   // redirection vers la page d'accueil
   header('Location: /');
   die();
}
?>
```

### Page d'Annulation
Dans un fichier cancel.php à la racine de votre site par exemple
```php
<?php
    // une page HTML d'Annulation ou une redirection vers la page d'accueil
    echo 'Vous avez annulé votre paiement';
?>
```

## Etape 2 : Préparation et affichage du formulaire de paiement
A inserer dans le script de paiement :
```php
<?php
        require_once __DIR__ . '/../src/cinetpay.php';
        try {
            $id_transaction = date("YmdHis"); // ou $id_transaction = Cinetpay::generateTransId()
            $description_du_paiement = "Mon produit de ref: $id_transaction";
            $date_transaction = date("Y-m-d H:i:s");
            // Montant minimun est de 5 francs sur CinetPay
            $montant_a_payer = mt_rand(5, 100);
            
            // Mettez ici une information qui vous permettra d'identifier de façon unique le payeur
            $identifiant_du_payeur = 'payeur@domaine.ci';
            
            //Veuillez entrer votre apiKey
            $apiKey = "21585943f75164bbc2.38014639";
            //Veuillez entrer votre siteId
            $site_id = "296911";
            
            //platform ,  utiliser PROD si vous avez créé votre compte sur www.cinetpay.com  ou TEST si vous avez créé votre compte sur www.sandbox.cinetpay.com
            $plateform = "PROD";
            
            //la version ,  utilisé V1 si vous voulez utiliser la version 1 de l'api
            $version = "V1";
    
            // nom du formulaire CinetPay
            $formName = "goCinetPay";
            // Les liens CinetPay
            $notify_url = 'http://mondomaine.ci/notify.php';
            $return_url = 'http://mondomaine.ci/return.php';
            $cancel_url = 'http://mondomaine.ci/cancel.php';
            // Configuration du bouton
            $btnType = 2;//1-5
            $btnSize = 'large'; // 'small' pour reduire la taille du bouton, 'large' pour une taille moyenne ou 'larger' pour  une taille plus grande 
    
            // Enregistrement de la commande dans notre BD
            //$commande->setTransId($id_transaction);
            //$commande->setMontant($montant_a_payer);
            //$commande->create();
            
            // Paramétrage du panier CinetPay et affichage du formulaire
            $CinetPay = new CinetPay($site_id, $apiKey, $plateform, $version);
            $CinetPay->setTransId($id_transaction)
                    ->setDesignation($description_du_paiement)
                    ->setTransDate($date_transaction)
                    ->setAmount($montant_a_payer)
                    ->setDebug(false) // Valorisé à true, si vous voulez activer le mode debug sur cinetpay afin d'afficher toutes les variables envoyées chez CinetPay
                    ->setCustom($identifiant_du_payeur)// optional
                    ->setNotifyUrl($notify_url)// optional
                    ->setReturnUrl($return_url)// optional
                    ->setCancelUrl($cancel_url)// optional
                    ->displayPayButton($formName, $btnType, $btnSize);
        } catch (Exception $e) {
            // Une erreur est survenue
            echo $e->getMessage();
        }
?>
```
# Exemple Intégration Production

Vous trouverez un exemple d'intégration en production dans :
*    index.php : script de paiement
*    notify/index.php : script de notification
*    le script MySQL que vous devriez adapter à votre projet
