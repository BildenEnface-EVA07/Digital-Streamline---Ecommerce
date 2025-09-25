<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/../config/db.php');

function getRequestData() {
    $data = $_POST;
    if (empty($data)) {
        $json = file_get_contents('php://input');
        $decoded = json_decode($json, true);
        if (is_array($decoded)) $data = $decoded;
    }
    return array_map(function($v){ return is_string($v) ? trim($v) : $v; }, $data);
}

function jsonResponse($payload, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload);
    exit;
}
