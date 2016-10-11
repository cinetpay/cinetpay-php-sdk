<?php

if (ini_get('allow_url_fopen')) require_once __DIR__ . '/src/cinetpay_nocurl.php';
else  require_once __DIR__ . '/src/cinetpay_curl.php';