<?php

function sendresponse($status,$message=null,$success,$data = []){
    http_response_code($status);
    $results = [
        'success' => $success,
        'message' => $message,
        'data' => $success ? $data : null
    ];
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