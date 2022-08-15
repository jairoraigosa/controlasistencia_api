<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
class Empleados extends REST_Controller{
    public function __construct(){
        parent::__construct();
        if(!$this->creatorjwt->ValidateToken()){die();}//Validacion del token de sesión
        $this->load->model('empleados/empleados_model');
    }
    /**
     * Funcion para consultar los empleados registrados
     * @param integer $empleado_id Id del empleado a consultar (opcional)
     * @global integer $_GET['cedula'] Numero de cedula del empleado a consultar (opcional)
     * @global string $_GET['cargo'] Cargo del empleado (opcional)
     * @global integer $_GET['edad_minima'] Edad minima del empleado a consultar (opcional)
     * @global integer $_GET['edad_maxima'] Edad maxima del empleado a consultar (opcional)
     */
    function index_get($empleado_id=false){
        if(!$this->validations->valRequestMethod('GET')){return false;}
        $this->forms->params = $this->get();
        $cedula = $this->forms->getParam('cedula');
        $cargo = $this->forms->getParam('cargo');
        $edad_minima = $this->forms->getParam('edad_min');
        $edad_maxima = $this->forms->getParam('edad_max');
        $rst['data'] = $this->empleados_model->getEmpleados($empleado_id, $cedula, $cargo, $edad_minima, $edad_maxima);
        if($rst['data']!==false){
            $rst['trans'] = true;
            $rst['msg'] = 'Información generada exitosamente.';
        }else{
            $rst['trans'] = false;
            $rst['msg'] = 'Se ha presentado un error al intentar generar la información.';
        }
        $this->response($rst, REST_Controller::HTTP_OK);
    }
    
    /**
     * Funcion para registrar los nuevos empleados
     * @param string $_POST['nombres'] nombres del empleado
     * @param string $_POST['apellidos'] apellidos del empleado
     * @param number $_POST['edad'] edad del empleado
     * @param string $_POST['cargo'] cargo que ocupa del empleado
     * @param number $_POST['cedula'] numero de cedula del empleado
     * @param number $_POST['telefono'] numero de telefono del empleado
     */
    function index_post(){
        if(!$this->validations->valRequestMethod('POST')){return false;}
        $valForm=true;
        $formData = $this->forms->params = $this->post();
        if(!$this->forms->valString('nombres', true, 5, 100)){
            $valForm = false;
        }
        if(!$this->forms->valString('apellidos', true, 5, 100)){
            $valForm = false;
        }
        if(!$this->forms->valInteger('edad', true, 1, 2)){
            $valForm = false;
        }
        if(!$this->forms->valString('cargo', true, 5, 100)){
            $valForm = false;
        }
        if(!$this->forms->valInteger('cedula', true, 7, 20)){
            $valForm = false;
        }
        if(!$this->forms->valInteger('no_celular', true, 7, 10)){
            $valForm = false;
        }
        if($valForm===true){
            $empleado = $this->empleados_model->getEmpleados(false,$formData['cedula']);
            if($empleado){
                $rst = array(
                    'trans' => false,
                    'msg' => 'El número de cédula ya existe para otro empleado.',
                );
            }else{
                $rst['trans'] = $this->empleados_model->regEmpleado($formData);
                $rst['msg'] = $rst['trans']===true ? 'Empleado registrado exitosamente.' : 'Error al intentar registrar el empleado.';
            }
        }else{
            $rst = array(
                'trans' => false,
                'msg' => 'Error en los parámetros enviados para el registro',
                'errors' => $this->forms->getMsg()
            );
        }
        $this->response($rst, REST_Controller::HTTP_OK);
    }
    
    /**
     * Funcion para actualizar al empleado
     * @param integer $empleado_id id del empleado que se va a actualizar (obligatorio)
     * @global string PUT['nombres'] nueva descripcion del comentario
     * @global string PUT['apellidos'] nueva descripcion del comentario
     * @global integer PUT['edad'] nuevo lik del comentario
     * @global string PUT['cargo'] nueva descripcion del comentario
     * @global integer PUT['cedula'] nuevo dis del comentario
     * @global integer PUT['telefono'] nuevo id del alumno rama relacionado al comentario
     */
    function index_put($empleado_id=false){
        if(!$this->validations->valRequestMethod('PUT')){return false;}
        if($empleado_id===false){
            $rst['trans'] = false;
            $rst['msg'] = 'Debe proporcionar el id del empleado que desea actualizar.';
            $this->response($rst, REST_Controller::HTTP_OK);
            return false;
        }
        $dataForm = $this->forms->params = is_array($this->put()) ? $this->put() : [];
        $valForm=true;
        $dataUpd = [];
        if(isset($dataForm['nombres'])){
            if(!$this->forms->valString("nombres", true, 5, 100)){
                $valForm=false;
            }else{
                $dataUpd['nombres'] = $dataForm['nombres'];
            }
        }
        if(isset($dataForm['apellidos'])){
            if(!$this->forms->valString("apellidos", true, 5, 100)){
                $valForm=false;
            }else{
                $dataUpd['apellidos'] = $dataForm['apellidos'];
            }
        }
        if(isset($dataForm['edad'])){
            if(!$this->forms->valInteger("edad", true, 1, 2)){
                $valForm=false;
            }else{
                $dataUpd['edad'] = $dataForm['edad'];
            }
        }
        if(isset($dataForm['cargo'])){
            if(!$this->forms->valString("cargo", true, 5, 100)){
                $valForm=false;
            }else{
                $dataUpd['cargo'] = $dataForm['cargo'];
            }
        }
        if(isset($dataForm['cedula'])){
            if(!$this->forms->valInteger("cedula", true, 1, 20)){
                $valForm=false;
            }else{
                $dataUpd['cedula'] = $dataForm['cedula'];
            }
        }
        if(isset($dataForm['no_celular'])){
            if(!$this->forms->valInteger("no_celular", true, 1, 20)){
                $valForm=false;
            }else{
                $dataUpd['no_celular'] = $dataForm['no_celular'];
            }
        }
        if(empty($dataUpd)){
            $rst['trans'] = false;
            $rst['msg'] = "Debe proporcionar los parametros de los campos a modificar.";
        }else if($valForm){
            $empleado = $this->empleados_model->existeEmpleado($empleado_id,$dataForm['cedula']);
            if($empleado){
                $rst = array(
                    'trans' => false,
                    'msg' => 'El número de cédula ya existe para otro empleado.',
                );
            }else{
                $rst['trans'] = $this->empleados_model->actuEmpleado($empleado_id, $dataUpd);
                if($rst['trans']!==false){
                    $rst['msg'] = 'Empleado actualizado exitosamente.';
                }else{
                    $rst['msg'] = 'Se ha presentado un error intentando actualizar el empleado, por favor, vuelva a intentarlo.';
                }
            }
        }else{
            $rst['trans'] = false;
            $rst['msg'] = "Error en la actualización, parámetros incorrectos.";
            $rst['errors'] = $this->forms->getMsg();
        }
        $this->response($rst, REST_Controller::HTTP_OK);
    }
}