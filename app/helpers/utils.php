<?php
include 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function sendresponse($status,$message=null,$success,$data = [],$optionalkey = '',$optionalval = ''){
    http_response_code($status);
    $results = [
        'success' => $success,
        'message' => $message,
        'data' => $success ? $data : null,
        $optionalkey => $optionalval
    ];
    unset($results['']);
    echo json_encode($results);
}

function validatejson()
{
    if($_SERVER['HTTP_CONTENT_TYPE'] !== 'application/json'){
        sendresponse(400,'Content type header not set to json',false);
        return false;
    }else{
        return true;
    }
}

function validateemail($email){
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        return false;
    }else{
        return true;
    }
}

function checkexists($con,$table,$field,$id,$param)
{
    $sql = 'SELECT COUNT(*) FROM '.$table.' WHERE (Deleted = 0) AND (ID <> :id) AND '.$field.' = :param';
    $stmt = $con->prepare($sql);
    $stmt->bindValue(':id',$id,PDO::PARAM_INT);
    $stmt->bindValue(':param',$param,PDO::PARAM_STR);
    $stmt->execute();
    if((int)$stmt->fetchColumn() !== 0) :
      return false;
    else:
      return true;
    endif;
}

function returninsertedrow($con,$table,$id){
    $sql = 'SELECT * FROM '.$table.' WHERE ID = :id';
    $stmt = $con->prepare($sql);
    $stmt->bindValue(':id',$id,PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
}

function senderrorresponse($messagearr){
    if(count($messagearr) > 0){
        sendresponse(400,$messagearr,false);
        exit();
    }
}

function decodejwt($jwt)
{
    try {
        $decoded = JWT::decode($jwt, new Key(JWT_KEY, 'HS256'));
        return $decoded;
    } catch (\Throwable $th) {
        sendresponse(401,$th->getMessage(),false);
        return false;
    }
    
}

function getjwtdetails(){
    if($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
        exit;
    }
    if (!isset($_SERVER['HTTP_AUTHORIZATION']) || strlen($_SERVER['HTTP_AUTHORIZATION']) < 1) {
        return false;
    }

    $accesstoken = explode(" ",$_SERVER['HTTP_AUTHORIZATION'])[1];
    if(!decodejwt($accesstoken)) exit;
    $details = decodejwt($accesstoken);
    return [$details->exp,$details->uid];
}

function getsingle($con,$table,$value){
    $sql = 'SELECT * FROM '.$table.' WHERE ID = :id';
    $stmt = $con->prepare($sql);
    $stmt->bindValue(':id', $value,PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
}

function validatetoken()
{
    if(!getjwtdetails()){
        sendresponse(401,['Invalid or no access token provided'],false);
        exit;
    }
    if(time() > getjwtdetails()[0]):
        sendresponse(401,['Token has expired! Please Log In again'],false);
        exit;
    endif;
}

//load result set
function loadresultset($con,$sql,$arr){
    $stmt = $con->prepare($sql);
    $stmt->execute($arr);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

//Get value from Database
function getdbvalue($con,$sql,$arr){
    $stmt = $con->prepare($sql);
    $stmt->execute($arr);
    return $stmt->fetchColumn();
}