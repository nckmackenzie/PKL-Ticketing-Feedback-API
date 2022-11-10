<?php

class Staff
{
    private $db;
    
    public function __construct()
    {
        $this->db = new Database;
    }

    public function Create($data)
    {
        try {
            $this->db->dbh->beginTransaction();

            $this->db->query('INSERT INTO staffs (StaffName,StaffDept) VALUES(:sname,:sdept)');
            $this->db->bind(':sname',$data['staffname']);
            $this->db->bind(':sdept',$data['staffdept']);
            $this->db->execute();
            $tid = $this->db->dbh->lastInsertId();
            if(!$this->db->dbh->commit()) return false;

            return returninsertedrow($this->db->dbh,'staffs',$tid); 
            
        } catch (PDOException $th) {
            error_log($th->getMessage(),0);
            return false;
        }
    }

    public function GetStaffs()
    {
        return loadresultset($this->db->dbh,'SELECT ID,StaffName FROM staffs',[]);
    }
}