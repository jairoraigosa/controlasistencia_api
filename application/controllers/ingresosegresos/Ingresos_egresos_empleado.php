<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
class Ingresos_egresos_empleado extends REST_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('ingresosegresos/ingresos_egresos_empleado_model');
        $this->load->model('ingresosegresos/ingresos_egresos_model');
        $this->load->model('empleados/empleados_model');
    }
    /**
     * Funcion para consultar los ingresos y egresos de los empleados
     * @param integer $ingreso_egreso_id id de la tarea a consultar (opcional)
     * @global integer $_GET['cedula'] cedula del empleado a consultar
     */
    function index_get($ingreso_egreso_id=false){
        if(!$this->validations->valRequestMethod('GET')){return false;}
        $this->forms->params = $this->get();
        $cedula = $this->forms->getParam('cedula');
        $empleado = $this->empleados_model->existeEmpleado(false, $cedula);
        if(!$empleado){
            $rst['trans'] = false;
            $rst['msg'] = 'No existe un empleado con este número de cédula.';
        }else{
            $rst['data'] = $this->ingresos_egresos_empleado_model->getIngresosSinEgresosEmpleado($ingreso_egreso_id, $cedula);
            if($rst['data']!==false){
                $rst['trans'] = true;
                $rst['msg'] = 'Información generada exitosamente.';
            }else{
                $rst['trans'] = false;
                $rst['msg'] = 'Se ha presentado un error al intentar generar la información.';
            }
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
                $rst['trans'] = $this->ingresos_egresos_empleado_model->regIngresoEgreso($formData);
                $rst['msg'] = $rst['trans']===true ? 'Ingreso registrado exitosamente.' : 'Error al intentar registrar el ingreso/egreso.';
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
            $rst['msg'] = 'Debe proporcionar el id del ingreso/egreso a actualizar.';
            $this->response($rst, REST_Controller::HTTP_OK);
            return false;
        }
        $rst['trans'] = $this->ingresos_egresos_empleado_model->actuEgreso($ingreso_egreso_id);
        if($rst['trans']!==false){
            $rst['msg'] = 'Egreso actualizado exitosamente.';
        }else{
            $rst['msg'] = 'Se ha presentado un error intentando actualizar el ingreso/egreso, por favor, vuelva a intentarlo.';
        }
        $this->response($rst, REST_Controller::HTTP_OK);
    }
}