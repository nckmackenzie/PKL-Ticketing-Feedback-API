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

    public function ByPriority()
    {
        return loadresultset($this->db->dbh,'SELECT DISTINCT Priority,fn_get_priority_count(Priority) As RecordCount
                                             FROM `cases` WHERE (Deleted = 0) AND (`Status` <> ?) ',['closed']);
    }

    public function ByStatus()
    {
        return loadresultset($this->db->dbh,'SELECT DISTINCT Status,fn_get_status_count(`Status`) As RecordCount 
                                             FROM `cases` WHERE (Deleted = 0) AND (`Status` <> ?)',['closed']);
    }

    public function ByDepartment()
    {
        return loadresultset($this->db->dbh,'SELECT DISTINCT `StaffDept`,fn_get_department_count(StaffDept) As RecordCount 
                                             FROM `vw_cases_by_dept`',[]);
    }
}