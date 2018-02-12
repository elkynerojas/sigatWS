<?php


class procesoInspeccion {
    private $conexion;
    function __construct() {
        $this->conexion=new conexion();
        $this->conexion->getConexion()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    public function insertar($request){
        
        try {
           
            $sql="INSERT INTO sigat.inspeccion  VALUES (?,?,?,?,?,?,?)";
            $ps=$this->conexion->getConexion()->prepare($sql);
            $obj = json_decode($request); 
            $ps->execute(array(
                $obj->ins_fecha,
                $obj->pre_id,
                $obj->per_cc,
                $obj->tec_id,
                $obj->ins_estado,
                $obj->ins_obs,
                $obj->rut_id,
                ));
            $dato = array();
            if($ps->rowCount()>0){
                $dato['titulo'] = 'Operación Exitosa'; 
                $dato['mensaje'] = 'La inspección se registró correctamente';
                $dato['est']= 'success';
            }
            $this->conexion = null;
        } catch (PDOException $e) {
            
            $dato['titulo'] = 'Operación Fallida'; 
            $dato['mensaje'] = substr($e->getMessage(),strpos($e->getMessage(),"ERROR"));
            //$dato['mensaje'] = $mensaje;
            $dato['est']= 'error';
        }
        return json_encode($dato);
    }
    public function listar($request){
        try {
              $sql = "select pre.pre_id, pre.pre_nombre, per.per_cc, per.per_nombre, per.per_apellido, ins.ins_fecha, ins.ins_estado,ins.ins_obs 
                      from sigat.inspeccion as ins
                      inner join sigat.predio as pre on (pre.pre_id = ins.pre_id)
                      inner join sigat.tecnico as tec on (ins.tec_id = tec.tec_id)
                      inner join sigat.persona as per on (per.per_cc = tec.per_cc)
                      where ins.pre_id = ? 
                      order by ins.ins_fecha desc";

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
   
   
     public function imp($request){
        try {
            $sql = "select *
                    from
                    sigat.inspeccion as ins 
                    where 
                    ins.rut_id = ? "; 
                    
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
    public function buscar($request){
        try {
            $sql = "select * from "
                    . "sigat.predio as pre, "
                    . "sigat.productor as prod, "
                    . "sigat.persona as per, "
                    . "sigat.municipio as mun "
                    . "where ("
                    . "pre.per_cc = prod.per_cc and "
                    . "per.per_cc = prod.per_cc and "
                    . "prod.mun_id = mun.mun_id ) "
                    . "and  "
                    . "pre.pre_id = ? order by pre.pre_id ";
            $ps = $this->conexion->getConexion()->prepare($sql);   
            $ps->execute(array($request["id"]));
            $dato = array();
            if(!$ps->rowCount()>0){
                $dato = array('mensaje' => 'No existe');
            }else{
                $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                   $dato['per_cc'] = $row->per_cc;
                   $dato['per_nombre'] = $row->per_nombre;
                   $dato['per_apellido'] = $row->per_apellido;
                   $dato['pre_id'] = $row->pre_id;
                   $dato['pre_nombre'] = $row->pre_nombre;
                   $dato['pre_area'] = $row->pre_area;
                   $dato['pre_lotes'] = $row->pre_lotes;
                   $dato['mun_id'] = $row->mun_id;
                   $dato['mun_descripcion'] = $row->mun_descripcion;
                   $dato['pre_fecha_reg'] = $row->pre_fecha_reg;
                   $dato['pre_fecha_act'] = $row->pre_fecha_act;
                   $dato['pre_tec_reg'] = $row->pre_tec_reg;
                   $dato['pre_tec_act'] = $row->pre_tec_act;
                }
                $sql = "select * from sigat.tecnico as tec, 
                        sigat.persona as per 
                        where tec.tec_id = ? 
                        and tec.per_cc = per.per_cc ";
                $ps = $this->conexion->getConexion()->prepare($sql);   
                $ps->execute(array($dato["pre_tec_reg"]));
                $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                    $dato['tec_reg_nombre'] = $row->per_nombre." ".$row->per_apellido;  
                }
                $sql = "select * from sigat.tecnico as tec, 
                        sigat.persona as per 
                        where tec.tec_id = ? 
                        and tec.per_cc = per.per_cc ";
                $ps = $this->conexion->getConexion()->prepare($sql);   
                $ps->execute(array($dato["pre_tec_act"]));
                $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                    $dato['tec_act_nombre'] = $row->per_nombre." ".$row->per_apellido;  
                }
                $sql = "select *from sigat.departamento as dep, sigat.municipio as mun
                        where mun.dep_id = dep.dep_id and mun.mun_id = ?; ";
                       
                $ps = $this->conexion->getConexion()->prepare($sql);   
                $ps->execute(array($dato["mun_id"]));
                $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                    $dato['dep_id'] = $row->dep_id;
                    $dato['dep_descripcion'] = $row->dep_descripcion;
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
            $sql = "update sigat.predio set "
                    . "pre_id = ?, "
                    . "pre_nombre = upper (?), "
                    . "pre_area =  ?, "
                    . "pre_lotes = ?, "
                    . "per_cc = ?, "
                    . "pre_fecha_act = ?, "
                    . "pre_tec_act = ? "
                    . "where pre_id = ? ";
            $ps=$this->conexion->getConexion()->prepare($sql);
            $obj = json_decode($request); 
            $ps->execute(array(
                $obj->pre_id,
                $obj->pre_nombre,
                $obj->pre_area,
                $obj->pre_lotes,
                $obj->per_cc,
                $obj->pre_fecha_act,
                $obj->pre_tec_act,
                $obj->pre_id_aux
                ));
            $dato = array();
            if($ps->rowCount()>0){
                $dato['titulo'] = 'Predio';
                $dato['mensaje'] = 'Predio Actualizado';
                $dato['est'] = 'success';
            }else{
                $dato['titulo'] = 'Predio';
                $dato['mensaje'] = 'Predio No se Actualizó';
                $dato['est'] = 'error';
            }
            $this->conexion = null;
        } catch (PDOException $e) {
             $dato['mensaje'] = substr($e->getMessage(),strpos($e->getMessage(),"ERROR"));
             $dato ['titulo'] = 'Productor';
             $dato['est'] = 'error';
        }
        return json_encode($dato);
    }
}
