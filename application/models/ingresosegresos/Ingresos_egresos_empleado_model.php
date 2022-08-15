<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Ingresos_egresos_empleado_model extends CI_Model{
    function __construct() {
        parent::__construct();
    }
    /**
     * Funcion para consultar  en la base de datos los ingresos que no contienen egresos en un empleado
     * @param integer $ingreso_egreso_id id del ingreso/egreso a consultar (opcional)
     * @param integer $cedula cedula del empleado a ser consultado (opcional)
     * @return boolean|array() false en caso de encontrar un problema en la consulta o array de datos con el resultado de la consulta
     */
    function getIngresosSinEgresosEmpleado($ingreso_egreso_id=false, $cedula=false){
        $this->db->select('a.ingreso_egreso_id,a.empleado_id,a.fecha_ingreso,DATE_FORMAT(a.fecha_ingreso, "%d-%m-%Y %H:%i") as fec_ing,a.fecha_egreso,DATE_FORMAT(a.fecha_egreso, "%d-%m-%Y %H:%i") as fec_eg,b.nombres,b.apellidos,b.edad,b.cargo,b.cedula');
        $this->db->from('ingresos_egresos as a');
        $this->db->join('empleados as b', 'b.empleado_id = a.empleado_id');
        $this->db->where('a.fecha_egreso', NULL);
        if($ingreso_egreso_id!==false){
            $this->db->where('a.ingreso_egreso_id', $ingreso_egreso_id);
        }
        if($cedula!==false){
            $this->db->where('b.cedula', $cedula);
        }
        $data = $this->db->get();
        if($data!==false){
            return $data->result_array();
        }
        return false;
    }
    
    /**
     * Funcion para registrar los ingresos de los empleados
     * @param array() $dataIngEg Array con la información a registrar 
     * @return boolean|array() false en caso de encontrar un problema en la consulta o array de datos con el resultado de la consulta
     */
    function regIngresoEgreso($dataIngEg){
        $empleado_id = $this->empleados_model->getEmpleados(false,$dataIngEg['cedula'])[0]['empleado_id'];
        return $this->db->insert('ingresos_egresos', array('empleado_id'=>$empleado_id,'fecha_ingreso'=>date('Y-m-d H:i:s'),'fecha_egreso'=>NULL));
    }
    
    /**
     * Funcion para registrar los egresos de los empleados
     * @param integer $ingreso_egreso_id id del ingreso/egreso a registrar su egreso
     */
    function actuEgreso($ingreso_egreso_id){
        $this->db->where('ingreso_egreso_id', $ingreso_egreso_id);
        return $this->db->update('ingresos_egresos',array('fecha_egreso'=>date('Y-m-d H:i:s')));
    }
}