<?php 
require 'library/U2FServer.php';

$U2F = new U2FServer;

$deviceReponse = json_decode($_POST['deviceResponse']);
$challenge = json_decode($_POST['challenge']);
$credential = json_decode($_POST['credential']);
$registrationRequestObj = new RegistrationRequest($challenge->challenge, $challenge->appId);
try {

    $validatedRegistration = $U2F::register(
        $registrationRequestObj,
        $deviceReponse
    );
    $resObj = new stdClass;
    $resObj->keyHandle = $validatedRegistration->getKeyHandle();
    $resObj->publicKey = $validatedRegistration->getPublicKey();
    $resObj->certificate = $validatedRegistration->getCertificate();
    $resObj->email = $credential->email;
    $resObj->password = $credential->password;
    
    echo json_encode($resObj);
    
} catch( Exception $e ) {
    echo $e->getMessage();
}
?>
