<?php


class procesoLote {
    private $conexion;
    function __construct() {
        $this->conexion=new conexion();
        $this->conexion->getConexion()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    public function insertar($request){
        try {
          
            $sql = "Select max(lot_id) as mayor from sigat.lote 
            where cast (pre_id as varchar) like ? ";
            $ps=$this->conexion->getConexion()->prepare($sql);
             $obj = json_decode($request); 
            $ps->execute(array($obj->pre_id.'%'));
            if($ps->rowCount()>0){
                $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                   
                   if($row->mayor){
                       $codigo = $row->mayor + 1;
                   }else{
                       $codigo = $obj->pre_id."0001";
                   }
                }
            }else{
                
            }
          
            $sql="INSERT INTO sigat.lote  VALUES (?, UPPER (?), ?,?,?,?,?,?)";
            $ps=$this->conexion->getConexion()->prepare($sql);
            $obj = json_decode($request); 
            $ps->execute(array(
                $codigo,
                $obj->lot_descripcion,
                $obj->lot_area,
                $obj->pre_id,
                $obj->lot_fecha_reg,
                $obj->lot_fecha_act,
                $obj->lot_tec_reg,
                $obj->lot_tec_act
                ));
            $dato = array();
            if($ps->rowCount()>0){
                $dato['titulo'] = 'Operación Exitos'; 
                $dato['mensaje'] = 'Lote Registrado';
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
                      . "sigat.lote as lot, "
                      . "sigat.predio as pre, "
                      . "sigat.productor as prod, "
                      . "sigat.persona as per "
                      . "where "
                      . "lot.pre_id = pre.pre_id "
                      . "and pre.per_cc = prod.per_cc "
                      . "and prod.per_cc = per.per_cc "
                      . "and lot.pre_id = ? "
                      . "order by lot.lot_descripcion ";
                      
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
    
    public function buscar($request){
        try {
            $sql = "select * from "
                      . "sigat.lote as lot, "
                      . "sigat.predio as pre, "
                      . "sigat.productor as prod, "
                      . "sigat.persona as per "
                      . "where "
                      . "lot.pre_id = pre.pre_id "
                      . "and pre.per_cc = prod.per_cc "
                      . "and prod.per_cc = per.per_cc "
                      . "and lot.lot_id = ? "
                      . "order by lot.lot_descripcion ";
                    
            $ps = $this->conexion->getConexion()->prepare($sql);   
            $ps->execute(array($request["id"]));
            $dato = array();
            if(!$ps->rowCount()>0){
                $dato = array('mensaje' => 'No existe');
            }else{
                $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                    
                   $dato['lot_id'] = $row->lot_id;
                   $dato['lot_descripcion'] = $row->lot_descripcion;
                   $dato['lot_area'] = $row->lot_area;
                   $dato['pre_id'] = $row->pre_id;
                   $dato['pre_nombre'] = $row->pre_nombre;
                   $dato['lot_fecha_reg'] = $row->lot_fecha_reg;
                   $dato['lot_fecha_act'] = $row->lot_fecha_act;
                   $dato['lot_tec_reg'] = $row->lot_tec_reg;
                   $dato['lot_tec_act'] = $row->lot_tec_act;
                }
                $sql = "select * from sigat.tecnico as tec, 
                        sigat.persona as per 
                        where tec.tec_id = ? 
                        and tec.per_cc = per.per_cc ";
                $ps = $this->conexion->getConexion()->prepare($sql);   
                $ps->execute(array($dato["lot_tec_reg"]));
                $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                    $dato['tec_reg_nombre'] = $row->per_nombre." ".$row->per_apellido;  
                }
                $sql = "select * from sigat.tecnico as tec, 
                        sigat.persona as per 
                        where tec.tec_id = ? 
                        and tec.per_cc = per.per_cc ";
                $ps = $this->conexion->getConexion()->prepare($sql);   
                $ps->execute(array($dato["lot_tec_act"]));
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
            $sql = "update sigat.lote set "
                    . "lot_id = ?, "
                    . "lot_descripcion = upper (?), "
                    . "lot_area =  ?, "
                    . "lot_fecha_act = ?, "
                    . "lot_tec_act = ? "
                    . "where lot_id = ? ";
            $ps=$this->conexion->getConexion()->prepare($sql);
            $obj = json_decode($request); 
            $ps->execute(array(
                $obj->lot_id,
                $obj->lot_descripcion,
                $obj->lot_area,
                $obj->lot_fecha_act,
                $obj->lot_tec_act,
                $obj->lot_id_aux
                ));
            $dato = array();
            if($ps->rowCount()>0){
                $dato['titulo'] = 'Operación Exitosa';
                $dato['mensaje'] = 'Lote Actualizado';
                $dato['est'] = 'success';
            }else{
                $dato['titulo'] = 'Operación Fallida';
                $dato['mensaje'] = 'Lote No se Actualizó';
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
    public function imp($request){
        try {
              $sql = "select lot.lot_id, lot.lot_descripcion, lot.lot_area, lot.pre_id,
                      lot.lot_fecha_reg, lot.lot_fecha_act, lot.lot_tec_reg, lot.lot_tec_act from 
                      sigat.lote as lot 
                      inner join sigat.predio as pre on (lot.pre_id = pre.pre_id)
                      inner join sigat.productor as prod on (pre.per_cc = prod.per_cc)
                      inner join sigat.municipio as mun on (prod.mun_id = mun.mun_id)
                      inner join sigat.ruta as rut on (rut.mun_id = mun.mun_id)
                      where rut.rut_id = ? ";
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

    public function exp($request){
        try {
            
            $sql="SELECT * FROM sigat.predio as pre WHERE pre.pre_id = ? ";

            $ps=$this->conexion->getConexion()->prepare($sql);
            $obj = json_decode($request);
            $dato = array(); 
            $ps->execute(array(
                $obj->pre_id_aux
            ));
            
            if($ps->rowCount()>0){
                $sql="update sigat.predio 
                    set pre_id = ?,
                    pre_nombre = ?,
                    pre_area = ?,
                    pre_lotes = ?, 
                    per_cc = ?,
                    pre_fecha_reg = ?, 
                    pre_fecha_act = ?,
                    pre_tec_reg = ?,
                    pre_tec_act = ?,
                    where pre_id = ? ";

                $ps=$this->conexion->getConexion()->prepare($sql);
                $obj = json_decode($request); 
                $ps->execute(array(
                    $obj->pre_id,
                    $obj->pre_nombre,
                    $obj->pre_area,
                    $obj->pre_lotes, 
                    $obj->per_cc,
                    $obj->pre_fecha_reg,
                    $obj->pre_fecha_act,
                    $obj->pre_tec_reg,
                    $obj->pre_tec_act,
                    $obj->pre_id_aux
                    ));
                if($ps->rowCount()>0){
                    $dato['titulo'] = 'Predio';
                    $dato['mensaje'] = 'Sincronizado';
                    $dato['est'] = 'success';
                }else{
                   
                }  
            }else{
                $sql="INSERT INTO sigat.predio VALUES (?,upper(?),?,?,?,?,?,?,?) ";

                $ps=$this->conexion->getConexion()->prepare($sql);
                $obj = json_decode($request); 
                $ps->execute(array(
                    $obj->pre_id,
                    $obj->pre_nombre,
                    $obj->pre_area,
                    $obj->pre_lotes, 
                    $obj->per_cc,
                    $obj->pre_fecha_reg,
                    $obj->pre_fecha_act,
                    $obj->pre_tec_reg,
                    $obj->pre_tec_act
                ));
                 if($ps->rowCount()>0){
                      $dato['titulo'] = 'Productor';
                      $dato['mensaje'] = 'Sincronizado';
                      $dato['est'] = 'success';
                  }else{

                  } 
                
                 
            }
            $this->conexion = null;
           
        } catch (PDOException $e) {
            
            $dato['titulo'] = 'Predio'; 
            $dato['mensaje'] = substr($e->getMessage(),strpos($e->getMessage(),"ERROR"));
            $dato['est']= 'error';
        }
        return json_encode($dato);
    }
}
