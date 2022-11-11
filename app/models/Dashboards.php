<?php

class Dashboards
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function GetSummaryInfo()
    {
        $sql = 'SELECT COUNT(*) FROM cases WHERE (Deleted = 0) AND (`Status` = ?)';
        $opentickets = getdbvalue($this->db->dbh,$sql,['open']);
        $closedtickets = getdbvalue($this->db->dbh,$sql,['closed']);
        $pendingtickets = getdbvalue($this->db->dbh,$sql,['pending']);
        return [$opentickets,$closedtickets,$pendingtickets];
    }
}