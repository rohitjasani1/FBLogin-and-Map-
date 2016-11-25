<?php
include_once("inc/facebook.php"); //include facebook SDK
######### Facebook API Configuration ##########
$appId = '1754011964860062'; //Facebook App ID
$appSecret = 'd7bb482e091d1ccf22df0b7ccd45769f'; // Facebook App Secret
$homeurl = 'http://localhost/final/';  //return to home
$fbPermissions = 'email';  //Required facebook permissions

//Call Facebook API
$facebook = new Facebook(array(
  'appId'  => $appId,
  'secret' => $appSecret

));
$fbuser = $facebook->getUser();
?>