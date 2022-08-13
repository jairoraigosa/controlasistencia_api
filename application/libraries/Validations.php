<?php
if (!defined('BASEPATH')) exit('No direct script access allowed'); 
class Validations{
    public function __construct(){
        $this->CI = &get_instance();
    }
    
    public function valRequestMethod($req_meth){
        if($_SERVER['REQUEST_METHOD']!==$req_meth){
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array('trans'=>false,'msg'=>'Solo se aceptan los tipos de solicitudes '.$req_meth.'.'));
            return false;
        }
        return true;
    }
}