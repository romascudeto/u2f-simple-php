<?php 
require 'library/U2FServer.php';

$U2F = new U2FServer;
$registrations = json_decode($_POST['registrations']);
$authenticationRequest = $U2F::makeAuthentication($registrations, "https://romascudeto.localtunnel.me");

echo json_encode($authenticationRequest[0]);

?>
