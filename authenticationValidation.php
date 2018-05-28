<?php 
require 'library/U2FServer.php';

$U2F = new U2FServer;
$registrations = json_decode($_POST['registrations']);
$authenticationRequest = json_decode($_POST['authenticationRequest']);
$arrAuthenticationRequest = [
    'appId' => $authenticationRequest->appId,
    'keyHandle' => $authenticationRequest->keyHandle,
    'challenge' => $authenticationRequest->challenge
    ];
$signRequest[] = new SignRequest($arrAuthenticationRequest);
$authenticationResponse = json_decode($_POST['authenticationResponse']);
try {
    $validatedAuthentication = $U2F::authenticate(
        $signRequest,
        $registrations,
        $authenticationResponse
    );
    $response = new stdClass;
    $response->errorCode = "0";
    $response->data = $validatedAuthentication;
    echo json_encode($response);

} catch( Exception $e ) {
    $errorCode = $e->getMessage();
    $errorCode = str_replace("Error code: ","", $errorCode);
    $errorCode = str_replace("User-agent returned error. ","", $errorCode);
    $response = new stdClass;
    $response->errorCode = $errorCode;
    echo json_encode($response);
}

?>
