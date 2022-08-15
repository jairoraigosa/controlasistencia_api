<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Empleados_model extends CI_Model{
    function __construct() {
        parent::__construct();
    }
    /**
     * Funcion para consultar  en la base de datos los empleados de la empresa
     * @param integer $empleado_id id del empleado a consultar
     * @param integer $cedula id del empleado a consultar
     * @return boolean|array() false en caso de encontrar un problema en la consulta o array de datos con el resultado de la consulta
     */
    function getEmpleados($empleado_id=false, $cedula=false, $cargo=false, $edad_minima=false, $edad_maxima=false){
        $this->db->select('empleado_id,nombres,apellidos,edad,cargo,cedula,no_celular');
        $this->db->from('empleados');
        if($empleado_id!==false){
            $this->db->where('empleado_id', $empleado_id);
        }
        if($cedula!==false){
            $this->db->where('cedula', $cedula);
        }
        if($cargo!==false){
            $this->db->where('cargo LIKE', "%$cargo%");
        }
        if($edad_minima!==false){
            $this->db->where('edad>=', $edad_minima);
        }
        if($edad_maxima!==false){
            $this->db->where('edad<=', $edad_maxima);
        }
        $data = $this->db->get();
        if($data!==false){
            return $data->result_array();
        }
        return false;
    }
    /**
     * Funcion para validar si existe un empleado con la cedula modificada, aplica para actualizaciones de empleado
     * @param integer $empleado_id id del empleado a validar
     * @param integer $cedula cedula a buscar si existe para otro empleado
     */    
    function existeEmpleado($empleado_id, $cedula){
        $this->db->select('empleado_id');
        $this->db->from('empleados');
        if($empleado_id!==false){
            $this->db->where('empleado_id!=', $empleado_id);
        }
        if($cedula!==false){
            $this->db->where('cedula', $cedula);
        }
        $data = $this->db->get();
        if($data!==false){
            return $data->result_array();
        }
        return false;
    }
    
    /**
     * Funcion para registrar a un nuevo empleado
     * @param array() $dataEmpleado Array de datos con la información del empleado a registrar
     * @return boolean False en caso de error y true en caso de exito
     */
    function regEmpleado($dataEmpleado){
        return $this->db->insert('empleados', $dataEmpleado);
    }
    
    /**
     * Funcion para actualizar el empleado en la base de datos
     * @param integer $empleado_id Id del empleado a modificar
     * @param array() $dataUpd Array de datos con la nueva informacion del empleado a modificar
     * @return boolean False en caso de error y true en caso de exito
     */
    function actuEmpleado($empleado_id,$dataUpd){
        $this->db->where('empleado_id', $empleado_id);
        return $this->db->update('empleados', $dataUpd);
    }
}