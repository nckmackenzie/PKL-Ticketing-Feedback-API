<?php
class Subscribe
{
    private $db;
    public function __construct()
    {
        $this->db = new Database;
    }

    public function CheckSubscriber($email)
    {
        $sql = 'SELECT COUNT(*) FROM subscribers WHERE (SubscriberEmail = ?) AND (Unsubscribed = 0)';
        if(getdbvalue($this->db->dbh,$sql,[$email]) > 0){
            return false;
        }
        return true;
    }

    public function Create($data)
    {
        $this->db->query('INSERT INTO subscribers (SubscriberName,SubscriberEmail) VALUES(:sname,:semail)');
        $this->db->bind(':sname',$data['name']);
        $this->db->bind(':semail',$data['email']);
        if(!$this->db->execute()){
            return false;
        }
        return true;
    }
}