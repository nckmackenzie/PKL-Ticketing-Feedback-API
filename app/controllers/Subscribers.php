<?php

class Subscribers extends Controller
{
    public function __construct()
    {
        $this->subscribermodel = $this->model('Subscribe');
    }

    public function subscribe()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $fields = json_decode(file_get_contents('php://input'));
            $data = [
                'name' => isset($fields->name) && !empty(trim($fields->name)) ? ucwords(trim($fields->name)) : null,
                'email' => isset($fields->email) && !empty(trim($fields->email)) ? strtolower(trim($fields->email)) : null
            ];

            //validate
            if(is_null($data['name'] || is_null($data['email']))){
                sendresponse(400,'Provide all required fields',false);
                exit;
            }

            if(!validateemail($data['email'])){
                sendresponse(400,'Provide a valid email address',false);
                exit;
            }

            //if email exists
            if(!$this->subscribermodel->CheckSubscriber($data['email']))
            {
                sendresponse(400,'You are already in our mailing list.👍',false);
                exit;
            }

            if(!$this->subscribermodel->Create($data))
            {
                sendresponse(500,'Unable to enjoin you to our mailing list. Try again later',false);
                exit;
            }

            sendresponse(200,'Thank you for joining our mail list.✋',true);
            exit;


        }
        elseif ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            
        }
        else
        {
            sendresponse(405, ['Invalid request method'],false);
            exit;
        }
    }
}