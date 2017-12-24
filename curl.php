<?php

require_once('/config/config.php');

echo $f;
  if( $curl = curl_init() ) {
    curl_setopt($curl, CURLOPT_URL, SERVER_URL_NAME . '/api.php');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl, CURLOPT_POST, true);
	
    $f = file_get_contents('/xml/unloading_c.xml', 'r');
	
    curl_setopt($curl, CURLOPT_POSTFIELDS, 'xml=' . $f);
    $out = curl_exec($curl);
    echo $out;
    curl_close($curl);
  }