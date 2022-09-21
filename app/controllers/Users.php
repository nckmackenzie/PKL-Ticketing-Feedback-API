<?php

class Users extends Controller {

    private $userid;
    public function __construct()
    {
        if(!getjwtdetails()){
            sendresponse(401,['Invalid or no access token provided'],false);
            exit;
        }
        if(time() > getjwtdetails()[0]):
            sendresponse(401,['Token has expired! Please Log In again'],false);
            exit;
        endif;
        $this->userid = (int)getjwtdetails()[1];
        $this->usermodel = $this->model('User');
    }

    public function getuserdetails()
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            $user = $this->usermodel->getuser($this->userid);
            if(!$user) :
                sendresponse(404,'User not found!',false);
                exit;
            endif;
            $data = [
                'id' => $user->ID,
                'userName' => strtoupper($user->user_name),
                'email' => $user->email,
                'contact' => $user->contact,
                'userTypeId' => $user->user_type_id,
            ];
            sendresponse(200, null,true,$data);
            exit;

        }elseif ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            
        }else{
            sendresponse(405, ['Invalid request method'],false);
            exit;
        }
    }

    public function updateprofile()
    {
        if($_SERVER['REQUEST_METHOD'] === 'PUT'){
            // if(!validatejson()) exit();
            $data = json_decode(file_get_contents('php://input'));
            $messages = [];

            if (!isset($data->name) || strlen(trim($data->name)) === 0) {
                array_push($messages,'Please enter full name');
            } 
            if (!isset($data->email) || strlen(trim($data->email)) === 0) {
                array_push($messages,'Please provide your email address');
            }
            if(isset($data->email) && strlen(trim($data->email)) > 0 
               && !$this->usermodel->checkexists('email',$this->userid,$data->email)){
                array_push($messages,'Email address already registered'); 
            }
            if(isset($data->contact) && strlen(trim($data->contact)) > 0 
               && !$this->usermodel->checkexists('contact',$this->userid,$data->contact)){
                array_push($messages,'Phone no address already registered'); 
            }

            if(count($messages) > 0){
                sendresponse(400,$messages,false);
                exit();
            }

            $user = $this->usermodel->updateprofile($data,$this->userid);
            if(!$user) :
                sendresponse(500,['Unable to update your profile! Retry or report to admin'],false);
                exit;
            endif;

            $data = [
                'id' => $user->ID,
                'userName' => $user->user_name,
                'email' => $user->email,
                'contact' => $user->contact,
            ];
            sendresponse(200,'Updated successfully',true,$data);
            exit;

        }elseif ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            
        }else{
            sendresponse(405, ['Invalid request method'],false);
            exit;
        }
    }
}