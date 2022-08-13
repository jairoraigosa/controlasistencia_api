<?php
if (!defined('BASEPATH')) exit('No direct script access allowed'); 
class Forms{
    private $msg = array();
    public $params = array();
    public function __construct(){
        $this->CI = &get_instance();
    }
    /**
     * Funcion para validar los parametros, esta funcion realiza las validaciones que cualquier campo de un formulario podria tener
     * @param string $param_name nombre del parametro que viene desde el formulario
     * @param boolean $is_req true en caso de que sea un campo requerido o false en caso de que pueda ir vacío el campo
     * @param integer|boolean $min_length longitud minima del campo, enviar false en caso de que no haya una longitud minima
     * @param integer|boolean $max_length longitud maxima del campo, enviar false en caso de que no haya una longitud maxima
     * @return boolean true en caso de que la validación se haya realizado exitosamente o false en caso de no superar las validaciones
     */
    public function valParam($param_name, $is_req=true, $min_length=false, $max_length=false){
        if(!isset($this->params[$param_name])){
            $this->msg[] = "Falta el parámetro $param_name.";
            return false;
        }
        if($is_req===true){
            if(trim($this->params[$param_name])===""){
                $this->msg[] = "El parámetro $param_name es requerido y no puede estar vacío.";
                return false;
            }
        }
        if($min_length!==false){
            if(strlen($this->params[$param_name])<$min_length){
                $this->msg[] = "El parámetro $param_name debe contener mínimo $min_length caracteres.";
                return false;
            }
        }
        if($max_length!==false){
            if(strlen($this->params[$param_name])>$max_length){
                $this->msg[] = "El parámetro $param_name debe contener máximo $max_length caracteres.";
                return false;
            }
        }
        return true;
    }
    
    /**
     * Funcion para validar los campos de tipo string
     * @param string $param_name nombre del parametro que viene desde el formulario
     * @param boolean $is_req true en caso de que sea un campo requerido o false en caso de que pueda ir vacío el campo
     * @param integer|boolean $min_length longitud minima del campo, enviar false en caso de que no haya una longitud minima
     * @param integer|boolean $max_length longitud maxima del campo, enviar false en caso de que no haya una longitud maxima
     * @return boolean true en caso de que la validación se haya realizado exitosamente o false en caso de no superar las validaciones
     */
    public function valString($param_name, $is_req=true, $min_length=false, $max_length=false){
        if(!$this->valParam($param_name, $is_req, $min_length, $max_length)){
            return false;
        }
        return true;
    }
    
    /**
     * Funcion para validar los campos de tipo integer
     * @param string $param_name nombre del parametro que viene desde el formulario
     * @param boolean $is_req true en caso de que sea un campo requerido o false en caso de que pueda ir vacío el campo
     * @param integer|boolean $min_length longitud minima del campo, enviar false en caso de que no haya una longitud minima
     * @param integer|boolean $max_length longitud maxima del campo, enviar false en caso de que no haya una longitud maxima
     * @return boolean true en caso de que la validación se haya realizado exitosamente o false en caso de no superar las validaciones
     */
    public function valInteger($param_name, $is_req=true, $min_length=false, $max_length=false){
        if(!$this->valParam($param_name, $is_req, $min_length, $max_length)){
            return false;
        }
        if(!is_numeric($this->params[$param_name])){
            $this->msg[] = "El parámetro $param_name debe contener solo caracteres numéricos.";
            return false;
        }
        return true;
    }
    
    /**
     * Funcion para validar los campos de tipo fecha
     * @param string $param_name nombre del parametro que viene desde el formulario
     * @param boolean $is_req true en caso de que sea un campo requerido o false en caso de que pueda ir vacío el campo
     * @param integer|boolean $min_date Fecha mínima que debe aceptar el campo, fecha en formato (dd-mm-yyyy)
     * @param integer|boolean $max_date Fecha máxima que debe aceptar el campo, fecha en formato (dd-mm-yyyy)
     * @return boolean true en caso de que la validación se haya realizado exitosamente o false en caso de no superar las validaciones
     */
    public function valDate($param_name, $is_req=true, $min_date=false, $max_date=false){
        if(!$this->valParam($param_name, $is_req)){
            return false;
        }
        if(strpos($this->params[$param_name], "-")===false){
            $this->msg[] = "El parámetro $param_name tiene un formato de fecha incorrecta.";
            return false;
        }
        list($d,$m,$y)=explode("-", $this->params[$param_name]);
        if(!checkdate($m, $d, $y)){
            $this->msg[] = "El parámetro $param_name tiene un formato de fecha incorrecta.";
            return false;
        }
        if($min_date!==false){
            list($md,$mm,$my)=explode("-", $min_date);
            list($d,$m,$y)=explode("-", $this->params[$param_name]);
            $mDate = new DateTime("$my-$mm-$md");
            $date = new DateTime("$y-$m-$d");
            if($date<$mDate){
                $this->msg[] = "El parámetro $param_name debe contener una fecha mayor o igual a $min_date.";
                return false;
            }
        }
        if($max_date!==false){
            list($md,$mm,$my)=explode("-", $max_date);
            list($d,$m,$y)=explode("-", $this->params[$param_name]);
            $mDate = new DateTime("$my-$mm-$md");
            $date = new DateTime("$y-$m-$d");
            if($date>$mDate){
                $this->msg[] = "El parámetro $param_name debe contener una fecha menor o igual a $max_date.";
                return false;
            }
        }
        return true;
    }
    
