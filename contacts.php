<?php

include("connect.php");

if ($input["userId"] == NULL) {
    $result["state"] = false;
    $result["error"]["message"][] = "'userId' is missing";
} else {
    $userId = $input["userId"];
}
if ($input["Last_Name"] == NULL) {
    if ($input["lastName"] == NULL) {
        $result["state"] = false;
        $result["error"]["message"][] = "'lastName' or 'Last_Name' is missing";
    } else {
        $input["Last_Name"] = $input["lastName"];
    }
}
if ($result["state"] === false) {
    echo json_encode($result);
    exit;
}

if (file_exists("contacts.json")) {
    $contacts = json_decode(file_get_contents("contacts.json"), true);
}
if ($input["contactId"] != NULL) {
    $contactId = $input["contactId"];
    $contacts[$userId] = $contactId;
    file_put_contents("contacts.json", json_encode($contacts));
    $result["action"] = "update";
    $type = "PUT";
    $url = $access["domain"]."/crm/v3/Contacts/".$contactId;
} else {
    if ($contacts[$userId] != NULL) {
        if ($input["contactId"] != NULL) {
            $contactId = $input["contactId"];
        } else {}
        $contactId = $contacts[$userId];
        $result["action"] = "update";
        $type = "PUT";
        $url = $access["domain"]."/crm/v3/Contacts/".$contactId;
    } else {
        $result["action"] = "create";
        $type = "POST";
        $url = $access["domain"]."/crm/v3/Contacts";
    }
}

$response = json_decode(send_request($url, ["Authorization: Zoho-oauthtoken ".$access["token"]], $type, ["data"=>[$input]]), true);
if ($response["data"][0] != NULL) {
    $result["contacts"] = $response["data"][0];
    if ($response["data"][0]["code"] == "SUCCESS" && $contactId == NULL) {
        $contacts[$userId] = $response["data"][0]["details"]["id"];
        file_put_contents("contacts.json", json_encode($contacts));
    }
}


echo json_encode($result, JSON_UNESCAPED_UNICODE);




