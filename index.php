<?php

use app\Http\ApiHandler;
use app\Http\AppResponse;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization');

include_once $_SERVER['DOCUMENT_ROOT'] . '/initial/autoload.php';

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestMethodArray = [];

if ($requestMethod === "GET") {
    $requestMethodArray = $_REQUEST;

    $apiHandler = new ApiHandler();

    if (isset($requestMethodArray['method'])) {
        $result = $apiHandler->execCommand($requestMethodArray['method']);

        echo(json_encode($result, JSON_PRETTY_PRINT));
    }

} else {
    $returnArray = AppResponse::getResponse('405');
    echo(json_encode($returnArray));
}
