<?php

class Deliveries extends Controller
{
    private $deliverymodel;

    public function __construct()
    {
        validatetoken();
        $this->deliverymodel = $this->model('Delivery');
    }

    public function index()
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET')
        {

        }
        elseif($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $fields = json_decode(file_get_contents('php://input'));
            $data = [
                'deliverydate' => isset($fields->deliveryDate) && !empty(trim($fields->deliveryDate)) ? date('Y-m-d',strtotime($fields->deliveryDate)) : null,
                'did' => $this->deliverymodel->GenerateUniqueId(),
                'client' => isset($fields->client) && !empty(trim($fields->client)) ? (int)trim($fields->client) : null,
                'time' => isset($fields->deliveryTime) && !empty(trim($fields->deliveryTime)) ? date('h:i',strtotime($fields->deliveryTime)) : null,
                'location' => isset($fields->location) && !empty(trim($fields->location)) ? strtolower(trim($fields->location)) : null,
                'notes' => isset($fields->notes) && !empty(trim($fields->notes)) ? strtolower(trim($fields->notes)) : null,
            ];

            if(is_null($data['deliverydate']) || is_null($data['client']) || is_null($data['location'])){
                sendresponse(400,['Fill all required fields'],false);
                exit;
            }

            if(!$this->deliverymodel->Create($data)){
                sendresponse(500,['Unable to save delivery. Try again later!'],false);
                exit;
            }

            sendresponse(201,'Success',true);
            exit;
            // sendresponse(200,'success',true,$data);
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