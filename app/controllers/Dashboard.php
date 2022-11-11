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
            $data = [];
            //summary information for cards
            $results = $this->dashboardmodel->GetSummaryInfo();
            $summary = ['open' => $results[0],'closed' => $results[1],'pending' => $results[2]];
            $data['summarycard'] = $summary;
            //chart data for by priority chart
            $prioritydata = [];
            foreach($this->dashboardmodel->ByPriority() as $result)
            {
                array_push($prioritydata,[
                    'priority' => ucwords($result->Priority),
                    'value' => $result->RecordCount,
                ]);
            }
            $data['bypriority'] = $prioritydata;
            //chart data for by dept
            $departmentdata = [];
            foreach($this->dashboardmodel->ByDepartment() as $result)
            {
                array_push($departmentdata,[
                    'department' => ucwords($result->StaffDept),
                    'value' => $result->RecordCount,
                ]);
            }
            $data['bydepartment'] = $departmentdata;
            sendresponse(200,null,true,$data);
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