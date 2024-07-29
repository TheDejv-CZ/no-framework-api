<?php

namespace Dejv\TodoApi;

use PDO;

require_once 'Todo.php';
require_once 'BaseController.php';
class TodoController extends BaseController
{
    private Todo $todoModel;
    
    /**
     * Construct
     * @param PDO $db
     */
    public function __construct(PDO $db) {
        $this->todoModel = new Todo($db);
        $this->todoModel->testDataTableIntegrity();
    }
    /**
     * Processing request from POST or PUT
     * @param $request
     * @return array
     */
    private function processRequest($request): array {
        $out['status'] = false;
        $out['data'] = null;
        $out['error'] = null;
        
        if (!$this->json_validator($request)) {
            http_response_code(400);
            $out['error'] = "This is not a JSON input.";
            return $out;
        }
        
        $data = json_decode($request, true);
        if (!$this->validateTodoInput($data)) {
            http_response_code(400);
            $out['error'] = "Some inputs have incorrect values.";
            return $out;
        }
        
        $out['data']['title'] = htmlspecialchars($data['title']);
        $out['data']['description'] = htmlspecialchars($data['description']);
        $out['data']['status'] = htmlspecialchars($data['status']);
        
        $out['status'] = true;
        
        return $out;
    }
    
    /**
     * Create request = processing input data and proceed to insert To-do Item to database
     * @param $request
     * @return void
     */
    public function create($request): void {
        
        $data = $this->processRequest($request);
        if (!$data['status']) {
            http_response_code(400);
            echo json_encode(['error' => $data['error']]);
            return;
        }
        $data = $this->todoModel->createTodo($data['data']);
        if (!$data['status']) {
            http_response_code(400);
            echo json_encode(['error' => 'New To-do Item has not been saved. Sorry.']);
            return;
        }
        echo json_encode($data['data']);
    }
    
    /**
     * Get all To-do Items from database
     * @return void
     */
    public function readAll(): void {
        $data = $this->todoModel->getTodos();
        if (!$data['status']) {
            http_response_code(400);
            echo json_encode(['error' => $data['error']]);
            return;
        }
        echo json_encode($data['data']);
    }
    
    /**
     * Get single To-do Item from database
     * @param $id
     * @return void
     */
    public function read($id): void {
        if (!is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Bad Request - ID is not numeric']);
            return;
        }
        $data = $this->todoModel->getTodoById($id);
        if (!$data['status']) {
            http_response_code(400);
            echo json_encode(['error' => 'Bad Request']);
            return;
        }
        if (empty($data['data'])) {
            http_response_code(404);
            echo json_encode(['error' => 'To-do Item not found']);
            return;
        }
        echo json_encode($data['data']);
    }
    
    /**
     * Update a To-do Item in Database
     * @param $id
     * @param $request
     * @return void
     */
    public function update($id, $request): void {
        if (!is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Bad Request - ID is not numeric']);
            return;
        }
        $data = $this->processRequest($request);
        if (!$data['status']) {
            http_response_code(400);
            echo json_encode(['error' => $data['error']]);
            return;
        }
        $data = $this->todoModel->updateTodo($id, $data['data']);
        if (empty($data['data'])) {
            http_response_code(404);
            echo json_encode(['error' => 'To-do Item not found']);
            return;
        }
        if (!$data['status']) {
            http_response_code(400);
            echo json_encode(['error' => $data['error']]);
            return;
        }
        echo json_encode($data['data']);
    }
    
    /**
     * Remove To-do Item from database
     * @param $id
     * @return void
     */
    public function delete($id): void {
        if (!is_numeric($id)) {
            http_response_code(404);
            echo json_encode(['error' => 'Malformed request']);
            return;
        }
        $data = $this->todoModel->deleteTodo($id);
        if (!$data['status']) {
            http_response_code(400);
            echo json_encode(['error' => $data['error']]);
            return;
        }
        http_response_code(204);
    }
}