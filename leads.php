<?php

include("connect.php");

if ($input["Last_Name"] == NULL) {
    if ($input["lastName"] == NULL) {
        $result["state"] = false;
        $result["error"]["message"][] = "'Last_Name' or 'lastName' is missing";
    } else {
        $input["Last_Name"] = $input["lastName"];
    }
}

if ($input["leadId"] == NULL) {
    $result["action"] = "create";
    $type = "POST";
    $url = $access["domain"]."/crm/v3/Leads";
} else {
    $result["action"] = "update";
    $type = "PUT";
    $url = $access["domain"]."/crm/v3/Leads/".$input["leadId"];
}

$response = json_decode(send_request($url, ["Authorization: Zoho-oauthtoken ".$access["token"]], $type, ["data"=>[$input]]), true);
if ($response["data"][0] != NULL) {
    $result["leads"] = $response["data"][0];
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);