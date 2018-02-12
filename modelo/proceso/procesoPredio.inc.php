<?php


class procesoPredio {
    private $conexion;
    function __construct() {
        $this->conexion=new conexion();
        $this->conexion->getConexion()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    public function AsignarCodigo($request){
        try {
            
            $sql = "Select max(pre_id) from sigat.predio 
            where cast (pre_id as varchar) like ? ";
            $ps=$this->conexion->getConexion()->prepare($sql);
             $obj = json_decode($request); 
            $ps->execute(array($obj->mun_id));
            if($ps->rowCount()>0){
                $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                   $pre_id = $row->max + 1;
                }
            }else{
                $pre_id = $obj->mun_id.'0001';
            }
        } catch (PDOException $e) {
            
        }
        return $pre_id;
    }
    public function insertar($request){
        
        try {
            $sql = "Select max(pre_id) as mayor from sigat.predio 
            where cast (pre_id as varchar) like ? ";
            $ps=$this->conexion->getConexion()->prepare($sql);
             $obj = json_decode($request); 
            $ps->execute(array($obj->mun_id.'%'));
            if($ps->rowCount()>0){
                $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                   
                   if($row->mayor){
                       $codigo = $row->mayor + 1;
                   }else{
                       $codigo = $obj->mun_id."0001";
                   }
                }
            }else{
                
            }
            
            $sql="INSERT INTO sigat.predio  VALUES (?, UPPER (?), ?,?,?,?,?,?,?)";
            $ps=$this->conexion->getConexion()->prepare($sql);
            $obj = json_decode($request); 
            $ps->execute(array(
                $codigo,
                $obj->pre_nombre,
                $obj->pre_area,
                $obj->pre_lotes,
                $obj->per_cc,
                $obj->pre_fecha_reg,
                $obj->pre_fecha_act,
                $obj->pre_tec_reg,
                $obj->pre_tec_act
                ));
            $dato = array();
            if($ps->rowCount()>0){
                $dato['titulo'] = 'Predio'; 
                $dato['mensaje'] = 'Predio Registrado con el código '.$codigo;
                $dato['est']= 'success';
            }
            $this->conexion = null;
        } catch (PDOException $e) {
            
            $dato['titulo'] = 'Predio'; 
            $dato['mensaje'] = substr($e->getMessage(),strpos($e->getMessage(),"ERROR"));
            //$dato['mensaje'] = $mensaje;
            $dato['est']= 'error';
        }
        return json_encode($dato);
    }
    public function listar($request){
        try {
              $sql = "select * from "
                      . "sigat.predio as pre, "
                      . "sigat.productor as prod, "
                      . "sigat.persona as per, "
                      . "sigat.municipio as mun "
                      . "where pre.per_cc = prod.per_cc "
                      . "and prod.per_cc = per.per_cc "
                      . "and prod.mun_id = mun.mun_id "
                      . "and ( "
                      . "upper (pre.pre_nombre) like upper (?) "
                      . "or upper (per.per_nombre) like upper (?) "
                      . "or upper (per.per_apellido) like upper (?) "
                      . "or upper (mun.mun_descripcion) like upper (?) "
                      . "or cast(pre.pre_id as varchar ) like ? "
                      . "or cast(prod.per_cc as varchar ) like ?) "
                      . "order by pre.pre_nombre";

            $ps = $this->conexion->getConexion()->prepare($sql);
            if(isset($request["id"])){
              $ps->execute(array ('%'.$request["id"].'%','%'.$request["id"].'%','%'.$request["id"].'%','%'.$request["id"].'%','%'.$request["id"].'%','%'.$request["id"].'%'));
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
    public function listarxMunicipio($request){
        try {
              $sql = "select * from "
                      . "sigat.predio as pre, "
                      . "sigat.productor as prod, "
                      . "sigat.persona as per, "
                      . "sigat.municipio as mun "
                      . "where pre.per_cc = prod.per_cc "
                      . "and prod.per_cc = per.per_cc "
                      . "and prod.mun_id = mun.mun_id "
                      . "and prod.mun_id = ?  ";

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
    public function listarxUbi($request){
        try {
              $sql = "select distinct(pre.pre_id) 
                      from sigat.predio as pre inner join sigat.ubi as ubi on (ubi.pre_id = pre.pre_id)
                      where ubi.rut_id = ? ";

            $ps = $this->conexion->getConexion()->prepare($sql);
            if(isset($request["id"])){
              $ps->execute(array ($request["id"]));
            }else{
                echo 'No Existe un paramétro para filtrar los datos';
            }
            $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                   $pre_id = $row->pre_id;
                }
           $sql = "select pre_id, pre_nombre 
                      from sigat.predio as pre 
                      where pre.pre_id = ? ";

            $ps = $this->conexion->getConexion()->prepare($sql);
            if(isset($request["id"])){
              $ps->execute(array ($pre_id));
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
    public function listarxRuta($request){
        try {
              $sql = "select pre.pre_id, pre.pre_nombre
                      from sigat.predio as pre 
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
    public function imp($request){
        try {
              $sql = "select * from "
                      . "sigat.predio as pre, "
                      . "sigat.productor as prod, "
                      . "sigat.municipio as mun, "
                      . "sigat.ruta as rut "
                      . "where pre.per_cc = prod.per_cc "
                      . "and prod.mun_id = rut.mun_id "
                      . "and prod.mun_id = mun.mun_id "
                      . "and rut.rut_id = ?  ";

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
            
            $sql="select * from sigat.predio as pre where pre.pre_id = ? ";

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
                    pre_tec_act = ?
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
