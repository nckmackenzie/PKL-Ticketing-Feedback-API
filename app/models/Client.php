<?php

class Client
{
    private $db;
    public function __construct()
    {
        $this->db = new Database;
    }

    public function GetClients()
    {
        $sql = 'SELECT ID,UCASE(ClientName) AS ClientName FROM clients ORDER BY ClientName';
        return loadresultset($this->db->dbh,$sql,[]);
    }
}