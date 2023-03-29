<?php

include("connect.php");

if ($input["dealId"] == NULL) {
    if ($input["userId"] == NULL) {
        $result["state"] = false;
        $result["error"]["message"][] = "'dealId' or 'userId' is missing";
    } else {
        if (file_exists("contacts.json")) {
            $contacts = json_decode(file_get_contents("contacts.json"), true);
        }
        if ($contacts[$input["userId"]] == NULL) {
            $result["state"] = false;
            $result["error"]["message"][] = "create a contact first";
        } else {
            $contactId = $contacts[$input["userId"]];
        }
    }
} else {
    $dealId = $input["dealId"];
}
if ($result["state"] === false) {
    echo json_encode($result);
    exit;
}

if ($dealId != NULL) {
    $url = $access["domain"]."/crm/v3/Deals/".$dealId;
    $getDeals = json_decode(send_request($url, ["Authorization: Zoho-oauthtoken ".$access["token"]]), true);
    $result["deals"] = $getDeals["data"][0];
} else {
    $url = $access["domain"]."/crm/v3/Contacts/".$contactId."/Deals?fields=id";
    $getDealsIds = json_decode(send_request($url, ["Authorization: Zoho-oauthtoken ".$access["token"]]), true);
    if ($getDealsIds["data"] != NULL) {
        $result["dealCount"] = count($getDealsIds["data"]);
        foreach ($getDealsIds["data"] as $oneDealId) {
            $url = $access["domain"]."/crm/v3/Deals/".$oneDealId["id"];
            $getOneDeal = json_decode(send_request($url, ["Authorization: Zoho-oauthtoken ".$access["token"]]), true);
            if ($getOneDeal["data"][0] != NULL) {
                $result["deals"][] = $getOneDeal["data"][0];
            }
        }
    }
}


echo json_encode($result, JSON_UNESCAPED_UNICODE);




