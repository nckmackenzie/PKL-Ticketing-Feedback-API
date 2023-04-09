<?php
class Delivery
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function GenerateUniqueId()
    {
        $looping = true;
        $uniqueid = '';
        while ($looping) {
            $uniqueid = uniqid();
            $sql = 'SELECT COUNT(*) FROM deliveries WHERE UniqueId = ?';
            $count = getdbvalue($this->db->dbh,$sql,[$uniqueid]);
            if((int)$count === 0){
                $looping = false;
            }
        }
        return $uniqueid;
    }

    public function Create($data)
    {
        $this->db->query('INSERT INTO `deliveries`(`UniqueId`,`ClientId`,`DeliveryDate`,`DeliveryTime`,`Location`,`Notes`)
                          VALUES (:did,:cid,:ddate,:dtime,:loc,:notes)');
        $this->db->bind(':did',$data['did']);
        $this->db->bind(':cid',$data['client']);
        $this->db->bind(':ddate',$data['deliverydate']);
        $this->db->bind(':dtime',$data['time']);
        $this->db->bind(':loc',$data['location']);
        $this->db->bind(':notes',$data['notes']);
        if(!$this->db->execute()){
            return false;
        }
        return true;
    }
}