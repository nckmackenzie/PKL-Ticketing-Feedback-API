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

        }
        elseif($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            $data = [];
            $customers = $this->clientmodel->GetClients();
            foreach($customers as $customer)
            {
                array_push($data,[
                    'id' => $customer->ID,
                    'customerName' => $customer->ClientName
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
}