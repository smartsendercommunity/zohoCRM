<?php

include("connect.php");

if ($input["leadId"] == NULL) {
    $result["state"] = false;
    $result["error"]["message"][] = "'leadId' is missing";
}

$url = $access["domain"]."/crm/v3/Leads/".$input["leadId"];
$getLead = json_decode(send_request($url, ["Authorization: Zoho-oauthtoken ".$access["token"]]), true);
$result["lead"] = $getLead["data"][0];

echo json_encode($result, JSON_UNESCAPED_UNICODE);