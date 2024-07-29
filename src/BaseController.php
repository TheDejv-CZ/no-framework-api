<?php

namespace Dejv\TodoApi;
class BaseController
{
    /**
     * Validate if provided string is in JSON format
     * @param $data
     * @return bool
     */
    public function json_validator($data): bool {
        if (!empty($data)) {
            return is_string($data) &&
            is_array(json_decode($data, true));
        }
        return false;
    }
    
    /**
     * Validate inputs for To-do Item
     * @param array $data
     * @return bool
     */
    public function validateTodoInput(array $data): bool {
        if (!isset($data['title']) || !is_string($data['title']) || empty(trim($data['title']))) {
            return false;
        }
        if (!isset($data['description']) || !is_string($data['description']) || empty(trim($data['description']))) {
            return false;
        }
        if (!isset($data['status']) || !in_array($data['status'], ['pending', 'completed'])) {
            return false;
        }
        return true;
    }
}