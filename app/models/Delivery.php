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

    public function UpdateNotificationStatus($status,$uniqueid)
    {
        $this->db->query('UPDATE deliveries SET NotificationStatus=:notstatus WHERE UniqueId=:id');
        $this->db->bind(':notstatus',$status);
        $this->db->bind(':id',$uniqueid);
        if(!$this->db->execute()){
            return false;
        }
        return true;
    }

    public function GetLatestDeliveries()
    {
        $sql = 'SELECT
                    d.ID,
                    c.ClientName,
                    d.DeliveryDate,
                    d.Location,
                    d.NotificationStatus,
                    d.FeedbackNotificationStatus
                FROM   deliveries d join clients c on d.ClientId = c.ID left join feedbacks f on d.ID = f.DeliveryId
                WHERE d.Deleted = 0
                ORDER BY d.ID DESC LIMIT 10;';
        return loadresultset($this->db->dbh,$sql,[]);
    }
}