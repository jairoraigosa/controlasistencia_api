<?php 
//application/libraries/CreatorJwt.php
require APPPATH . '/libraries/JWT.php';
class CreatorJwt{
   /*************This function generate token private key**************/ 
    PRIVATE $key = "C0ntr0lAs1st3nc14123456"; 
    public function __construct(){
        $this->CI = &get_instance();
    }
    public function GenerateToken($data){          
        $jwt = JWT::encode($data, $this->key);
        return $jwt;
    }
    /*************This function DecodeToken token **************/

    public function DecodeToken($token){          
        $decoded = JWT::decode($token, $this->key, array('HS256'));
        $decodedData = (array) $decoded;
        return $decodedData;
    }
    
    public function ValidateToken(){
        $received_Token = $this->CI->input->request_headers('Authorization');
        if(!isset($received_Token['token'])){
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array('status' => false,'message' => 'No estas autorizado.'));
            return false;
        }else{
            try{
                $jwtData = $this->DecodeToken($received_Token['token']);
                return $jwtData;
            }catch (Exception $e){
                http_response_code(401);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(array( "status" => false, "message" => $e->getMessage()));
                return false;
            }
        }
    }
}
