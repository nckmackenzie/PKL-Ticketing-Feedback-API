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

    public function register($data)
    {
        $this->db->query('INSERT INTO users (`user_name`,`password`,`email`,`contact`,`user_type_id`) 
                          VALUES(:uname,:pwd,:email,:contact,:usid)');
        $this->db->bind(':uname',!empty($data->user_name) ? trim(strtolower($data->user_name)) : NULL);
        $this->db->bind(':pwd',!empty($data->password) ? password_hash(trim(strtolower($data->user_name)),PASSWORD_DEFAULT) : NULL);
        $this->db->bind(':email',!empty($data->email) ? trim(strtolower($data->email)) : NULL);
        $this->db->bind(':contact',!empty($data->contact) ? trim(strtolower($data->contact)) : NULL);
        $this->db->bind(':usid',!empty($data->user_type) ? trim(strtolower($data->user_type)) : NULL);
        if(!$this->db->execute()) :
            return false;
        endif;
        $id = $this->db->getlastinsertid();
        return returninsertedrow($this->db->dbh,'users',$id);  
    }
}