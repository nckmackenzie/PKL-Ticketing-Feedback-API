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