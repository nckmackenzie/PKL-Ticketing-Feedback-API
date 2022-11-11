<?php

class Dashboard extends Controller
{
    public function __construct()
    {
        validatetoken();
        $this->dashboardmodel = $this->model('Dashboards');
    }

    public function summaryinfo()
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            $results = $this->dashboardmodel->GetSummaryInfo();
            $summary = ['open' => $results[0],'closed' => $results[1],'pending' => $results[2]];
            sendresponse(200,null,true,$summary);
        }
        elseif ($_SERVER['REQUEST_METHOD'] === 'OPTIONS')
        {
            
        }
        else
        {
            sendresponse(405, 'Invalid request method',false);
            exit;
        }
    }
}