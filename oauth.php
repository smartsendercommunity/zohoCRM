<?php

include('config.php');
if (file_exists("access.php")) {
    include("access.php");
}

$input = json_decode(file_get_contents("php://input"), true);

$log["input"] = [
    "json" => $input,
    "post" => $_POST,
    "get" => $_GET
];

$access["authUrl"] = $_GET["accounts-server"];

$sendAuth = [
    "grant_type" => "authorization_code",
    "client_id" => $zId,
    "client_secret" => $zKey,
    "redirect_uri" => $url."oauth.php",
    "code" => $_GET["code"],
];
$log["auth"]["send"] = $sendAuth;
$rAuth = json_decode(send_request($access["authUrl"]."/oauth/v2/token", [], 'POST', $sendAuth, "form"), true);
if ($rAuth["expires_in"] > 1000) {
    $access["token"] = $rAuth["access_token"];
    $access["refresh"] = $rAuth["refresh_token"];
    $access["domain"] = $rAuth["api_domain"];
    $access["expires"] = time() + $rAuth["expires_in"] - 10;
    file_put_contents("access.json", json_encode($access));
}
$log["auth"]["result"] = $rAuth;

send_forward(json_encode($log), "https://log.mufiksoft.com/zohocrm");
header('Location: '.$url.'index.html');
