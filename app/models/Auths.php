<?php

class Auths
{
    private $db;
    public function __construct()
    {
        $this->db = new Database;
    }
    public function checkemailexists($id,$val)
    {
        return checkexists($this->db->dbh,'users','email',$id,$val);
    }
}