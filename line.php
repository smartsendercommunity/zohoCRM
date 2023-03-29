<?php

include("connect.php");

if ($input["endpoint"] == NULL) {
    $result["state"] = false;
    $result["error"]["message"][] = "'endpoint' is missing";
}
if ($input["type"] == NULL) {
    $input["type"] = "GET";
}

$result["response"] = json_decode(send_request($access["domain"].$input["endpoint"], [
    "Authorization: Zoho-oauthtoken ".$access["token"],
], $input["type"], $input["param"]), true);

echo json_encode($result, JSON_UNESCAPED_UNICODE);