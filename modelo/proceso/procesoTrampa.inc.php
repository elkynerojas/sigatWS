<?php


class procesoTrampa {
    private $conexion;
    function __construct() {
        $this->conexion=new conexion();
        $this->conexion->getConexion()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    public function insertar($request){
        try {
            $sql="INSERT INTO sigat.trampa  VALUES (?,?,?,?,?,?,?,?)";
            $ps=$this->conexion->getConexion()->prepare($sql);
            $obj = json_decode($request); 
            $ps->execute(array(
                $obj->tra_id,
                $obj->tit_id,
                $obj->atr_id,
                $obj->est_id,
                $obj->tra_fecha_reg,
                $obj->tra_fecha_act,
                $obj->tra_tec_reg,
                $obj->tra_tec_act
                ));
            $dato = array();
            if($ps->rowCount()>0){
                $dato['titulo'] = 'Operación Exitosa'; 
                $dato['mensaje'] = 'Trampa Registrada';
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
    public function listar($request){
        try {
              $sql = "select * from "
                      . "sigat.trampa as tra, "
                      . "sigat.tipo_trampa as tit, "
                      . "sigat.atrayente as atr, "
                      . "sigat.estado as est "
                      . "where ( "
                      . "tra.tit_id = tit.tit_id and "
                      . "tra.atr_id = atr.atr_id "
                      . "and tra.est_id = est.est_id) "
                      . "and ( "
                      . "cast(tra.tra_id as varchar) like ? "
                      . "or upper(tit.tit_descripcion) like upper(?) "
                      . "or upper(atr.atr_descripcion) like upper(?) ) "
                      . "order by tra.tra_id ";
            $ps = $this->conexion->getConexion()->prepare($sql);
            if(isset($request["id"])){
              $ps->execute(array ('%'.$request["id"].'%','%'.$request["id"].'%','%'.$request["id"].'%'));
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
    public function listarInactivas($request){
        try {
              $sql = "select * from "
                      . "sigat.trampa as tra "
                      . "where  "
                      . "tra.est_id = '2' "
                      . "and tra_tec_reg = ?  "
                      . "order by tra.tra_id ";
            $ps = $this->conexion->getConexion()->prepare($sql);
            if(isset($request["id"])){
              $ps->execute(array ($request["id"]));
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
    public function imp(){
        try {
              $sql = "select * from "
                      . "sigat.trampa as tra "
                      . "order by tra.tra_id ";
            $ps = $this->conexion->getConexion()->prepare($sql);
            
            $ps->execute();
            
            $dato = array();
            $dato=$ps->fetchAll(PDO::FETCH_OBJ);   
            $this->conexion = null;
        } catch (PDOException $e) {
             $dato = array('mensaje' => substr($e->getMessage(),strpos($e->getMessage(),"ERROR")));
        }
        return json_encode($dato);
    }

    public function exp($request){
        try {
            
            $sql="select * from sigat.trampa as tra where tra.tra_id = ? ";

            $ps=$this->conexion->getConexion()->prepare($sql);
            $obj = json_decode($request);
            $dato = array(); 
            $ps->execute(array(
                $obj->tra_id_aux
            ));
            if($ps->rowCount()>0){
                $sql="update sigat.trampa 
                    set tra_id = ?,
                    tit_id = ?,
                    atr_id = ?,
                    est_id = ?, 
                    tra_fecha_reg = ?, 
                    tra_fecha_act = ?,
                    tra_tec_reg = ?,
                    tra_tec_act = ?
                    where tra_id = ? ";

                $ps=$this->conexion->getConexion()->prepare($sql);
                $obj = json_decode($request); 
                $ps->execute(array(
                    $obj->tra_id,
                    $obj->tit_id,
                    $obj->atr_id,
                    $obj->est_id, 
                    $obj->tra_fecha_reg,
                    $obj->tra_fecha_act,
                    $obj->tra_tec_reg,
                    $obj->tra_tec_act,
                    $obj->tra_id_aux
                    ));
                if($ps->rowCount()>0){
                    $dato['titulo'] = 'Trampa';
                    $dato['mensaje'] = 'Sincronizado';
                    $dato['est'] = 'success';
                }else{
                   
                }  
            }else{
                $sql="insert into sigat.trampa values (?,?,?,?,?,?,?,?)";

                $ps=$this->conexion->getConexion()->prepare($sql);
                $obj = json_decode($request); 
                $ps->execute(array(
                    $obj->tra_id,
                    $obj->tit_id,
                    $obj->atr_id,
                    $obj->est_id, 
                    $obj->tra_fecha_reg,
                    $obj->tra_fecha_act,
                    $obj->tra_tec_reg,
                    $obj->tra_tec_act
                ));
                 if($ps->rowCount()>0){
                      $dato['titulo'] = 'Trampa';
                      $dato['mensaje'] = 'Sincronizado';
                      $dato['est'] = 'success';
                  }else{

                  }      
            }
            $this->conexion = null;
           
        } catch (PDOException $e) {
            
            $dato['titulo'] = 'Trampa'; 
            $dato['mensaje'] = substr($e->getMessage(),strpos($e->getMessage(),"ERROR"));
            $dato['est']= 'error';
        }
        return json_encode($dato);
    }
    public function buscar($request){
        try {
            $sql = "select * from "
                    . "sigat.trampa as tra, "
                    . "sigat.tipo_trampa as tit, "
                    . "sigat.atrayente as atr, "
                    . "sigat.estado as est "
                    . "where ( "
                    . "tra.tit_id = tit.tit_id and "
                    . "tra.atr_id = atr.atr_id and "
                    . "tra.est_id = est.est_id) and  "
                    . "tra.tra_id = ? ";
            $ps = $this->conexion->getConexion()->prepare($sql);   
            $ps->execute(array($request["id"]));
            $dato = array();
            if(!$ps->rowCount()>0){
                $dato = array('mensaje' => 'No existe');
            }else{
                $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                   $dato['tra_id'] = $row->tra_id;
                   $dato['tit_id'] = $row->tit_id;
                   $dato['tit_descripcion'] = $row->tit_descripcion;
                   $dato['atr_id'] = $row->atr_id;
                   $dato['atr_descripcion'] = $row->atr_descripcion;
                   $dato['est_id'] = $row->est_id;
                   $dato['est_descripcion'] = $row->est_descripcion;
                   $dato['tra_fecha_reg'] = $row->tra_fecha_reg;
                   $dato['tra_fecha_act'] = $row->tra_fecha_act;
                   $dato['tra_tec_reg'] = $row->tra_tec_reg;
                   $dato['tra_tec_act'] = $row->tra_tec_act;
                  
                }
                $sql = "select * from sigat.tecnico as tec, 
                        sigat.persona as per 
                        where tec.tec_id = ? 
                        and tec.per_cc = per.per_cc ";
                $ps = $this->conexion->getConexion()->prepare($sql);   
                $ps->execute(array($dato["tra_tec_reg"]));
                $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                    $dato['tec_reg_nombre'] = $row->per_nombre." ".$row->per_apellido;  
                }
                $sql = "select * from sigat.tecnico as tec, 
                        sigat.persona as per 
                        where tec.tec_id = ? 
                        and tec.per_cc = per.per_cc ";
                $ps = $this->conexion->getConexion()->prepare($sql);   
                $ps->execute(array($dato["tra_tec_act"]));
                $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                    $dato['tec_act_nombre'] = $row->per_nombre." ".$row->per_apellido;  
                }
                
            }
            $this->conexion = null;   
        } catch (PDOException $e) {
            $dato = array('mensaje' => substr($e->getMessage(),strpos($e->getMessage(),"ERROR")));
        }
        return json_encode($dato);
    }
    
    public function actualizar($request){
        try {
            $sql = "update sigat.trampa set "
                    . "tra_id = ?, "
                    . "tit_id = ?, "
                    . "atr_id =  ?, "
                    . "est_id = ?, "
                    . "tra_fecha_act = ?, "
                    . "tra_tec_act = ? "
                    . "where tra_id = ? ";
            $ps=$this->conexion->getConexion()->prepare($sql);
            $obj = json_decode($request); 
            $ps->execute(array(
                $obj->tra_id,
                $obj->tit_id,
                $obj->atr_id,
                $obj->est_id,
                $obj->tra_fecha_act,
                $obj->tra_tec_act,
                $obj->tra_id_aux
                ));
            $dato = array();
            if($ps->rowCount()>0){
                $dato['titulo'] = 'Operación Exitosa';
                $dato['mensaje'] = 'Trampa Actualizada';
                $dato['est'] = 'success';
            }else{
                $dato['titulo'] = 'Operación Fallida';
                $dato['mensaje'] = 'Trampa No se Actualizó';
                $dato['est'] = 'error';
            }
            $this->conexion = null;
        } catch (PDOException $e) {
             $dato['mensaje'] = substr($e->getMessage(),strpos($e->getMessage(),"ERROR"));
             $dato ['titulo'] = 'Operación Fallida';
             $dato['est'] = 'error';
        }
        return json_encode($dato);
    }
    public function activar($request){
        try {
            $sql = "update sigat.trampa set "
                    . "est_id = 1"
                    . "where tra_id = ? ";
            $ps=$this->conexion->getConexion()->prepare($sql);
            $obj = json_decode($request); 
            $ps->execute(array(
                $obj->tra_id,
                ));
            $dato = array();
            if($ps->rowCount()>0){
                $dato['resp'] = true;
               
            }else{
                $dato['resp'] = false;
            }
            $this->conexion = null;
        } catch (PDOException $e) {
             $dato['resp'] = 'error';
        }
        return json_encode($dato);
    }
}
