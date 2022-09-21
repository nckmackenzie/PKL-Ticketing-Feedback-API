<?php

class Auth extends Controller
{
    private $jwt;
    public function __construct()
    {
        $this->authmodel = $this->model('Auths');
        $this->jwt = new JwtAuth;
    }

    public function index()
    {
        sendresponse(404,'You are trying to access an invalid route!',false);
        exit;
    }
    
    public function login()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
           
            // if(!validatejson()) exit();

            $data = json_decode(file_get_contents('php://input'));
            $messages = [];
            //validation
            if(!isset($data->email) || strlen(trim($data->email)) === 0){
                array_push($messages,'Enter your email address');
            }
            if(!isset($data->password) || strlen(trim($data->password)) === 0){
                array_push($messages,'Enter your password');
            }

            senderrorresponse($messages);
            if($this->authmodel->checkemailexists('',$data->email)) :
                sendresponse(401,['Invalid email or password'],false);
                exit();
            endif;
            $logged_user = $this->authmodel->login($data);
            if(!$logged_user) :
                sendresponse(401,['Invalid email or password'],false);
                exit();
            endif;

            $data = [
                'id' => $logged_user->ID,
                'user_name' => $logged_user->user_name,
                'email' => $logged_user->email,
                'user_type' => $logged_user->user_type_id,
                'contact' => $logged_user->contact,
            ];
            sendresponse(200,'Login successfully',true,$data,'jwt',$this->jwt->getjwt($logged_user->ID,$logged_user->user_type_id));

        }elseif($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
            
        }else{
            sendresponse(405,'Invalid request method',false);
            exit(1);
        }
    }

    public function register()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
           
            // if(!validatejson()) exit();

            $data = json_decode(file_get_contents('php://input'));

            $messages = [];

            //validation
            if (!isset($data->user_name) || strlen(trim($data->user_name)) === 0) {
                array_push($messages,'Please enter full name');
            } 
            if (!isset($data->email) || strlen(trim($data->email)) === 0) {
                array_push($messages,'Please provide your email address');
            }
            if (!isset($data->password) || strlen(trim($data->password)) === 0) {
                array_push($messages,'Please provide your password');
            }
            if (!isset($data->confirm_password) || strlen(trim($data->confirm_password)) === 0) {
                array_push($messages,'Please provide confirm password');
            }
            if(isset($data->password) && strlen(trim($data->password)) > 0 && isset($data->confirm_password)
               && strlen(trim($data->confirm_password)) > 0 && strcmp($data->password,$data->confirm_password) !== 0){
                array_push($messages,'Passwords don\'t match');
            }
            if (!isset($data->user_type) || strlen(trim($data->user_type)) === 0) {
                array_push($messages,'Please provide user type');
            }
            if(isset($data->email) && !empty($data->email) && !validateemail($data->email)) {
                array_push($messages,'Invalid email address provided');
            }
            if(isset($data->email) && !empty($data->email) && !$this->authmodel->checkemailexists('',$data->email)) {
                array_push($messages,'Email address already exists');
            }

            if(count($messages) > 0){
                sendresponse(400,$messages,false);
                exit();
            }

            $row = $this->authmodel->register($data);

            if(!$row){
                sendresponse(500,'Unable to register user! Retry or contact the administrator',false);
                exit();
            }
            
            $user = [
                'id' => $row->ID,
                'user_name' => $row->user_name,
                'email' => $row->email,
                'contact' => $row->contact,
                'created_at' => $row->created_at,
            ];

            sendresponse(200,'User created successfully!',true,$user);
            exit();

        }elseif($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
            
        }else{
            sendresponse(405,'Invalid request method',false);
            exit(1);
        }
    }
}