<?php

class Staffs extends Controller
{
    public function __construct()
    {
        validatetoken();
        $this->staffmodel = $this->model('Staff');
    }

    public function index()
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            $data = [];
            $results = $this->staffmodel->GetStaffs();
            foreach($results as $result)
            {
                array_push($data,[
                    'value' => $result->ID,
                    'text' => ucwords($result->StaffName)
                ]);
            }

            sendresponse(200,null,true,$data);
            exit;
        }
        elseif ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            
        }
        else{
            sendresponse(405, ['Invalid request method'],false);
            exit;
        }
    }

    public function create()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $messages = [];
            $fields = json_decode(file_get_contents('php://input')); //decode json input
            $data = [
                'staffname' => isset($fields->staffName) && !empty(trim($fields->staffName)) ? strtolower(trim($fields->staffName)) : null,
                'staffdept' => isset($fields->staffDepartment) && !empty(trim($fields->staffDepartment)) ? strtolower(trim($fields->staffDepartment)) : null,
            ];
            //validate
            if(is_null($data['staffname'])){
                array_push($messages,'Provide staff name');
            }
            if(is_null($data['staffdept'])){
                array_push($messages,'Provide staff dept');
            }

            if(count($messages) > 0){
                sendresponse(400,$messages,false);
                exit();
            }
            //get inserted row
            $newstaff = $this->staffmodel->Create($data);
            if(!$newstaff){
                sendresponse(500,'Something went wrong when creating staff',false);
                exit;
            }
            $staff =['staffName' => $newstaff->StaffName,'id' => $newstaff->ID];
            sendresponse(200,null,true,$staff);
            exit;
        }
        elseif ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            
        }
        else{
            sendresponse(405, ['Invalid request method'],false);
            exit;
        }
    }

    
}