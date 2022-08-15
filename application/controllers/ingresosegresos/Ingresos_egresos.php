<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
class Ingresos_egresos extends REST_Controller{
    public function __construct(){
        parent::__construct();
        if(!$this->creatorjwt->ValidateToken()){die();}//Validacion del token de sesión
        $this->load->model('ingresosegresos/ingresos_egresos_model');
        $this->load->model('empleados/empleados_model');
    }
    /**
     * Funcion para consultar los ingresos y egresos de los empleados
     * @param integer $ingreso_egreso_id id de la tarea a consultar (opcional)
     * @global integer $_GET['empleado_id'] Id del empleado al cual se le desean consultar los ingresos y egresos
     * @global integer $_GET['cedula'] cedula del empleado a consultar
     * @global integer $_GET['fecha_inicio'] fecha de inicio para una consulta en rango de fechas
     * @global integer $_GET['fecha_fin'] Fecha de finalizacion para una consulta en rango de fechas
     */
    function index_get($ingreso_egreso_id=false){
        if(!$this->validations->valRequestMethod('GET')){return false;}
        $this->forms->params = $this->get();
        $empleado_id = $this->forms->getParam('empleado_id');
        $cedula = $this->forms->getParam('cedula');
        $fecha_inicio = $this->forms->getParam('fecha_inicio');
        $fecha_fin = $this->forms->getParam('fecha_fin');
        $rst['data'] = $this->ingresos_egresos_model->getIngresosEgresos($ingreso_egreso_id, $empleado_id, $cedula, $fecha_inicio, $fecha_fin);
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
     * Funcion para registrar los nuevos ingresos/egresos de los empleados
     * @param string $_POST['cedula'] cedula del empleado a ser consultado
     * @param string $_POST['fecha_ingreso'] Fecha de ingreso del empleado
     * @param number $_POST['fecha_egreso'] Fecha de egreso del empleado
     */
    function index_post(){
        if(!$this->validations->valRequestMethod('POST')){return false;}
        $valForm=true;
        $formData = $this->forms->params = $this->post();
        if(!$this->forms->valInteger('cedula', true, 7, 20)){
            $valForm = false;
        }
        if(!$this->forms->valString('fecha_ingreso', true)){
            $valForm = false;
        }
        if(trim($formData['fecha_egreso'])===''){
            $formData['fecha_egreso'] = NULL;
        }
        if($valForm===true){
            $empleado = $this->empleados_model->getEmpleados(false,$formData['cedula']);
            if(!$empleado){
                $rst = array(
                    'trans' => false,
                    'msg' => 'No existe un empleado con este número de cédula.',
                );
            }else if($this->ingresos_egresos_model->getIngresoSinEgreso($empleado[0]['empleado_id'])){
                $rst = array(
                    'trans' => false,
                    'msg' => 'El empleado tiene un registro de ingreso sin registro de egreso, por favor registre su egreso para poder registrar un nuevo ingreso.',
                );
            }else{
                $rst['trans'] = $this->ingresos_egresos_model->regIngresoEgreso($formData);
                $rst['msg'] = $rst['trans']===true ? 'Ingreso/egreso registrado exitosamente.' : 'Error al intentar registrar el ingreso/egreso.';
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
     * Funcion para actualizar la informacion de ingresos y/o egresos
     * @param integer $ingreso_egreso_id id del ingreso_egreso al que se le va a actualizar la información (obligatorio)
     * @global string PUT['fecha_ingreso'] nueva fecha de ingreso del registro a actualizar
     * @global string PUT['fecha_egreso'] nueva fecha de egreso del registro a actualizar
     */
    function index_put($ingreso_egreso_id=false){
        if(!$this->validations->valRequestMethod('PUT')){return false;}
        if($ingreso_egreso_id===false){
            $rst['trans'] = false;
            $rst['msg'] = 'Debe proporcionar el id del ingreso/egreso a modificar.';
            $this->response($rst, REST_Controller::HTTP_OK);
            return false;
        }
        $dataForm = $this->forms->params = is_array($this->put()) ? $this->put() : [];
        $valForm=true;
        $dataUpd = [];
        if(isset($dataForm['fecha_ingreso'])){
            if(!$this->forms->valString("fecha_ingreso", true)){
                $valForm=false;
            }else{
                $dataUpd['fecha_ingreso'] = $dataForm['fecha_ingreso'];
            }
        }
        if(isset($dataForm['fecha_egreso'])){
            if(!$this->forms->valString("fecha_egreso", true)){
                $valForm=false;
            }else{
                $dataUpd['fecha_egreso'] = $dataForm['fecha_egreso'];
            }
        }
        if(empty($dataUpd)){
            $rst['trans'] = false;
            $rst['msg'] = "Debe proporcionar los parametros de los campos a modificar.";
        }else if($valForm){
            $rst['trans'] = $this->ingresos_egresos_model->actuIngresoEgreso($ingreso_egreso_id, $dataUpd);
            if($rst['trans']!==false){
                $rst['msg'] = 'Ingreso/egreso actualizado exitosamente.';
            }else{
                $rst['msg'] = 'Se ha presentado un error intentando actualizar el ingreso/egreso, por favor, vuelva a intentarlo.';
            }
        }else{
            $rst['trans'] = false;
            $rst['msg'] = "Error en la actualización, parámetros incorrectos.";
            $rst['errors'] = $this->forms->getMsg();
        }
        $this->response($rst, REST_Controller::HTTP_OK);
    }
}