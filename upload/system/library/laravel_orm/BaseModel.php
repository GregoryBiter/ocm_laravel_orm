<?php

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model {
    // Доступ до OpenCart реєстрації (може знадобитися для OpenCart функцій)
    protected $registry;

    public function __construct($registry = null) {
        $this->registry = $registry;

        // Виклик конструктора Eloquent
        parent::__construct();
    }

    // Додайте підтримку виклику OpenCart методів
    public function __get($key) {
        if ($this->registry && $this->registry->has($key)) {
            return $this->registry->get($key);
        }

        return parent::__get($key);
    }

    public function __set($key, $value) {
        if ($this->registry) {
            $this->registry->set($key, $value);
        } else {
            parent::__set($key, $value);
        }
    }
}
