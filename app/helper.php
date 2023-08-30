<?php


if(!function_exists('hresponse')){
    function hresponse($status, $data, $msg=""){
        return response([
            'data' => $data,
            'isSuccess' => $status,
            'message' => $msg,
        ],200);
    }
}