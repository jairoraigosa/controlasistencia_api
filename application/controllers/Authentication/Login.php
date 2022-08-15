<?php
require APPPATH . '/libraries/CreatorJwt.php';

class Login extends CI_Controller
{
    public function __construct(){
        parent::__construct();
        header('Content-Type: application/json');
        $this->load->model('authentication/login_model');
    }

    /**
     * Funcion para autenticar al usuario a través de la api (inicio de sesión)
     * @global array $_POST Se deben enviar en los parametros de la petición el username y el password del estudiante para autenticar
     */
    public function authentication(){
        if(!$this->validations->valRequestMethod('POST')){return false;}
        if(!isset($_POST['username']) || !isset($_POST['password'])){
            $rst = array(
                'status' => false,
                'message' => 'Parámetros incorrectos'
            );
        }else{
            $user = $this->login_model->getUsuario($_POST['username'],$_POST['password']);
            if($user){
                $jwtToken = $this->creatorjwt->GenerateToken($user);
                $rst = array(
                    'status' => true,
                    'message' => 'Autenticación exitosa.',
                    'data' => array(
                        'login_token' => $jwtToken,
                        'data_alumno' => $user
                    ),
                );
            }else{
                $rst = array(
                    'status' => false,
                    'message' => 'Error en la autenticación.'
                );
            }
        }
        echo json_encode($rst);
    }
    
    function check_token(){
        if(!$this->validations->valRequestMethod('GET')){return false;}//validacion del tipo de solicitud, solo se aceptan tipos de solicitud get
        $data_token = $this->creatorjwt->ValidateToken();
        if(!$data_token){die();}//validacion del token de inicio de sesión si es invalido la funcion devolvera una respuesta de no autenticado por lo tanto muere la ejecución
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data_token);
    }
}
    
