<?php
class DB_UTILITY_SQLITE {
    private $conn;

    public function  __construct ($conn) {
        $this->conn = $conn;
    }
}

/* 
   list database, fileds. -> mysql_list_dbs
   drop database
   create database
   export
   backup
   optimize table or database
   get info (client, host, proto, server, stat) mysql_get_{}_info

 */