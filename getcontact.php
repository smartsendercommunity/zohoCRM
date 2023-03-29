<?php

include("connect.php");

if ($input["contactId"] == NULL) {
    if ($input["userId"] == NULL) {
        $result["state"] = false;
        $result["error"]["message"][] = "'contactId' or 'userId' is missing";
    } else {
        if (file_exists("contacts.json")) {
            $contacts = json_decode(file_get_contents("contacts.json"), true);
        }
        if ($contacts[$input["userId"]] != NULL) {
            $contactId = $contacts[$input["userId"]];
        } else {
            $result["state"] = false;
            $result["error"]["message"][] = "create a contact first";
        }
    }
} else {
    $contactId = $input["contactId"];
}

$url = $access["domain"]."/crm/v3/Contacts/".$contactId;
$getContact = json_decode(send_request($url, ["Authorization: Zoho-oauthtoken ".$access["token"]]), true);
$result["contact"] = $getContact["data"][0];

echo json_encode($result, JSON_UNESCAPED_UNICODE);