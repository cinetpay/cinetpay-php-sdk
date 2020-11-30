# CinetPay SDK PHP Integration

CinetPay SDK PHP Integration permet d'intégrer rapidement CinetPay à un site en ligne fait avec PHP.

L'intégration de ce SDK se fait en deux etapes :

## Etape 1 : Préparation des pages de notification, de retour et d'annulation

### Page de Notification
Pour ceux qui possèdent des services qui ne neccessitent pas un traitement des notifications de paiement de CinetPay, vous pouvez passer directement à la phase suivante, par exemple les services de don.

A chaque paiement, CinetPay vous notifie via un lien de notification.

NB :

    -C'est un lien silencieux
    -C'est le seul lien qui est abilité à mettre à jour les informations de la base de donnée relatif à la transaction

Exemple :
```php 
<?php
if (isset($_POST['cpm_trans_id'])) {
    // SDK PHP de CinetPay 
    require_once __DIR__ . '/cinetpay.php';
    require_once __DIR__ . '/commande.php';

    //La classe commande correspond à votre colonne qui gère les transactions dans votre base de données
    $commande = new Commande();
    try {
        // Initialisation de CinetPay et Identification du paiement
        $id_transaction = $_POST['cpm_trans_id'];
        $apiKey = _VOTRE_APIKEY_;
        $site_id = _VOTRE_SITEID_;
        $plateform = "TEST"; // Valorisé à PROD si vous êtes en production
        $version = "V2"; // Valorisé à V1 si vous voulez utiliser la version 1 de l'api
        $CinetPay = new CinetPay($site_id, $apiKey, $plateform, $version);
        // Reprise exacte des bonnes données chez CinetPay
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

        // Recuperation de la ligne de la transaction dans votre base de données
        $commande->setTransId($id_transaction);
        $commande->getCommandeByTransId();
        // Verification de l'etat du traitement de la commande
        if ($commande->getStatut() == '00') {
            // La commande a été déjà traité
            // Arret du script
            die();
        }
        // Dans le cas contrait, on remplit notre ligne des nouvelles données acquise en cas de tentative de paiement sur CinetPay
        $commande->setMethode($payment_method);
        $commande->setPayId($cpm_payid);
        $commande->setBuyerName($buyer_name);
        $commande->setSignature($signature);
        $commande->setPhone($cel_phone_num);
        $commande->setDatePaiement($cpm_payment_date . ' ' . $cpm_payment_time);

        // On verifie que le montant payé chez CinetPay correspond à notre montant en base de données pour cette transaction
        if ($commande->getMontant() == $cpm_amount) {
            // C'est OK : On continue le remplissage des nouvelles données
            $commande->setErrorMessage($cpm_error_message);
            $commande->setStatut($cpm_result);
            $commande->setTransStatus($cpm_trans_status);
            if($cpm_result == '00'){
                //Le paiement est bon
                // Traitez et delivrez le service au client
            }else{
                //Le paiement a échoué
            }
        } else {
            //Fraude : montant payé ' . $cpm_amount . ' ne correspond pas au montant de la commande
            $commande->setStatut('-1');
            $commande->setTransStatus('REFUSED');
        }
        // On met à jour notre ligne
        $commande->update();
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

La page de retour est la page où est redirigée le client après une transaction sur CinetPay (quelque soit le statut de la transaction). Aucune mise à jour de la base de données ne doit être traité sur cette page

Exemple de page de retour :
```php
<?php
if (isset($_POST['cpm_trans_id'])) {
    // SDK PHP de CinetPay 
    require_once __DIR__ . '/cinetpay.php';
    require_once __DIR__ . '/commande.php';

    //La classe commande correspond à votre colonne qui gère les transactions dans votre base de données
    $commande = new Commande();
    try {
        // Initialisation de CinetPay et Identification du paiement
        $id_transaction = $_POST['cpm_trans_id'];
        $apiKey = _VOTRE_APIKEY_;
        $site_id = _VOTRE_SITEID_;
        $plateform = "TEST"; // Valorisé à PROD si vous êtes en production
        $version = "V2"; // Valorisé à V1 si vous voulez utiliser la version 1 de l'api
        $CinetPay = new CinetPay($site_id, $apiKey, $plateform, $version);
        // Reprise exacte des bonnes données chez CinetPay
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

        // Recuperation de la ligne de la transaction dans votre base de données
        $commande->setTransId($id_transaction);
        $commande->getCommandeByTransId();
        // Verification de l'etat du traitement de la commande
        if ($commande->getStatut() == '00') {
            echo 'Felicitation, votre paiement a été effectué avec succès';
            die();
        }
        // On verifie que le montant payé chez CinetPay correspond à notre montant en base de données pour cette transaction
        if ($commande->getMontant() == $cpm_amount) {
            if($cpm_result == '00'){
                 echo 'Felicitation, votre paiement a été effectué avec succès';
                 die();
            }else{
                echo 'Echec, votre paiement a échoué';
                die();
            }
        } else {
             echo 'Echec, votre paiement a été validé mais les montants ne correspondent pas';
             die();
        }
    } catch (Exception $e) {
        echo "Erreur :" . $e->getMessage();
        // Une erreur s'est produite
    }
} else {
   // redirection vers la page d'accueil
    die();
}
?>
```

### Page d'Annulation

En depit de faire une page d'annulation, vous pouvez simplement utiliser votre page d'accueil

## Etape 2 : Préparation et affichage du formulaire de paiement

Le formulaire de paiement CinetPay est constitué de :

    transId : L'identifiant de la transaction
    designation :  La designation de la transaction
    transDate : La date du debut de la transaction
    amount : Le montant de la transaction
    version : La version à utiliser
    custom : La valeur qui vou permettra d'identifier de façon unique la personne effectuant la transaction dans votre système
    notifyUrl : Url silencieuse que CinetPay appel après chaque transaction, peut être appelé plusieurs fois
    returnUrl : Après une transaction, c'est le lien où est redirigé le client
    cancelUrl : Le lien appelé quand le client annule volontairement sa transaction
    
Pour plus de sécurité, Il faut enregistrer les informations sur le paiement dans la base de données avant d'afficher le formulaire.

Exemple :
```php
<?php
        // Inclusion des classes necessaires
        require_once __DIR__ . '/cinetpay.php';
        require_once __DIR__ . '/commande.php';
        $commande = new Commande();
        try {
            $id_transaction = date("YmdHis"); // ou $id_transaction = Cinetpay::generateTransId()
            $description_du_paiement = "Mon produit de ref: $id_transaction";
            $date_transaction = date("Y-m-d H:i:s");
            // Montant minimun est de 5 francs sur CinetPay
            $montant_a_payer = mt_rand(100, 200);
            // Devise
            $devise = 'XOF';
            // Mettez ici une information qui vous permettra d'identifier de façon unique le payeur
            $identifiant_du_payeur = 'payeur@domaine.ci';
            
            //Veuillez entrer votre apiKey
            $apiKey = "39955468c7a8c0cef1.68322505";
            //Veuillez entrer votre siteId
            $site_id = "124598";
            
            //platform ,  utiliser PROD si vous avez créé votre compte sur www.cinetpay.com  ou TEST si vous avez créé votre compte sur www.sandbox.cinetpay.com
            $plateform = "TEST";
            
            //la version ,  utilisé V1 si vous voulez utiliser la version 1 de l'api
            $version = "V2";
    
            // nom du formulaire CinetPay
            $formName = "goCinetPay";
            // Les liens CinetPay
            $notify_url = '';
            $return_url = '';
            $cancel_url = '';
            // Configuration du bouton
            $btnType = 2;//1-5
            $btnSize = 'large'; // 'small' pour reduire la taille du bouton, 'large' pour une taille moyenne ou 'larger' pour  une taille plus grande 
    
            // Enregistrement de la commande dans notre BD
            $commande->setTransId($id_transaction);
            $commande->setMontant($montant_a_payer);
            $commande->create();
            
            // Paramétrage du panier CinetPay et affichage du formulaire
            $CinetPay = new CinetPay($site_id, $apiKey, $plateform, $version);
            $CinetPay->setTransId($id_transaction)
                    ->setDesignation($description_du_paiement)
                    ->setTransDate($date_transaction)
                    ->setAmount($montant_a_payer)
                    ->setCurrency($devise)
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
# Votre Api Key et Site ID

Ces informations sont disponibles dans votre BackOffice CinetPay.

# Exemple Intégration

Vous trouverez un exemple d'intégration complet dans le dossier exemple/
