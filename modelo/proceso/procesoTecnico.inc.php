<?php

//include 'procesoPersona.inc.php';
class procesoTecnico {
    private $conexion;
    function __construct() {
        
        $this->conexion=new conexion();
        $this->conexion->getConexion()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    public function insertar($request){
        try {
            $sql="INSERT INTO sigat.persona  VALUES (?,upper(?),upper(?),?,?,upper(?),lower(?),?)";
            $ps=$this->conexion->getConexion()->prepare($sql);
            $obj = json_decode($request); 
            $ps->execute(array(
                $obj->per_cc,
                $obj->per_nombre,
                $obj->per_apellido,
                $obj->gen_id,
                $obj->per_telefono,
                $obj->per_direccion,
                $obj->per_correo,
                $obj->tip_id
                ));
            $dato = array();
            if($ps->rowCount()>0){
                $dato = array('mensaje' => 'Registrado');
            }
            $sql = "Select max(tec_id) as mayor from sigat.tecnico 
            where cac_id = ? ";
            $ps=$this->conexion->getConexion()->prepare($sql);
            $ps->execute(array($obj->cac_id));
            if($ps->rowCount()>0){
                $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                   
                   if($row->mayor){
                       $codigo = $row->mayor + 1;
                   }else{
                       $codigo = $obj->cac_id."20001";
                   }
                }
            }else{
                
            }
            $sql="INSERT INTO sigat.tecnico  VALUES (?,?,?,?,?,?,?)";
            $ps=$this->conexion->getConexion()->prepare($sql);
             
            $ps->execute(array(
                $codigo,
                $obj->per_cc,
                $obj->cac_id,
                $obj->adm_id,
                $obj->tec_fecha_reg,
                $obj->tec_fecha_act,
                $obj->est_id
                ));
            $dato = array();
            if($ps->rowCount()>0){
                 $sql="INSERT INTO sigat.usuarios  VALUES (?,?,md5(?),?,?)";
                 $ps=$this->conexion->getConexion()->prepare($sql);
             
                $ps->execute(array(
                    $obj->per_cc,
                    $obj->usu_usuario,
                    $obj->usu_contrasena,
                    $obj->tiu_id,
                    $obj->est_id
                    ));
                $dato = array();
                if($ps->rowCount()>0){
                  $dato['titulo'] = 'Operación Exitosa'; 
                  $dato['mensaje'] = 'Técnico Registrado Correctamente';
                  $dato['est']= 'success';
                }
            }
            $this->conexion = null;
        } catch (PDOException $e) {
            $dato['titulo'] = 'Operación Fallida'; 
            $dato['mensaje'] = substr($e->getMessage(),strpos($e->getMessage(),"ERROR"));
            $dato['est']= 'error';
        }
        return json_encode($dato);
      
    }
    
    public function listar($request){
        try {
              $sql = "select * from sigat.tecnico as tec
                      inner join sigat.persona as per on (per.per_cc = tec.per_cc)
                      where cast (tec.tec_id as varchar) like ? 
                      or cast (per.per_cc as varchar) like ? 
                      or upper(per.per_nombre) like upper (?)
                      or upper(per.per_apellido) like upper(?)";

            $ps = $this->conexion->getConexion()->prepare($sql);
            if(isset($request["id"])){
              $ps->execute(array ('%'.$request["id"].'%','%'.$request["id"].'%','%'.$request["id"].'%','%'.$request["id"].'%'));
            }else{
                echo 'No Existe un paramétro para filtrar los datos';
            }
            $dato = array();
            $dato=$ps->fetchAll(PDO::FETCH_OBJ);   
            $this->conexion = null;
        } catch (PDOException $e) {
             $dato = array('mensaje' => substr($e->getMessage(),strpos($e->getMessage(),"ERROR")));
        }
        return json_encode($dato);
    } 
    
