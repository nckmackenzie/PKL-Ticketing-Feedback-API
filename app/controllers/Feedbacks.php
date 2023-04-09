<?php

class Feedbacks extends Controller
{
    private $feedbackmodel;
    public function __construct()
    {
        $this->feedbackmodel = $this->model('Feedback');
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
}