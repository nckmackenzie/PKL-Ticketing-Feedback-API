<?php

class Cas
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    //create a new case
    public function Create($data)
    {
        try 
        {
            $this->db->dbh->beginTransaction(); //begin transaction

            //query to insert
            $this->db->query('INSERT INTO cases (`Subject`,Priority,Staff,Narration,`Status`) VALUES(:subj,:priority,:staff,:narr,:sta)');
            $this->db->bind(':subj',$data['subject']);
            $this->db->bind(':priority',$data['priority']);
            $this->db->bind(':staff',$data['staff']);
            $this->db->bind(':narr',$data['narration']);
            $this->db->bind(':sta',$data['status']);
            $this->db->execute();
            $tid = $this->db->dbh->lastInsertId();
            if(!$this->db->dbh->commit()) return false;
            return returninsertedrow($this->db->dbh,'cases',$tid);
        }
        catch (PDOException $th)
        {
            error_log($th->getMessage(),0);
            return false;
        }
    }
}