     public function actualizar($request){
        try {
            $sql = "update sigat.persona set "
                    . "per_cc=?, "
                    . "per_nombre=?, "
                    . "per_apellido=?, "
                    . "gen_id = ?, "
                    . "per_telefono= ?, "
                    . "per_direccion= ? "
                    . "where per_cc=? ";
            $ps=$this->conexion->getConexion()->prepare($sql);
            $obj = json_decode($request); 
            $ps->execute(array(
                $obj->per_cc,
                $obj->per_nombre,
                $obj->per_apellido,
                $obj->gen_id,
                $obj->per_telefono,
                $obj->per_direccion,
                $obj->per_cc_aux
                ));
            $dato = array();
            if($ps->rowCount()>0){
                $dato['titulo'] = 'Operación Exitosa'; 
                $dato['mensaje'] = 'Datos Actualizados Correctamente';
                $dato['est']= 'success';
                 
            }
            
            $this->conexion = null;
        } catch (PDOException $e) {
            $dato['titulo'] = 'Operación Fallida'; 
            $dato['mensaje'] = substr($e->getMessage(),strpos($e->getMessage(),"ERROR"));
            $dato['est']= 'error';
        }
        return json_encode($dato);
    }
    public function credenciales ($request){
      try {
                 $sql = "update sigat.usuarios 
                        set usu_usuario = ?, usu_contrasena = md5 (?),est_id = ? 
                        where per_cc = ? ";
                
                    $ps=$this->conexion->getConexion()->prepare($sql);
                    $obj = json_decode($request); 
                    $ps->execute(array(
                        $obj->usu_usuario,
                        $obj->usu_contrasena,
                        $obj->est_id,
                        $obj->per_cc
                        ));
                    $dato = array();
                    if($ps->rowCount()>0){
                          $dato['titulo'] = 'Operación Exitosa'; 
                          $dato['mensaje'] = 'Datos Actualizados Correctamente';
                          $dato['est']= 'success';
                    }
            
            
            $this->conexion = null;
        } catch (PDOException $e) {
            $dato['titulo'] = 'Operación Fallida'; 
            $dato['mensaje'] = substr($e->getMessage(),strpos($e->getMessage(),"ERROR"));
            $dato['est']= 'error';
        }
        return json_encode($dato);
    }
    public function buscar($request){
        try {
            $sql = "select * from sigat.tecnico as tec
                    inner join sigat.persona as per on (tec.per_cc = per.per_cc)
                    inner join sigat.genero as gen on (per.gen_id = gen.gen_id)
                    inner join sigat.usuarios as usu on (usu.per_cc = tec.per_cc)
                    where tec.tec_id = ? ";
            $ps = $this->conexion->getConexion()->prepare($sql);   
            $ps->execute(array($request["id"]));
            $dato = array();
            if(!$ps->rowCount()>0){
                $dato = array('mensaje' => 'No existe');
            }else{
                $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                   $dato['per_cc'] = $row->per_cc;
                   $dato['tec_id'] = $row->tec_id;
                   $dato['per_nombre'] = $row->per_nombre;
                   $dato['per_apellido'] = $row->per_apellido;
                   $dato['gen_id'] = $row->gen_id;
                   $dato['gen_descripcion'] = $row->gen_descripcion;
                   $dato['per_telefono'] = $row->per_telefono;
                   $dato['per_direccion'] = $row->per_direccion;
                   $dato['per_correo'] = $row->per_correo;
                   $dato['adm_id'] = $row->adm_id;
                   $dato['est_id'] = $row->est_id;
                   $dato['usu_usuario'] = $row->usu_usuario;
                }
                $sql = "select * from sigat.administrador as adm, 
                        sigat.persona as per 
                        where adm.adm_id = ? 
                        and adm.per_cc = per.per_cc ";
                $ps = $this->conexion->getConexion()->prepare($sql);   
                $ps->execute(array($dato["adm_id"]));
                $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                    $dato['adm_nombre'] = $row->per_nombre." ".$row->per_apellido;  
                }
                
            }
            $this->conexion = null;   
        } catch (PDOException $e) {
            $dato = array('mensaje' => substr($e->getMessage(),strpos($e->getMessage(),"ERROR")));
        }
        return json_encode($dato);
    }

    

    public function imp($request){
        try {
            $sql="select * from "
                    . "sigat.persona as per, "
                    . "sigat.tecnico as tec, "
                    . "sigat.ruta as rut, "
                    . "sigat.centro_acopio as cac "
                    . "where per.per_cc = tec.per_cc "
                    . "and rut.cac_id = cac.cac_id "
                    . "and tec.cac_id = rut.cac_id "
                    . "and rut.rut_id = ? ";

            $ps = $this->conexion->getConexion()->prepare($sql);
            
            $ps->execute(array($request["id"]));
           
            $dato = array();
            $dato=$ps->fetchAll(PDO::FETCH_OBJ);   

            $this->conexion = null;
        } catch (PDOException $e) {
             $dato = array('mensaje' => substr($e->getMessage(),strpos($e->getMessage(),"ERROR")));
        }
        return json_encode($dato);
    } 
  public function clave($request){
     try {
           $obj = json_decode($request);
            $sql = "select * from sigat.usuarios where usu_usuario ='".$obj->usu_usuario."'and usu_contrasena = md5('".$obj->usu_contrasena."') ";
            $ps=$this->conexion->getConexion()->prepare($sql);
             
              $ps->execute();
            $dato = array();
            if($ps->rowCount()>0){
                 $sql = "update sigat.usuarios set usu_contrasena = md5(?) where usu_usuario = ? ";
                  $ps=$this->conexion->getConexion()->prepare($sql);
                  $ps->execute(array($obj->usu_contrasena_new, $obj->usu_usuario));
                  $dato = array();
                  if($ps->rowCount()>0){
                            $dato['titulo'] = 'Operación Exitosa'; 
                            $dato['mensaje'] = 'La contraseña se cambió correctamente';
                            $dato['est']= 'success';
                  }else{
                            $dato['titulo'] = 'Operación Fallida'; 
                            $dato['mensaje'] = 'Error desconocido al cambiar la contraseña';
                            $dato['est']= 'error';

                  }
            }else{
                      $dato['titulo'] = 'Operación Fallida'; 
                      $dato['mensaje'] = 'La contraseña es incorrecta';
                      $dato['est']= 'error';
                     
            }
            
            $this->conexion = null;
        } catch (PDOException $e) {
             $dato = array('mensaje' => substr($e->getMessage(),strpos($e->getMessage(),"ERROR")));
        }
        return json_encode($dato);
  }
}