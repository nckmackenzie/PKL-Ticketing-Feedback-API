<?php

class Feedbacks extends Controller
{
    private $feedbackmodel;
    private $deliverymodel;
    public function __construct()
    {
        $this->feedbackmodel = $this->model('Feedback');
        $this->deliverymodel = $this->model('Delivery');
    }

    public function index()
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            validatetoken();
            $data = [];
            $deliveries = $this->feedbackmodel->GetDeliveries();
            
            foreach($deliveries as $delivery):
                array_push($data,[
                    'id' => $delivery->ID,
                    'client' => ucwords($delivery->ClientName),
                    'date' => date('d/m/Y',strtotime($delivery->DeliveryDate)),
                    'ratings' => number_format($delivery->AverageRating,1)
                ]);
            endforeach;

            sendresponse(200, 'Success', true, $data);
            exit;
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

    public function sendfeedbacksms()
    {
        validatetoken();
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $fields = json_decode(file_get_contents('php://input'));
            $deliveryId = isset($fields->deliveryId) && !empty(trim($fields->deliveryId)) ? (int)trim($fields->deliveryId) : null;

            if(is_null($deliveryId)){
               sendresponse(400,['Select delivery'],false);
               exit;
            }

            if(!$this->feedbackmodel->DeliveryFound($deliveryId)){
               sendresponse(404,['Selected delivery not found'],false) ;
               exit;
            }

            $details = $this->feedbackmodel->GetDeliveryDetails($deliveryId);
            $dateformatted = date('d-m-Y',strtotime($details[1]));
            $uniqueid = $details[2];


            $message = "Greetings. We would love to hear from you on the recent delivery we made on {$dateformatted}.Click on the provided link to share your feedback.\n https://feedback.panesar.co.ke/?did={$uniqueid}";
            $result = sendmessage($details[0],$message);
            $status = $result['status'];
            $this->deliverymodel->UpdateNotificationStatus($status,$uniqueid,'feedback');
            sendresponse(201,'Success',true);
            exit;
            
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

    public function submitfeedback()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $fields = json_decode(file_get_contents('php://input'));
            
            $data = [
                'did' => isset($fields->did) && !empty(trim($fields->did)) ? trim($fields->did) : null,
                'knowabout' =>  isset($fields->knowAbout) && !empty(trim($fields->knowAbout)) ? trim($fields->knowAbout) : null,
                'quality' =>  isset($fields->quality) && !empty(trim($fields->quality)) ? (int)trim($fields->quality) : null,
                'recommend' =>  isset($fields->recommend) && !empty(trim($fields->recommend)) ? (int)trim($fields->recommend) : null,
                'repurchase' =>  isset($fields->repurchase) && !empty(trim($fields->repurchase)) ? (int)trim($fields->repurchase) : null,
                'service' =>  isset($fields->service) && !empty(trim($fields->service)) ? (int)trim($fields->service) : null,
                'duration' =>  isset($fields->duration) && !empty(trim($fields->duration)) ? (int)trim($fields->duration) : null,
                'howlong' =>  isset($fields->howLong) && !empty(trim($fields->howLong)) ? trim($fields->howLong) : null,
                'pendingwork' =>  isset($fields->pendingWork) && !empty(trim($fields->pendingWork)) ? (trim($fields->pendingWork) === 'yes' ? 1 : 0 ) : null,
                'pendingexplained' =>  isset($fields->pendingArea) && !empty(trim($fields->pendingArea)) ? trim($fields->pendingArea) : null,
                'damages' =>  isset($fields->damages) && !empty(trim($fields->damages)) ? (trim($fields->damages) === 'yes' ? 1 : 0 ) : null,
                'damagesexplained' =>  isset($fields->damagesArea) && !empty(trim($fields->damagesArea)) ? trim($fields->damagesArea) : null,
                'additional' =>  isset($fields->additionalComments) && !empty(trim($fields->additionalComments)) ? trim($fields->additionalComments) : null,
            ];

            if(is_null($data['knowabout']) || is_null($data['quality']) || is_null($data['recommend']) 
               || is_null($data['repurchase']) || is_null($data['service']) || is_null($data['duration']) 
               || is_null($data['howlong']) || is_null($data['pendingwork']) || is_null($data['damages'])){

               sendresponse(400,['Fill/Select all required entries'],false) ;
               exit;
            }

            if($data['damages'] === 0){
                $data['damagesexplained'] = null;
            }
            if($data['pendingwork'] === 0){
                $data['pendingexplained'] = null;
            }

            // validate did
            if(is_null($data['did']) || !$this->feedbackmodel->CheckUniqueId($data['did'])){
                sendresponse(400,['Unable to get delivery for rating. Select link from message and try again'],false) ;
                exit;
            }

            if(!$this->feedbackmodel->Create($data)){
               sendresponse(500,['Unable to submit feedback. Try again!'],false) ;
               exit;
            }

            sendresponse(200,'Success!',true) ;
            exit;

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

    public function checkfeedback()
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            $uniqueid = isset($_GET['did']) && !empty(trim($_GET['did'])) ? trim($_GET['did']) : null;

            if(is_null($uniqueid) || !$this->feedbackmodel->GetDelivery($uniqueid)){
                sendresponse(404,['Unable to get this delivery information'],false);
                exit;
            }

            if($this->feedbackmodel->GetDelivery($uniqueid) === 0){
                sendresponse(404,'Feedback not received',false) ;
                exit;
            }
            sendresponse(200,'Feedback already done!',true) ;
            exit;
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