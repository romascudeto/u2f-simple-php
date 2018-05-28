<?php 
require 'library/U2FServer.php';

$U2F = new U2FServer;
$registrationData = $U2F::makeRegistration("https://romascudeto.localtunnel.me");
$registrationRequest = json_encode($registrationData['request']);

echo $registrationRequest;

?>
