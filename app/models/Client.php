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

    public function Create($data)
    {
        try {
            $this->db->dbh->beginTransaction();

            $this->db->query('INSERT INTO clients (ClientName,Contact) VALUES(:cname,:contact)');
            $this->db->bind(':cname',$data['customername']);
            $this->db->bind(':contact',$data['contact']);
            $this->db->execute();
            $tid = $this->db->dbh->lastInsertId();
            if(!$this->db->dbh->commit()) return false;

            return returninsertedrow($this->db->dbh,'clients',$tid); 
            
        } catch (PDOException $th) {
            error_log($th->getMessage(),0);
            return false;
        }
    }

    public function GetContact($id)
    {
        $sql = 'SELECT Contact FROM clients WHERE ID=?';
        return getdbvalue($this->db->dbh,$sql,[$id]);
    }
}