<?php

include('config.php');

$result["state"] = true;
$input = json_decode(file_get_contents("php://input"), true);

// Сохранение данных приложения
if ($input["mode"] == "create application") {
    if ($input["id"] == NULL) {
        $result["state"] = false;
        $result["error"]["message"][] = "Application ID is missing";
    }
    if ($input["secret"] == NULL) {
        $result["state"] = false;
        $result["error"]["message"][] = "Application Secret is missing";
    }
    if ($result["state"] === false) {
        echo json_encode($result);
        exit;
    }
    $result["write"] = file_put_contents("access.php", "<?php".PHP_EOL.PHP_EOL."\$zId = \"".$input["id"]."\";".PHP_EOL."\$zKey = \"".$input["secret"]."\";");
    echo json_encode($result);
    exit;
}

// Проверка данных приложения
if (file_exists("access.php")) {
    include("access.php");
} else {
    $result["state"] = false;
    $result["connecting"] = "step1";
    $result["redirectUrl"] = $url."oauth.php";
    echo json_encode($result);
    exit;
}

// Проверка авторизации приложения
if (file_exists("access.json")) {
    $access = json_decode(file_get_contents("access.json"), true);
} else {
    $result["state"] = false;
    $result["connecting"] = "step2";
    $result["appId"] = $zId;
    $result["redirectUrl"] = $url."oauth.php";
    echo json_encode($result);
    exit;
}

// Обновление токенов доступа
if ($access["expires"] < time()) {
    $sendAuth = [
        "grant_type" => "refresh_token",
        "client_id" => $zId,
        "client_secret" => $zKey,
        "redirect_uri" => $url."oauth.php",
        "refresh_token" => $access["refresh"],
    ];
    $rAuth = json_decode(send_request($access["authUrl"]."/oauth/v2/token", [], 'POST', $sendAuth, "form"), true);
    if ($rAuth["expires_in"] > 1000) {
        $access["token"] = $rAuth["access_token"];
        $access["expires"] = time() + $rAuth["expires_in"] - 10;
        file_put_contents("access.json", json_encode($access));
    }
}

// Получение информации о компании
if ($_GET["mode"] == "info") {
    $companys = json_decode(send_request($access["domain"]."/crm/v3/org", [
        "Authorization: Zoho-oauthtoken ".$access["token"],
    ], "GET"), true);
    if ($companys["org"] != NULL) {
        $result["company"] = $companys["org"][0];
        $result["redirectUrl"] = $url."oauth.php";
    } else {
        $result["state"] = false;
        $result["connecting"] = "step2";
        $result["appId"] = $zId;
        $result["redirectUrl"] = $url."oauth.php";
    }
    echo json_encode($result);
    exit;
}


