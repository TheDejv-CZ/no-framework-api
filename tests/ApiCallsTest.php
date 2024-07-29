<?php

use PHPUnit\Framework\TestCase;

class ApiCallsTest extends TestCase {
    private string $baseUrl = 'http://localhost:8000/api';
    public function testCreateTodo() {
        $response = $this->sendRequest('/todos', 'POST', [
            'title' => 'Test Todo',
            'description' => 'Description',
            'status' => 'pending'
        ]);
        
        $this->assertEquals(200, $response['status_code']);
        $this->assertArrayHasKey('id', $response['body']);
    }
    public function testGetAllTodos() {
        $response = $this->sendRequest('/todos', 'GET');
        
        $this->assertEquals(200, $response['status_code']);
        $this->assertIsArray($response['body']);
    }
    public function testGetTodoById() {
        $createResponse = $this->sendRequest('/todos', 'POST', [
            'title' => 'Test Todo',
            'description' => 'Description',
            'status' => 'pending'
        ]);
        
        $id = $createResponse['body']['id'];
        $response = $this->sendRequest("/todos/$id", 'GET');
        
        $this->assertEquals(200, $response['status_code']);
        $this->assertEquals('Test Todo', $response['body']['title']);
    }
    public function testUpdateTodo() {
        $createResponse = $this->sendRequest('/todos', 'POST', [
            'title' => 'Test Todo',
            'description' => 'Description',
            'status' => 'pending'
        ]);
        
        $id = $createResponse['body']['id'];
        $updateResponse = $this->sendRequest("/todos/$id", 'PUT', [
            'title' => 'Updated Todo',
            'description' => 'New Description',
            'status' => 'completed'
        ]);
        
        $this->assertEquals(200, $updateResponse['status_code']);
        $response = $this->sendRequest("/todos/$id", 'GET');
        $this->assertEquals('Updated Todo', $response['body']['title']);
    }
    public function testDeleteTodo() {
        $createResponse = $this->sendRequest('/todos', 'POST', [
            'title' => 'Test Todo',
            'description' => 'Description',
            'status' => 'pending'
        ]);
        
        $id = $createResponse['body']['id'];
        $deleteResponse = $this->sendRequest("/todos/$id", 'DELETE');
        
        $this->assertEquals(204, $deleteResponse['status_code']);
        $response = $this->sendRequest("/todos/$id", 'GET');
        $this->assertEquals(404, $response['status_code']);
    }
    private function sendRequest($endpoint, $method, $data = []) {
        $url = $this->baseUrl . $endpoint;
        $options = [
            'http' => [
                'method' => $method,
                'header' => 'Content-Type: application/json',
                'content' => json_encode($data),
                'ignore_errors' => true,
            ]
        ];
        
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $statusCode = $this->getHttpStatusCode($http_response_header);
        $body = json_decode($result, true);
        
        return [
            'status_code' => $statusCode,
            'body' => $body,
        ];
    }
    
    private function getHttpStatusCode($headers) {
        if (is_array($headers) && count($headers) > 0) {
            $parts = explode(' ', $headers[0]);
            if (count($parts) > 1) {
                return intval($parts[1]);
            }
        }
        return 0;
    }
}