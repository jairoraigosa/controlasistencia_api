<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Ingresos_egresos_model extends CI_Model{
    function __construct() {
        parent::__construct();
        $this->load->model('empleados/empleados_model');
    }
    /**
     * Funcion para consultar  en la base de datos
     * @param integer $ingreso_egreso_id id del ingreso_egreso (opcional)
     * @param integer $empleado_id id del empleado al cual se le desea realizar la consulta de ingresos y egresos
     * @param integer $cedula cedula del empleado a ser consultada
     * @param integer $fecha_inicio Fecha de inicio para una consulta en rango de fechas
     * @param integer $fecha_fin fecha de finalizacion para una consulta en rango de fechas
     * @return boolean|array() false en caso de encontrar un problema en la consulta o array de datos con el resultado de la consulta
     */
    function getIngresosEgresos($ingreso_egreso_id=false, $empleado_id=false, $cedula=false, $fecha_inicio=false, $fecha_fin=false){
        $this->db->select('a.ingreso_egreso_id,a.empleado_id,a.fecha_ingreso,DATE_FORMAT(a.fecha_ingreso, "%d-%m-%Y %H:%i") as fec_ing,a.fecha_egreso,DATE_FORMAT(a.fecha_egreso, "%d-%m-%Y %H:%i") as fec_eg,b.nombres,b.apellidos,b.edad,b.cargo,b.cedula');
        $this->db->from('ingresos_egresos as a');
        $this->db->join('empleados as b', 'b.empleado_id = a.empleado_id');
        if($ingreso_egreso_id!==false){
            $this->db->where('a.ingreso_egreso_id', $ingreso_egreso_id);
        }
        if($empleado_id!==false){
            $this->db->where('a.empleado_id', $empleado_id);
        }
        if($cedula!==false){
            $this->db->where('b.cedula', $cedula);
        }
        if($fecha_inicio!==false){
            if($fecha_fin===false){
                $fecha_fin = date('Y-m-d H:i:s');
            }
            $this->db->where("((a.fecha_ingreso BETWEEN '$fecha_inicio 00:00:00' and '$fecha_fin 23:59:59') OR (a.fecha_egreso BETWEEN '$fecha_inicio 00:00:00' and '$fecha_fin 23:59:59'))");
        }
        $data = $this->db->get();
        if($data!==false){
            return $data->result_array();
        }
        return false;
    }
    
    /**
     * Funcion para regisrar un nuevo ingreso/egreso de un empleado en específico
     * @param array() $dataIngEg Array de datos con la informacion del ingreso/egreso
     */
    function regIngresoEgreso($dataIngEg){
        $empleado_id = $this->empleados_model->getEmpleados(false,$dataIngEg['cedula'])[0]['empleado_id'];
        return $this->db->insert('ingresos_egresos', array('empleado_id'=>$empleado_id,'fecha_ingreso'=>$dataIngEg['fecha_ingreso'],'fecha_egreso'=>$dataIngEg['fecha_egreso']));
    }
    
    
    
    /**
     * Funcion para actualizar el ingreso/egreso
     * @param integer $ingreso_egreso_id Id del ingreso/egreso a modificar
     * @param array() $dataUpd Array de datos con la nueva informacion del ingreso/egreso a modificar
     * @return boolean False en caso de error y true en caso de exito
     */
    function actuIngresoEgreso($ingreso_egreso_id,$dataUpd){
        $this->db->where('ingreso_egreso_id', $ingreso_egreso_id);
        return $this->db->update('ingresos_egresos', $dataUpd);
    }
    
    
    /**
     * Funcion para consultar si el empleado tiene un ingreso sin un egreso registrado
     * @param integer $empleado_id id del empleado a consultar
     * @return boolean|array() false en caso de encontrar un problema en la consulta o array de datos con el resultado de la consulta
     */
    function getIngresoSinEgreso($empleado_id){
        $this->db->select('ingreso_egreso_id');
        $this->db->from('ingresos_egresos');
        $this->db->where('empleado_id', $empleado_id);
        $this->db->where('fecha_egreso', NULL);
        $data = $this->db->get();
        if($data!==false){
            if($data->num_rows()>0){
                return true;
            }
        }
        return false;
    }
}