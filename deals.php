<?php

include("connect.php");

if ($input["userId"] == NULL) {
    $result["state"] = false;
    $result["error"]["message"][] = "'userId' is missing";
} else {
    if (file_exists("contacts.json")) {
        $contacts = json_decode(file_get_contents("contacts.json"), true);
    }
    if ($contacts[$input["userId"]] == NULL) {
        $result["state"] = false;
        $result["error"]["message"][] = "create a contact first";
    } else {
        $input["Contact_Name"]["id"] = $contacts[$input["userId"]];
    }
}
if ($input["Deal_Name"] == NULL) {
    if ($input["title"] == NULL) {
        $result["state"] = false;
        $result["error"]["message"][] = "'Deal_Name' or 'title' is missing";
    } else {
        $input["Deal_Name"] = $input["title"];
    }
}
if ($input["Stage"] == NULL) {
    if ($input["stage"] == NULL) {
        $result["state"] = false;
        $result["error"]["message"][] = "'Stage' is missing";
    } else {
        $input["Stage"] = $input["stage"];
    }
}

if ($input["dealId"] == NULL) {
    $result["action"] = "create";
    $type = "POST";
    $url = $access["domain"]."/crm/v3/Deals";
} else {
    $result["action"] = "update";
    $type = "PUT";
    $url = $access["domain"]."/crm/v3/Deals/".$input["dealId"];
}

$response = json_decode(send_request($url, ["Authorization: Zoho-oauthtoken ".$access["token"]], $type, ["data"=>[$input]]), true);
if ($response["data"][0] != NULL) {
    $result["deals"] = $response["data"][0];
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);