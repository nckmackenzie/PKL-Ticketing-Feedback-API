<?php

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