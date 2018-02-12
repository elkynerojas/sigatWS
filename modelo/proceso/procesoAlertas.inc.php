<?php



class procesoAlertas {
    private $conexion;
    function __construct() {
        
        $this->conexion=new conexion();
        $this->conexion->getConexion()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    
    public function calcularMTD($request){
        try {
              $sql = "select (sum (cap_machos) + sum (cap_hembras)) as nmc from sigat.cap as cap
                      inner join sigat.inspeccion as ins on (cap.pre_id = ins.pre_id)
                      where cap.pre_id = ? and 
                      cap_fecha_cap between 
                      (select ins_fecha from sigat.inspeccion where pre_id = ? order by ins_fecha desc limit 1) and current_date";

            $ps = $this->conexion->getConexion()->prepare($sql);
            if(isset($request["id"])){
              $ps->execute(array ($request["id"],$request["id"]));
            }else{
                echo 'No Existe un paramétro para filtrar los datos';
            }
            $dato = array();
            $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                   $dato['nmc'] = $row->nmc;
                }
           $sql = "select (current_date - (select ins_fecha from sigat.inspeccion where pre_id = ? 
           order by ins_fecha desc limit 1)) as nde";

            $ps = $this->conexion->getConexion()->prepare($sql);
            if(isset($request["id"])){
              $ps->execute(array ($request["id"]));
            }else{
                echo 'No Existe un paramétro para filtrar los datos';
            }
            $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                   $dato['nde'] = $row->nde;
                }
          $sql = "select count (ubi.tra_id) as ntr from sigat.ubi as ubi
                  inner join sigat.trampa as tra on(ubi.tra_id = tra.tra_id)
                  where ubi.pre_id = ? and tra.est_id = 1";

            $ps = $this->conexion->getConexion()->prepare($sql);
            if(isset($request["id"])){
              $ps->execute(array ($request["id"]));
            }else{
                echo 'No Existe un paramétro para filtrar los datos';
            }
            $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                   $dato['ntr'] = $row->ntr;
                }
          $mtd =  $dato['nmc']/($dato['ntr']*$dato['nde']);
          $dato['mtd'] = round ($mtd,2);
           $sql = "select pre.pre_id, pre.pre_nombre,rut.rut_id from sigat.predio as pre
                  inner join sigat.productor as prod on (pre.per_cc = prod.per_cc)
                  inner join sigat.municipio as mun on (prod.mun_id = mun.mun_id)
                  inner join sigat.ruta as rut on (rut.mun_id = mun.mun_id)
                  where pre_id = ? ";

            $ps = $this->conexion->getConexion()->prepare($sql);
            if(isset($request["id"])){
              $ps->execute(array ($request["id"]));
            }else{
                echo 'No Existe un paramétro para filtrar los datos';
            }
            
            $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                   $dato['pre_id'] = $row->pre_id;
                   $dato['pre_nombre'] = $row->pre_nombre;
                   $dato['rut_id'] = $row->rut_id;
                }
            $this->conexion = null;
        } catch (PDOException $e) {
             $dato = array('mensaje' => substr($e->getMessage(),strpos($e->getMessage(),"ERROR")));
        }
        return json_encode($dato);
    } 
    
    public function insertar($request){
        try {
            $sql="INSERT INTO sigat.alertas  VALUES (?,?,?,?,?)";
            $ps=$this->conexion->getConexion()->prepare($sql);
            $obj = json_decode($request); 
            $ps->execute(array(
                $obj->pre_id,
                $obj->ale_fecha,
                $obj->ale_mtd,
                $obj->tec_id,
                $obj->rut_id
                ));
            $dato = array();
            if($ps->rowCount()>0){
                if($resp != true){
                    $dato['titulo'] = 'Operación Exitosa'; 
                    $dato['mensaje'] = 'Se registró la Alerta correctamente';
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
    public function listar($request){
        try {
            $sql = "select pre.pre_id, pre.pre_nombre, ale.ale_fecha, ale.ale_mtd, per.per_nombre, per.per_apellido 
                    from sigat.alertas as ale 
                    inner join sigat.predio as pre on (ale.pre_id = pre.pre_id)
                    inner join sigat.tecnico as tec on (ale.tec_id = tec.tec_id)
                    inner join sigat.persona as per on (tec.per_cc = per.per_cc)
                    where cast (ale.ale_fecha as varchar) like ? 
                    or cast (pre.pre_id as varchar) like ?
                    or upper(pre.pre_nombre) like upper(?)
                    or upper (per.per_nombre) like upper (?)
                    or upper (per.per_apellido) like upper (?)
                    or cast(tec.tec_id as varchar) like ?
                    order by ale_fecha desc";
            $ps = $this->conexion->getConexion()->prepare($sql);   
            $ps->execute(array('%'.$request["id"].'%',
                               '%'.$request["id"].'%',
                               '%'.$request["id"].'%',
                               '%'.$request["id"].'%',
                               '%'.$request["id"].'%',
                               '%'.$request["id"].'%'));
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
                    sigat.alertas as ale 
                    where 
                    ale.rut_id = ?"; 
                    
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