    /**
     * Funcion para validar los campos de tipo email
     * @param string $param_name nombre del parametro que viene desde el formulario
     * @param boolean $is_req true en caso de que sea un campo requerido o false en caso de que pueda ir vacío el campo
     * @param integer|boolean $min_length longitud minima del campo, enviar false en caso de que no haya una longitud minima
     * @param integer|boolean $max_length longitud maxima del campo, enviar false en caso de que no haya una longitud maxima
     * @return boolean true en caso de que la validación se haya realizado exitosamente o false en caso de no superar las validaciones
     */
    public function valEmail($param_name, $is_req=true, $min_length=false, $max_length=false){
        if(!$this->valParam($param_name, $is_req, $min_length, $max_length)){
            return false;
        }
        if(!filter_var($this->params[$param_name], FILTER_VALIDATE_EMAIL)) {
            $this->msg[] = "El parámetro $param_name debe contener un correo electrónico válido.";
            return false;
        }
        return true;
    }
    
    /**
     * Funcion para validar los campos de tipo integer
     * @param string $param_name nombre del parametro que viene desde el formulario
     * @param boolean $is_req true en caso de que sea un campo requerido o false en caso de que pueda ir vacío el campo
     * @param integer|boolean $min_length longitud minima del campo, enviar false en caso de que no haya una longitud minima
     * @param integer|boolean $max_length longitud maxima del campo, enviar false en caso de que no haya una longitud maxima
     * @param integer|boolean $minus true en caso de requerir minusculas false en caso de no requerirlas
     * @param integer|boolean $mayus true en caso de requerir mayusculas false en caso de no requerirlas
     * @param integer|boolean $num true en caso de requerir números false en caso de no requerirlos
     * @return boolean true en caso de que la validación se haya realizado exitosamente o false en caso de no superar las validaciones
     */
    public function valPassword($param_name, $is_req=true, $min_length=false, $max_length=false, $minus=false, $mayus=false, $num=false){
        if(!$this->valParam($param_name, $is_req, $min_length, $max_length)){
            return false;
        }
        if(strlen($this->params[$param_name]) < $min_length){
            $this->msg[] = "El parámetro $param_name debe contener mas de $min_length caracteres.";
            return false;
        }
        if(strlen($this->params[$param_name]) > $max_length){
            $this->msg[] = "El parámetro $param_name debe contener menos de $max_length caracteres.";
            return false;
        }
        if($minus){
            if (!preg_match('`[a-z]`',$this->params[$param_name])){
                $this->msg[] = "El parámetro $param_name debe contener al menos 1 minúscula.";
                return false;
            }
        }
        if($mayus){
            if (!preg_match('`[A-Z]`',$this->params[$param_name])){
                $this->msg[] = "El parámetro $param_name debe contener al menos 1 mayúscula.";
                return false;
            }
        }
        if($num){
            if (!preg_match('`[0-9]`',$this->params[$param_name])){
                $this->msg[] = "El parámetro $param_name debe contener al menos 1 número.";
                return false;
            }
        }
        return true;
    }
    /**
     * Funcion para capturar un parametro proveniente del formulario
     * @param string $param nombre del parametro proveniente del formulario
     * @return boolean|string valor del parametro o false en caso de que el parametro venga vacío
     */
    public function getParam($param){
        return isset($this->params[$param])
            ? $this->params[$param]!==''
                ? $this->params[$param]
                : false
            : false;
    }
    
    /**
     * Funcion para setear mensajes que se enviarán como respuesta en la solicitud
     * @param string $msg Mensaje a adicionar en la variable de respuesta de la peticion
     * @return string retorna el mensaje que se adiciono al listado de mensajes
     */
    public function setMsg($msg){
        $this->msg[] = $msg;
        return $msg;
    }
    
    /**
     * Funcion para capturar el mensaje guardado
     * @return array() array con la información de los mensajes almacenados durante el tiempo de ejecución
     */
    public function getMsg(){
        return $this->msg;
    }
}