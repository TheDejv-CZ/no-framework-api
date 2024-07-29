<?php

use Dejv\TodoApi\Todo;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase {
    private PDO $db;
    private Todo $todo;
    protected function setUp(): void  {
        $this->db = new PDO('sqlite::memory:');
        $this->db->exec("CREATE TABLE todos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT,
            description TEXT,
            status TEXT CHECK( status IN ('pending','completed') )
        )");
        $this->todo = new Todo($this->db);
    }
    
    public function testCreateTodo() {
        $data['title'] = 'New Todo';
        $data['description'] = 'Description';
        $data['status'] = 'pending';
        $out = $this->todo->createTodo($data);
        $this->assertTrue($out['status']);
        $this->assertIsInt($out['data']['id']);
        
        $q = $this->db->query('SELECT * FROM todos WHERE id = '.$out['data']['id']);
        $result = $q->fetch(PDO::FETCH_ASSOC);
        
        $this->assertEquals('New Todo', $result['title']);
        $this->assertEquals('Description', $result['description']);
        $this->assertEquals('pending', $result['status']);
    }
    
    public function testGetAllTodos() {
        $data['title'] = 'New Todo';
        $data['description'] = 'Description';
        $data['status'] = 'pending';
        $this->todo->createTodo($data);
        
        $data['title'] = 'New Todo 2';
        $data['description'] = 'Description 2';
        $data['status'] = 'completed';
        $this->todo->createTodo($data);
        
        $out = $this->todo->getTodos();
        $this->assertTrue($out['status']);
        $this->assertCount(2, $out['data']);
    }
    
    public function testGetTodoById() {
        $data['title'] = 'New Todo';
        $data['description'] = 'Description';
        $data['status'] = 'pending';
        $id = $this->todo->createTodo($data);
        $out = $this->todo->getTodoById($id);
        $this->assertTrue($out['status']);
        
        $this->assertEquals('New Todo', $out['data']['title']);
        $this->assertEquals('Description', $out['data']['description']);
        $this->assertEquals('pending', $out['data']['status']);
    }
    public function testUpdateTodo() {
        $data['title'] = 'New Todo';
        $data['description'] = 'Description';
        $data['status'] = 'pending';
        $out = $this->todo->createTodo($data);
        
        $data['title'] = 'Updated Todo';
        $data['description'] = 'New Description';
        $data['status'] = 'completed';
        $id = $out['data']['id'];
        
        $this->todo->updateTodo($id, $data);
        
        $out = $this->todo->getTodoById($id);
        $this->assertTrue($out['status']);
        $this->assertEquals('Updated Todo', $out['data']['title']);
        $this->assertEquals('New Description', $out['data']['description']);
        $this->assertEquals('completed', $out['data']['status']);
    }
    
    public function testDeleteTodo()
    {
        $data['title'] = 'New Todo';
        $data['description'] = 'Description';
        $data['status'] = 'pending';
        $out = $this->todo->createTodo($data);
        $this->todo->deleteTodo($out['data']['id']);
        
        $rs = $this->db->query('SELECT * FROM todos WHERE id = '.$out['data']['id']);
        $result = $rs->fetch(PDO::FETCH_ASSOC);
        
        $this->assertFalse($result);
    }
}