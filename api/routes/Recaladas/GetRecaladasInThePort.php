<?php

namespace Api\Routes\Endpoint\Recaladas;

header("Access-Control-Allow-Origin: https://localhost:8100");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once $_SERVER["DOCUMENT_ROOT"] . "/guiastur/vendor/autoload.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/guiastur/api/controllers/Recaladas/GetRecaladasInThePortMobileController.php";

use Api\Controllers\Recaladas\GetRecaladasInThePortMobileController;

$controller = new GetRecaladasInThePortMobileController();
$controller->handleRequest($_REQUEST);
