<?php
class Feedback
{
    private $db;

    public function __construct()
    {
        $this->db =  new Database;
    }

    public function CheckUniqueId($id)
    {
        $sql = 'SELECT COUNT(*) FROM deliveries WHERE UniqueId = ?';
        $count = getdbvalue($this->db->dbh,$sql,[$id]);
        if((int)$count === 0){
            return false;
        }
        return true;
    }

    public function GetDeliveryId($uniqueid)
    {
        return getdbvalue($this->db->dbh,'SELECT ID FROM deliveries WHERE (UniqueId=?)',[$uniqueid]);
    }

    public function Create($data)
    {
        $this->db->query('INSERT INTO `feedback`(`DeliveryId`,`KnowAbout`,`Duration`,`Quality`,`Service`,`Repurchase`,`Recommend`,`DurationAsCustomer`, 
                                                 `Damages`,`DamagesExplained`,`PendingWork`,`PendingWorkExplained`,`Suggestions`)
                          VALUES (:did,:know,:duration,:quality,:servic,:purchase,:recommend,:customer,:damages,:damageexp,:pend,
                                  :pendexp,:sugg)');
        $this->db->bind(':did',$this->GetDeliveryId($data['did']));
        $this->db->bind(':know',$data['knowabout']);
        $this->db->bind(':duration',$data['duration']);
        $this->db->bind(':quality',$data['quality']);
        $this->db->bind(':servic',$data['service']);
        $this->db->bind(':purchase',$data['repurchase']);
        $this->db->bind(':recommend',$data['recommend']);
        $this->db->bind(':customer',$data['howlong']);
        $this->db->bind(':damages',$data['damages']);
        $this->db->bind(':damageexp',$data['damagesexplained']);
        $this->db->bind(':pend',$data['pendingwork']);
        $this->db->bind(':pendexp',$data['pendingexplained']);
        $this->db->bind(':sugg',$data['additional']);
        if(!$this->db->execute()){
            return false;
        }
        return true;
    }

    public function GetDelivery($uniqueid)
    {
        $count = getdbvalue($this->db->dbh,'SELECT COUNT(*) FROM deliveries WHERE (UniqueId=?)',[$uniqueid]);
        if((int)$count === 0) return false;
        $deliveryid = $this->GetDeliveryId($uniqueid);
        $count = getdbvalue($this->db->dbh,'SELECT COUNT(*) FROM feedback WHERE (DeliveryId=?)',[(int)$deliveryid]);
        return (int)$count;
    }

    public function GetDeliveries()
    {
        $sql = 'SELECT 
                    d.ID,
                    c.ClientName,
                    d.DeliveryDate,
                    ((f.Duration + 
                    f.Quality +
                    f.Service +
                    f.Repurchase +
                    f.Recommend) / 5) As AverageRating
                FROM `feedbacks` f join deliveries d on f.DeliveryId = d.ID
                join  clients c on d.ClientId = c.ID
                ORDER BY f.ID DESC';
        return loadresultset($this->db->dbh,$sql,[]);
    }

    public function DeliveryFound($id)
    {
        return converttobool(getdbvalue($this->db->dbh,'SELECT COUNT(*) FROM deliveries WHERE (ID=?)',[(int)$id]));
    }

    public function GetDeliveryDetails($id)
    {
        $details = loadsingleset($this->db->dbh,'SELECT ClientId,DeliveryDate,UniqueId FROM deliveries WHERE (ID=?)',[(int)$id]);
        $contact = getdbvalue($this->db->dbh,'SELECT Contact FROM clients WHERE (ID=?)',[(int)$details->ClientId]);
        return [$contact,$details->DeliveryDate,$details->UniqueId];
    }
}
