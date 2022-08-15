<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Login_model extends CI_Model{
    function __construct() {
        parent::__construct();
    }
    /**
     * Funcion para consultar  en la base de datos
     * @param integer $username Nombre de usuario en la plataforma
     * @param integer $password Contraseña del usuario en la plataforma
     * @return boolean|array() false en caso de encontrar un problema o si la consulta no devuelve datos o array de datos con el resultado de la consulta
     */
    function getUsuario($username, $password){
        $this->db->select('usuario_id,nombre_usuario,contrasena');
        $this->db->from('usuarios');
        $this->db->where('nombre_usuario', $username);
        $this->db->where('contrasena', md5($password));
        $data = $this->db->get();
        if($data!==false){
            if($data->num_rows()>0){
                return $data->result_array();
            }
        }
        return false;
    }
}