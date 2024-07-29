<?php
namespace Dejv\TodoApi;
use PDO;

class Todo
{
    private PDO $db;
    
    /**
     * Construct
     * @param PDO $db
     */
    public function __construct(PDO $db) {
        $this->db = $db;
    }
    
    /**
     * Create new table with name "todos"
     * @return void
     */
    private function createTodosTable(): void {
        $q = "CREATE TABLE IF NOT EXISTS todos (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title TEXT,
                    description TEXT,
                    status TEXT CHECK( status IN ('pending','completed')))";
        $this->db->query($q);
    }
    
    /**
     * Check if table "todos" still exists, if not, create it
     * @return void
     */
    public function testDataTableIntegrity(): void {
        $rs = $this->db->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name='todos'");
        $out['status'] = $rs->execute();
        $table = $rs->fetch(PDO::FETCH_ASSOC);
        if (empty($table)) {
            $this->createTodosTable();
        }
    }
    
    /**
     * Insert new To-do Item
     * @param $data
     * @return array
     */
    public function createTodo($data): array {
        $rs = $this->db->prepare("INSERT INTO todos (title, description, status) VALUES (:title, :description, :status)");
        $rs->bindValue(':title', $data['title']);
        $rs->bindValue(':description', $data['description']);
        $rs->bindValue(':status', $data['status']);
        $out['status'] = $rs->execute();
        if (!$out['status']) {
            $out['error'] = $rs->errorInfo();
            return $out;
        }
        $data['id'] = (int)$this->db->lastInsertId();
        $out['data'] = $data;
        return $out;
    }
    
    
    /**
     * Select all To-do Items from database
     * @return array
     */
    public function getTodos(): array {
        $rs = $this->db->prepare("SELECT * FROM todos");
        $out['status'] = $rs->execute();
        if (!$out['status']) {
            $out['error'] = $rs->errorInfo();
            return $out;
        }
        $out['data'] = $rs->fetchAll(PDO::FETCH_ASSOC);
        return $out;
    }
    
    /**
     * Get single To-do Item from database based on ID
     * @param $id
     * @return array
     */
    public function getTodoById($id): array {
        $rs = $this->db->prepare("SELECT * FROM todos WHERE id = :id");
        $rs->bindValue(':id', $id, PDO::PARAM_INT);
        $out['status'] = $rs->execute();
        if (!$out['status']) {
            $out['error'] = $rs->errorInfo();
            return $out;
        }
        $out['data'] = $rs->fetch(PDO::FETCH_ASSOC);
        return $out;
    }
    
    /**
     * Update a single To-do Item based on ID
     * @param $id
     * @param $data
     * @return array
     */
    public function updateTodo($id, $data): array {
        $out['status'] = false;
        $out['data'] = false;
        $todoItem = $this->getTodoById($id);
        if (!$todoItem['status']) {
            $out['error'] = $todoItem['error'];
            return $out;
        }
        if (empty($todoItem['data'])) {
            $out['error'] = 'Not Found';
            return $out;
        }
        $rs = $this->db->prepare("UPDATE todos SET title = :title, description = :description, status = :status WHERE id = :id");
        $rs->bindValue(':title', $data['title']);
        $rs->bindValue(':description', $data['description']);
        $rs->bindValue(':status', $data['status']);
        $rs->bindValue(':id', $id, PDO::PARAM_INT);
        $out['status'] = $rs->execute();
        if (!$out['status']) {
            $out['error'] = $rs->errorInfo();
            return $out;
        }
        $out['data'] = $data;
        return $out;
    }
    
    /**
     * Remove To-do Item from database
     * @param $id
     * @return array
     */
    public function deleteTodo($id) :array {
        $out['status'] = false;
        $out['data'] = false;
        $todoItem = $this->getTodoById($id);
        if (!$todoItem['status']) {
            $out['error'] = $todoItem['error'];
            return $out;
        }
        if (empty($todoItem['data'])) {
            $out['error'] = 'Not Found';
            return $out;
        }
        $rs = $this->db->prepare("DELETE FROM todos WHERE id = :id");
        $rs->bindValue(':id', $id, PDO::PARAM_INT);
        
        $out['status'] = $rs->execute();
        if (!$out['status']) $out['error'] = $rs->errorInfo();
        return $out;
    }
}