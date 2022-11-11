<?php

class Cases extends Controller
{
    public function __construct()
    {
        validatetoken();
        $this->casemodel = $this->model('Cas');
    }

    public function index()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $fields = json_decode(file_get_contents('php://input')); //decode json data
            $data = [
                'subject' => isset($fields->subject) && !empty(trim($fields->subject)) ? strtolower(trim($fields->subject)) : null,
                'priority' => isset($fields->priority) && !empty(trim($fields->priority)) ? strtolower(trim($fields->priority)) : null,
                'staff' => isset($fields->staff) && !empty(trim($fields->staff)) ? (int)trim($fields->staff) : null,
                'narration' => isset($fields->description) && !empty(trim($fields->description)) ? strtolower(trim($fields->description)) : null,
                'status' => isset($fields->status) && !empty(trim($fields->status)) ? strtolower(trim($fields->status)) : null,
            ];
            //validate
            if(is_null($data['subject']) || is_null($data['priority']) || is_null($data['staff']) || is_null($data['narration']) || is_null($data['status'])) :
                sendresponse(400,'Fill all required fields',false);
                exit;
            endif;
            
            $results = $this->casemodel->Create($data);
            if(!$results):
                sendresponse(500,'Something went wrong while creating the case',false);
                exit;
            endif;

            sendresponse(200,'Created successfully',true);
            exit;


        }
        elseif ($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            
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