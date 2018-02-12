<?php

include 'procesoTrampa.inc.php';

class procesoCap {
    private $conexion;
    function __construct() {
        
        $this->conexion=new conexion();
        $this->conexion->getConexion()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    
    public function listar($request){
        try {
              $sql = "select cap.tec_id,vig.vig_id, cap.cap_semana, cap.cap_ano, cac_descripcion, reg.reg_descripcion, 
                      dep.dep_descripcion,mun.mun_descripcion, ubi.rut_id, ubi.cod_ruta, cap.tra_id, tit.tit_descripcion, esm.esm_id, esm.esm_descripcion, 
                      cap.cap_fecha_cap, per.per_nombre,per.per_apellido, cap.cap_machos, cap.cap_hembras, est.est_descripcion,
                      fen.fen_descripcion, cap.cap_fecha_dia, cap.cap_mue
                      from sigat.cap as cap inner join sigat.tecnico as tec on (cap.tec_id = tec.tec_id)
                      inner join sigat.centro_acopio as cac on (tec.cac_id = cac.cac_id)
                      inner join sigat.ubi as ubi on (cap.tra_id = ubi.tra_id)
                      inner join sigat.vigilancia as vig on (cap.vig_id = vig.vig_id)
                      inner join sigat.trampa as tra on (tra.tra_id = cap.tra_id)
                      inner join sigat.tipo_trampa as tit on (tra.tit_id = tit.tit_id)
                      inner join sigat.ruta as rut on (ubi.rut_id = rut.rut_id)
                      inner join sigat.municipio as mun on (rut.mun_id = mun.mun_id)
                      inner join sigat.departamento as dep on (mun.dep_id = dep.dep_id)
                      inner join sigat.region as reg on (dep.reg_id = reg.reg_id)
                      inner join sigat.especie_mosca as esm on (cap.esm_id = esm.esm_id)
                      inner join sigat.persona as per on (tec.per_cc = per.per_cc)
                      inner join sigat.estado as est on (tra.est_id = est.est_id)
                      inner join sigat.fenologia as fen on (cap.fen_id = fen.fen_id)
                      where 
                      upper (mun.mun_descripcion) like upper (?) or 
                      upper (per.per_nombre) like upper (?) or 
                      upper (per.per_apellido) like upper (?) or
                      upper (per.per_apellido) like upper (?) or
                      cast(tra.tra_id as varchar) like ? or 
                      upper (esm.esm_descripcion) like upper (?) or 
                      upper (rut.rut_nombre) like upper (?) or
                      cast (tec.tec_id as varchar) like ? or
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
  public function consulta($request){
    try {
              $sql = "select cap.tec_id,vig.vig_id, cap.cap_semana, cap.cap_ano, cac_descripcion, reg.reg_descripcion, 
                      dep.dep_descripcion,mun.mun_descripcion, ubi.rut_id, ubi.cod_ruta, cap.tra_id, tit.tit_descripcion, esm.esm_id, esm.esm_descripcion, 
                      cap.cap_fecha_cap, per.per_nombre,per.per_apellido, cap.cap_machos, cap.cap_hembras, est.est_descripcion,
                      fen.fen_descripcion, cap.cap_fecha_dia, cap.cap_mue
                      from sigat.cap as cap inner join sigat.tecnico as tec on (cap.tec_id = tec.tec_id)
                      inner join sigat.centro_acopio as cac on (tec.cac_id = cac.cac_id)
                      inner join sigat.ubi as ubi on (cap.tra_id = ubi.tra_id)
                      inner join sigat.vigilancia as vig on (cap.vig_id = vig.vig_id)
                      inner join sigat.trampa as tra on (tra.tra_id = cap.tra_id)
                      inner join sigat.tipo_trampa as tit on (tra.tit_id = tit.tit_id)
                      inner join sigat.ruta as rut on (ubi.rut_id = rut.rut_id)
                      inner join sigat.municipio as mun on (rut.mun_id = mun.mun_id)
                      inner join sigat.departamento as dep on (mun.dep_id = dep.dep_id)
                      inner join sigat.region as reg on (dep.reg_id = reg.reg_id)
                      inner join sigat.especie_mosca as esm on (cap.esm_id = esm.esm_id)
                      inner join sigat.persona as per on (tec.per_cc = per.per_cc)
                      inner join sigat.estado as est on (tra.est_id = est.est_id)
                      inner join sigat.fenologia as fen on (cap.fen_id = fen.fen_id)
                      where cap.rut_id = ? and cap.cap_fecha_cap between ? and ? ";
            $obj = json_decode($request);
            $ps = $this->conexion->getConexion()->prepare($sql);
            $ps->execute(array ($obj->rut_id, $obj->cap_fecha_ini, $obj->cap_fecha_fin));
            $dato = array();
            $dato=$ps->fetchAll(PDO::FETCH_OBJ);   
            $this->conexion = null;
        } catch (PDOException $e) {
             $dato = array('mensaje' => substr($e->getMessage(),strpos($e->getMessage(),"ERROR")));
        }
        return json_encode($dato);
  }
    public function ExtractYear($fecha){
        try {   
            
            $sql="select extract (year from date '$fecha')";                        
                $ps = $this->conexion->getConexion()->prepare($sql);
                $ps->execute(NULL);
                $lista=$ps->fetchAll(PDO::FETCH_OBJ);               
                $this->conexion=null;
        } catch (PDOException $e) {
            echo 'Falló la conexión: ' . $e->getMessage();
        }
        return $lista;
    }
    public function ExtractWeek($fecha){
        try {   
            
            $sql="select extract (week from date '$fecha')";                        
                $ps = $this->conexion->getConexion()->prepare($sql);
                $ps->execute(NULL);
                $lista=$ps->fetchAll(PDO::FETCH_OBJ);               
                $this->conexion=null;
        } catch (PDOException $e) {
            echo 'Falló la conexión: ' . $e->getMessage();
        }
        return $lista;
    }
    public function insertar($request){
        
        try {
             $obj = json_decode($request);
             $sql="select extract (year from date '$obj->cap_fecha_cap' )";                        
                $ps = $this->conexion->getConexion()->prepare($sql);
                $ps->execute(NULL);
                $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                   $ano = $row->date_part;
                }
          
              $sql="select extract (week from date '$obj->cap_fecha_cap')";                        
                $ps = $this->conexion->getConexion()->prepare($sql);
                $ps->execute(NULL);
                $lista=$ps->fetchAll(PDO::FETCH_OBJ);
                foreach ($lista as $row) {
                   $semana = $row->date_part;
               } 
          
            $sql="INSERT INTO sigat.cap  VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $ps=$this->conexion->getConexion()->prepare($sql);
            $ps->execute(array(
                $obj->tec_id,
                $obj->cac_id,
                $semana,
                $ano,
                $obj->vig_id,
                $obj->rut_id,
                $obj->tra_id,
                $obj->esm_id,
                $obj->cap_fecha_cap,
                $obj->cap_machos,
                $obj->cap_hembras,
                $obj->fen_id,
                $obj->cap_fecha_dia,
                $obj->cap_mue,
                $obj->pre_id
                ));
            $dato = array();
            if($ps->rowCount()>0){
                    $dato['titulo'] = 'Operación Exitosa'; 
                    $dato['mensaje'] = 'La Captura se registró correctamente';
                    $dato['est']= 'success';
                }else{
                    $dato['titulo'] = 'Operación Fallida'; 
                    $dato['mensaje'] = 'Error al registrar la captura';
                    $dato['est']= 'error';
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
    public function imp($request){
        try {
            $sql = "select *
                    from
                    sigat.cap as cap 
                    where 
                    cap.rut_id = ?"; 
                    
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
