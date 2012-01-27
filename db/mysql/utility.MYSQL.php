<?php
class DB_UTILITY_MYSQL {
    private $conn;

    public function  __construct ($conn) {
        $this->conn = $conn;
    }
}
