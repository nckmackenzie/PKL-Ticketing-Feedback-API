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

}