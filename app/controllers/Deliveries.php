<?php
// require_once 'SendMessage.php';

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
                'contact' => isset($fields->contact) && !empty(trim($fields->contact)) ? (int)trim($fields->contact) : null,
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
            $dateformated = date('d-m-Y',strtotime($data['deliverydate']));
            // $link = 'https://feedback.panesar.co.ke/?did='.$data['did'];
            // $message = "We would love to hear from you on the recent delivery we made on {$dateformated}. Click on the provided link to share your feedback.\n {$link}" ;
            $message = "Greetings. Please note we have scheduled a delivery for your products on {$dateformated}.Thank you for your business.";
            $result = sendmessage($data['contact'],$message);
            $status = $result['status'];
            $this->deliverymodel->UpdateNotificationStatus($status,$data['did']);
            sendresponse(201,'Success',true);
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

    public function getlatestdeliveries()
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            $deliveries = $this->deliverymodel->GetLatestDeliveries();
            $data = [];
            foreach($deliveries as $delivery):
                array_push($data,[
                    'id' => (int)$delivery->ID,
                    'clientName' => ucwords($delivery->ClientName),
                    'deliveryDate' => date('d-m-Y',strtotime($delivery->DeliveryDate)),
                    'location' => ucwords($delivery->Location),
                    'deliverySMS' => ucwords($delivery->NotificationStatus),
                    'feedbackSMS' => is_null($delivery->FeedbackNotificationStatus) ? 'Not Sent' : ucwords($delivery->FeedbackNotificationStatus) 
                ]);
            endforeach;

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
    public function getdeliverydetails()
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            $id = isset($_GET['id']) && !empty(trim($_GET['id'])) ? (int)trim($_GET['id']) : null;
            if(is_null($id)){
                sendresponse(400,['Unable to get selected delivery'],false);
                exit;
            }

            $delivery = $this->deliverymodel->GetDeliveryDetails($id);
            $feedback = $this->deliverymodel->GetFeedbackDetails($id);

            if(!$delivery){
                sendresponse(404,['Delivery not found'],false);
                exit;
            }

            $data = [
                'deliveryDetails' => [
                    'client' => ucwords($delivery->ClientName),
                    'date' => date('d-m-Y',strtotime($delivery->DeliveryDate)),
                    'time' => date('h:i',strtotime($delivery->DeliveryTime)),
                    'location' => ucwords($delivery->Location),
                    'notes' => !is_null($delivery->Notes) ? ucfirst($delivery->Notes) : null, 
                    'delivery SMS' => ucwords($delivery->NotificationStatus),
                    'feedback SMS' => is_null($delivery->FeedbackNotificationStatus) ? 'Not Sent' : ucwords($delivery->FeedbackNotificationStatus) 
                ],
                'feedbackDetails' => [
                    'submitted' => !$feedback ? false : true
                ]
            ];

            if($feedback)
            {
                $data['feedbackDetails']['knowAbout'] = ucwords($feedback->KnowAbout);    
                $data['feedbackDetails']['duration'] = (int)$feedback->Duration; 
                $data['feedbackDetails']['quality'] = (int)$feedback->Quality;  
                $data['feedbackDetails']['service'] = (int)$feedback->Service;  
                $data['feedbackDetails']['repurchase'] = (int)$feedback->Repurchase;  
                $data['feedbackDetails']['recommend'] = (int)$feedback->Recommend;  
                $data['feedbackDetails']['durationAsCustomer'] = ucfirst($feedback->DurationAsCustomer);  
                $data['feedbackDetails']['damages'] = boolval($feedback->damages);  
                $data['feedbackDetails']['damagesExplained'] = !is_null($feedback->DamagesExplained) ? ucfirst($feedback->DamagesExplained) : null;  
                $data['feedbackDetails']['pendingWork'] = boolval($feedback->PendingWork);  
                $data['feedbackDetails']['pendingWorkExplained'] = !is_null($feedback->PendingWorkExplained) ? ucfirst($feedback->PendingWorkExplained) : null;
                $data['feedbackDetails']['suggestions'] = !is_null($feedback->Suggestions) ? ucfirst($feedback->Suggestions) : null;  
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