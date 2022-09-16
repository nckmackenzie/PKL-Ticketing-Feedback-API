<?php 
class User
{
    private $db;
    public function __construct()
    {
        $this->db= new Database;
    }

    public function getuser($id)
    {
        return getsingle($this->db->dbh,'users',$id);
    }
}