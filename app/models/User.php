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

    public function checkexists($field,$id,$value)
    {
        return checkexists($this->db->dbh,'users',$field,$id,$value);
    }

    public function updateprofile($data,$id)
    {
        $this->db->query('UPDATE 
                            users 
                          SET 
                            user_name = :uname,
                            email = :email, 
                            contact = :contact
                          WHERE
                            ID = :id');
        $this->db->bind(':uname', !empty(trim($data->userName)) ? trim(strtolower($data->userName)) : null);
        $this->db->bind(':email', !empty(trim($data->email)) ? trim(strtolower($data->email)) : null);
        $this->db->bind(':contact', !empty(trim($data->contact)) ? trim(strtolower($data->contact)) : null);
        $this->db->bind(':id', $id);
        if(!$this->db->execute()) return false;
        return returninsertedrow($this->db->dbh,'users',$id);
    }
}