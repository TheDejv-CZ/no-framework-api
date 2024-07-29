<?php

use Dejv\TodoApi\TodoController;

try {
    $db = new \PDO("sqlite:../database.sqlite");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $ex) {
    die ("Connection to database failed. Check if sqlite database file exists or its not corrupted.");
}

require_once 'TodoController.php';
$controller = new TodoController($db);

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$parsedUrl = parse_url($uri);
$path = $parsedUrl['path'];
$query = $parsedUrl['query'] ?? '';

/**
 *  Routing to specific action in Controller based on Request Method
 */
switch ($method) {
    case 'POST':
        if ($path == '/api/todos') {
            $controller->create(file_get_contents('php://input'));
        }
        break;
    case 'GET':
        if ($path == '/api/todos') {
            $controller->readAll();
        } elseif (preg_match('/\/api\/todos\/(\d+)/', $path, $matches)) {
            $controller->read($matches[1]);
        }
        break;
    case 'PUT':
        if (preg_match('/\/api\/todos\/(\d+)/', $path, $matches)) {
            $controller->update($matches[1], file_get_contents('php://input'));
        }
        break;
    case 'DELETE':
        if (preg_match('/\/api\/todos\/(\d+)/', $path, $matches)) {
            $controller->delete($matches[1]);
        }
        break;
    case 'OPTIONS':
        http_response_code(204);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}