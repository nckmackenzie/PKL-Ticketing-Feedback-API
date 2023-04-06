<?php

class Clients extends Controller
{
    private $clientmodel;
    public function __construct()
    {
        validatetoken();
        $this->clientmodel = $this->model('Client');
    }

    public function index()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $messages = [];
            $fields = json_decode(file_get_contents('php://input'));
            $data = [
                'customername' => isset($fields->customerName) && !empty(trim($fields->customerName)) ? trim($fields->customerName) : null,
                'contact' => isset($fields->contact) && !empty(trim($fields->contact)) ? '254'. substr(trim($fields->contact),1) : null,
            ];

            
            //validate
            if(is_null($data['customername'])){
                array_push($messages,'Provide client name');
            }
            if(is_null($data['contact'])){
                array_push($messages,'Provide client contact');
            }

            if(count($messages) > 0){
                sendresponse(400,$messages,false);
                exit();
            }

            $newclient = $this->clientmodel->Create($data);
            if(!$newclient){
                sendresponse(500,'Something went wrong when creating staff',false);
                exit;
            }
            $client =['customerName' => ucwords($newclient->ClientName),'id' => $newclient->ID];
            sendresponse(200,null,true,$client);
            exit;

        }
        elseif($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            $data = [];
            $customers = $this->clientmodel->GetClients();
            foreach($customers as $customer)
            {
                array_push($data,[
                    'id' => $customer->ID,
                    'customerName' => ucwords($customer->ClientName)
                ]);
            }
            sendresponse(200,null,true,$data);
            exit;
        }
        elseif($_SERVER['REQUEST_METHOD'] === 'OPTIONS')
        {
            
        }
        else
        {
            sendresponse(405, 'Invalid request method',false);
            exit;
        }
    }

    public function getcontact()
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            $id = isset($_GET['id']) && !empty(trim($_GET['id'])) ? (int)trim($_GET['id']) : null;
            if(is_null($id)){
                sendresponse(400,['Client not selected'],false);
                exit;
            }

            $contact = $this->clientmodel->GetContact($id);

            sendresponse(200,null,true,$contact);
            exit;
        }
        elseif($_SERVER['REQUEST_METHOD'] === 'OPTIONS')
        {
            
        }
        else
        {
            sendresponse(405, 'Invalid request method',false);
            exit;
        }
    }
}