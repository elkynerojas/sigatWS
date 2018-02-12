<?php

include 'procesoTrampa.inc.php';

class procesoUbi {
    private $conexion;
    function __construct() {
        
        $this->conexion=new conexion();
        $this->conexion->getConexion()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    
    public function listar($request){
        try {
              $sql = "select vig.vig_id, reg.reg_descripcion, dep.dep_descripcion, mun.mun_descripcion, 
                    rut.rut_id, ubi.cod_ruta,tra.tra_id, tit.tit_descripcion, atr.atr_descripcion, 
                    ubi.ubi_fecha, ubi.ubi_long, ubi.ubi_lat, ubi.ubi_alt,ubi.ubi_ubicacion, 
                    pre.pre_nombre, pre.pre_id, per.per_nombre, per.per_apellido, loc.loc_descripcion, 
                    arb.arb_descripcion,rut.rut_nombre 
                    from
                    sigat.ubi as ubi inner join sigat.predio as pre on (ubi.pre_id = pre.pre_id)
                    inner join sigat.productor as prod on (ubi.prod_id = prod.per_cc) 
                    inner join sigat.persona as per on (prod.per_cc = per.per_cc)  
                    inner join sigat.municipio as mun on (prod.mun_id = mun.mun_id) 
                    inner join sigat.departamento as dep on (mun.dep_id = dep.dep_id) 
                    inner join sigat.region as reg on (dep.reg_id = reg.reg_id)
                    inner join sigat.vigilancia as vig on (ubi.vig_id = vig.vig_id) 
                    inner join sigat.ruta as rut on (ubi.rut_id = rut.rut_id) 
                    inner join sigat.trampa as tra on (ubi.tra_id = tra.tra_id) 
                    inner join sigat.tipo_trampa as tit on (tra.tit_id = tit.tit_id) 
                    inner join sigat.atrayente as atr on (tra.atr_id = atr.atr_id)
                    inner join sigat.arbol as arb on (ubi.arb_id = arb.arb_id)
                    inner join sigat.localizacion as loc on (ubi.loc_id = loc.loc_id) 
                    where 
                    upper (mun.mun_descripcion) like upper (?) or 
                    upper (pre.pre_nombre) like upper (?) or 
                    upper (per.per_nombre) like upper (?) or
                    upper (per.per_apellido) like upper (?) or
                    upper (tit.tit_descripcion) like upper (?) or 
                    upper (atr.atr_descripcion) like upper (?) or 
                    upper (rut.rut_nombre) like upper (?) or
                    cast (per.per_cc as varchar) like ? or
                    cast (rut.rut_id as varchar) like ? ";

            $ps = $this->conexion->getConexion()->prepare($sql);
            if(isset($request["id"])){
              $ps->execute(array ('%'.$request["id"].'%',
                                    '%'.$request["id"].'%',
                                    '%'.$request["id"].'%',
                                    '%'.$request["id"].'%',
                                    '%'.$request["id"].'%',
                                    '%'.$request["id"].'%',
                                    '%'.$request["id"].'%',
                                    '%'.$request["id"].'%',
                                    '%'.$request["id"].'%'));
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
    public function consulta ($request){
        try {
              $sql = "select vig.vig_id, vig.vig_descripcion,reg.reg_descripcion,dep.dep_descripcion,
                      mun.mun_descripcion,rut.rut_id, rut.rut_nombre, ubi.cod_ruta, ubi.tra_id, atr.atr_id, atr.atr_descripcion,
                      tit.tit_id, tit.tit_descripcion, ubi.ubi_fecha, ubi.ubi_long, ubi.ubi_lat, ubi.ubi_alt, ubi.ubi_ubicacion, pre.pre_nombre,
                      per.per_nombre, per.per_apellido, loc.loc_id, loc.loc_descripcion, arb.arb_id, arb.arb_descripcion
                   
                      from sigat.ubi as ubi
                      inner join sigat.tecnico as tec on (ubi.tec_id = tec.tec_id)
                      inner join sigat.vigilancia as vig on (ubi.vig_id = vig.vig_id)
                      inner join sigat.ruta as rut on (ubi.rut_id = rut.rut_id)
                      inner join sigat.trampa as tra on (ubi.tra_id = tra.tra_id)
                      inner join sigat.predio as pre on (ubi.pre_id = pre.pre_id)
                      inner join sigat.productor as prod on (ubi.prod_id = prod.per_cc)
                      inner join sigat.persona as per on (prod.per_cc = per.per_cc)
                      inner join sigat.municipio as mun on (prod.mun_id = mun.mun_id)
                      inner join sigat.departamento as dep on (mun.dep_id = dep.dep_id)
                      inner join sigat.region as reg on (dep.reg_id = reg.reg_id)
                      inner join sigat.localizacion as loc on (ubi.loc_id = loc.loc_id)
                      inner join sigat.arbol as arb on (ubi.arb_id = arb.arb_id)
                      inner join sigat.tipo_trampa as tit on (tra.tit_id = tit.tit_id)
                      inner join sigat.atrayente as atr on (tra.atr_id = atr.atr_id)
                      where ubi.rut_id = ? and ubi.ubi_fecha between ? and ?  ";
            $obj = json_decode($request);
            $ps = $this->conexion->getConexion()->prepare($sql);
            $ps->execute(array ($obj->rut_id, $obj->ubi_fecha_ini, $obj->ubi_fecha_fin));
            $dato = array();
            $dato=$ps->fetchAll(PDO::FETCH_OBJ);   
            $this->conexion = null;
        } catch (PDOException $e) {
             $dato = array('mensaje' => substr($e->getMessage(),strpos($e->getMessage(),"ERROR")));
        }
        return json_encode($dato);
    } 
    public function insertar($request){
        try {
            $sql="INSERT INTO sigat.ubi  VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $ps=$this->conexion->getConexion()->prepare($sql);
            $obj = json_decode($request); 
            $ps->execute(array(
                $obj->tec_id,
                $obj->vig_id,
                $obj->rut_id,
                $obj->cod_ruta,
                $obj->tra_id,
                $obj->ubi_fecha,
                $obj->pre_id,
                $obj->prod_id,
                $obj->ubi_long,
                $obj->ubi_lat,
                $obj->ubi_alt,
                $obj->ubi_ubicacion,
                $obj->loc_id,
                $obj->arb_id
                ));
            $dato = array();
            if($ps->rowCount()>0){
                $trampa = new procesoTrampa();
                $resp = $trampa->activar($request);

                if($resp != true){
                    $dato['titulo'] = 'Operación Fallida'; 
                    $dato['mensaje'] = 'Falla al activar la trampa';
                    $dato['est']= 'error';
                }else{
                    $dato['titulo'] = 'Operación Exitosa'; 
                    $dato['mensaje'] = 'Ubicación de Trampa Registrada';
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
     
    public function buscar($request){
        try {
            $sql = "select vig.vig_id, reg.reg_descripcion, dep.dep_descripcion, mun.mun_descripcion, 
                    rut.rut_id, ubi.cod_ruta,tra.tra_id, tit.tit_descripcion, atr.atr_descripcion, 
                    ubi.ubi_fecha, ubi.ubi_long, ubi.ubi_lat, ubi.ubi_alt,ubi.ubi_ubicacion, 
                    pre.pre_nombre, pre.pre_id, per.per_nombre, per.per_apellido, loc.loc_descripcion, 
                    arb.arb_descripcion,rut.rut_nombre 
                    from
                    sigat.ubi as ubi inner join sigat.predio as pre on (ubi.pre_id = pre.pre_id)
                    inner join sigat.productor as prod on (ubi.prod_id = prod.per_cc) 
                    inner join sigat.persona as per on (prod.per_cc = per.per_cc)  
                    inner join sigat.municipio as mun on (prod.mun_id = mun.mun_id) 
                    inner join sigat.departamento as dep on (mun.dep_id = dep.dep_id) 
                    inner join sigat.region as reg on (dep.reg_id = reg.reg_id)
                    inner join sigat.vigilancia as vig on (ubi.vig_id = vig.vig_id) 
                    inner join sigat.ruta as rut on (ubi.rut_id = rut.rut_id) 
                    inner join sigat.trampa as tra on (ubi.tra_id = tra.tra_id) 
                    inner join sigat.tipo_trampa as tit on (tra.tit_id = tit.tit_id) 
                    inner join sigat.atrayente as atr on (tra.atr_id = atr.atr_id)
                    inner join sigat.arbol as arb on (ubi.arb_id = arb.arb_id)
                    inner join sigat.localizacion as loc on (ubi.loc_id = loc.loc_id) 
                    where 
                    ubi.pre_id = ? 
                    and ubi.ubi_fecha = ? 
                    and ubi.tra_id = ? ";
            $ps = $this->conexion->getConexion()->prepare($sql);
            $obj = json_decode($request);   
            $ps->execute(array($obj->pre_id, $obj->ubi_fecha, $obj->tra_id));
            $dato = array();
            if(!$ps->rowCount()>0){
                $dato = array('mensaje' => 'No existe');
            }
            $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                   $dato['vig_id'] = $row->vig_id;
                   $dato['reg_descripcion'] = $row->reg_descripcion;
                   $dato['dep_descripcion'] = $row->dep_descripcion;
                   $dato['mun_descripcion'] = $row->mun_descripcion;
                   $dato['rut_id'] = $row->rut_id;
                   $dato['cod_ruta'] = $row->cod_ruta;
                   $dato['tit_descripcion'] = $row->tit_descripcion;
                   $dato['tra_id'] = $row->tra_id; 
                   $dato['atr_descripcion'] = $row->atr_descripcion;
                   $dato['ubi_fecha'] = $row->ubi_fecha; 
                   $dato['ubi_long'] = $row->ubi_long; 
                   $dato['ubi_lat'] = $row->ubi_lat;
                   $dato['ubi_alt'] = $row->ubi_alt; 
                   $dato['ubi_ubicacion'] = $row->ubi_ubicacion;
                   $dato['pre_nombre'] = $row->pre_nombre;
                   $dato['per_nombre'] = $row->per_nombre;
                   $dato['per_apellido'] = $row->per_apellido;
                   $dato['loc_descripcion'] = $row->loc_descripcion;
                   $dato['arb_descripcion'] = $row->arb_descripcion;
                   $dato['rut_nombre'] = $row->rut_nombre;  
                }
            
            $this->conexion = null;
        } catch (PDOException $e) {
            $dato = array('mensaje' => substr($e->getMessage(),strpos($e->getMessage(),"ERROR")));
        }
        return json_encode($dato);
    }
    public function listarTrampas($request){
        try {
            $sql = "select tra_id from sigat.ubi where rut_id = ? ";
            $ps = $this->conexion->getConexion()->prepare($sql);   
            $ps->execute(array($request["id"]));
            $dato = array();
            if(!$ps->rowCount()>0){
                $dato = array('mensaje' => 'No existe');
            }
            $dato=$ps->fetchAll(PDO::FETCH_OBJ); 
                
            $this->conexion = null;
        } catch (PDOException $e) {
            $dato = array('mensaje' => substr($e->getMessage(),strpos($e->getMessage(),"ERROR")));
        }
        return json_encode($dato);
    }
     public function asignarPredio($request){
        try {
            $sql = "select pre_id from sigat.ubi where tra_id = ? ";
            $ps = $this->conexion->getConexion()->prepare($sql);   
            $ps->execute(array($request["id"]));
            $dato = array();
            if(!$ps->rowCount()>0){
                $dato = array('mensaje' => 'No existe');
            }
             $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                   $dato['pre_id'] = $row->pre_id; 
                }  
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
                    sigat.ubi as ubi 
                    where 
                    ubi.rut_id = ?"; 
                    
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
     
}
