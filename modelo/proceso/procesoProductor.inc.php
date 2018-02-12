<?php

//include 'procesoPersona.inc.php';
//include 'procesoTecnico.inc.php';
class procesoProductor {
    private $conexion;
    function __construct() {
        
        $this->conexion=new conexion();
        $this->conexion->getConexion()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    public function insertar($request){
        try {
            
            //$per = new procesoPersona();
            //$per->insertar($request);
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
            
            $sql="INSERT INTO sigat.productor  VALUES (?,?,?,?,?,?,?)";
            $ps=$this->conexion->getConexion()->prepare($sql);
            //$obj = json_decode($request); 
            $ps->execute(array(
                $obj->per_cc,
                $obj->prod_fecha_reg,
                $obj->prod_fecha_act,
                $obj->prod_tec_reg,
                $obj->prod_tec_act,
                $obj->prod_estado,
                $obj->mun_id
                ));
            $dato = array();
            if($ps->rowCount()>0){
                $dato['titulo'] = 'Productor'; 
                $dato['mensaje'] = 'Productor Registrado';
                $dato['est']= 'success';
            }
            $this->conexion = null;
        } catch (PDOException $e) {
            
            $dato['titulo'] = 'Productor'; 
            $dato['mensaje'] = substr($e->getMessage(),strpos($e->getMessage(),"ERROR"));
            $dato['est']= 'error';
        }
        return json_encode($dato);
    }
    
    public function listar($request){
        try {
              $sql = "select *from sigat.persona as per, "
                      . "sigat.productor as prod, "
                      . "sigat.genero as gen, "
                      . "sigat.municipio as mun "
                      . "where ( "
                      . "per.per_cc = prod.per_cc "
                      . "and per.gen_id = gen.gen_id "
                      . "and prod.mun_id = mun.mun_id ) "
                      . "and ( "
                      . "cast(per.per_cc as varchar) like ? "
                      . "or upper (per.per_nombre) like upper(?) "
                      . "or upper (per.per_apellido) like upper(?)  "
                      . "or upper (mun.mun_descripcion) like upper(?)  "
                      . "or upper (per.per_direccion) like upper(?))  "
                      . "order by per.per_apellido ";

            $ps = $this->conexion->getConexion()->prepare($sql);
            if(isset($request["id"])){
              $ps->execute(array ('%'.$request["id"].'%','%'.$request["id"].'%','%'.$request["id"].'%','%'.$request["id"].'%','%'.$request["id"].'%'));
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
              $sql = "select *from sigat.persona as per, "
                      . "sigat.productor as prod, "
                      . "sigat.genero as gen, "
                      . "sigat.municipio as mun, "
                      . "sigat.ruta as rut "
                      . "where ( "
                      . "per.per_cc = prod.per_cc "
                      . "and per.gen_id = gen.gen_id "
                      . "and prod.mun_id = mun.mun_id  "
                      . "and rut.mun_id = mun.mun_id ) "
                      . "and ( rut.rut_id = ? )";

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
            
            $sql="SELECT * FROM sigat.productor as prod WHERE prod.per_cc = ? ";

            $ps=$this->conexion->getConexion()->prepare($sql);
            $obj = json_decode($request);
            $dato = array(); 
            $ps->execute(array(
                $obj->per_cc_aux
            ));
            
            if($ps->rowCount()>0){
                $sql="update sigat.persona 
                    set per_cc = ?,
                    per_nombre = ?,
                    per_apellido = ?,
                    gen_id = ?, 
                    per_telefono = ?,
                    per_direccion = ?, 
                    per_correo = ?,
                    tip_id = ?
                    where per_cc = ? ";

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
                    $obj->tip_id,
                    $obj->per_cc_aux
                    ));
              $sql="update sigat.productor 
                    set per_cc = ?,
                    prod_fecha_reg = ?,
                    prod_fecha_act = ?,
                    prod_tec_reg = ?,
                    prod_tec_act = ?,
                    prod_estado = ?,
                    mun_id = ?
                    where per_cc = ? ";

                $ps=$this->conexion->getConexion()->prepare($sql);
                $obj = json_decode($request); 
                $ps->execute(array(
                    $obj->per_cc,
                    $obj->prod_fecha_reg,
                    $obj->prod_fecha_act,
                    $obj->prod_tec_reg,
                    $obj->prod_tec_act, 
                    $obj->prod_estado,
                    $obj->mun_id,
                    $obj->per_cc_aux
                    ));
               
                if($ps->rowCount()>0){
                    $dato['titulo'] = 'Productor';
                    $dato['mensaje'] = 'Sincronizado';
                    $dato['est'] = 'success';
                }else{
                   
                }  
            }else{
                $sql="INSERT INTO sigat.persona VALUES (?,upper(?),upper(?),?,?,upper(?),lower(?),?) ";

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
                if($ps->rowCount()>0){
                  $sql="INSERT INTO sigat.productor VALUES (?,?,?,?,?,?,?) ";

                  $ps=$this->conexion->getConexion()->prepare($sql);
                  $obj = json_decode($request); 
                  $ps->execute(array(
                      $obj->per_cc,
                      $obj->prod_fecha_reg,
                      $obj->prod_fecha_act,
                      $obj->prod_tec_reg,
                      $obj->prod_tec_act,
                      $obj->prod_estado,
                      $obj->mun_id
                      ));

                  if($ps->rowCount()>0){
                      $dato['titulo'] = 'Productor';
                      $dato['mensaje'] = 'Sincronizado';
                      $dato['est'] = 'success';
                  }else{

                  } 
                }
                 
            }
            $this->conexion = null;
           
        } catch (PDOException $e) {
            
            $dato['titulo'] = 'Productor'; 
            $dato['mensaje'] = substr($e->getMessage(),strpos($e->getMessage(),"ERROR"));
            $dato['est']= 'error';
        }
        return json_encode($dato);
    }
    public function listarxMunicipio($request){
        try {
              $sql = "select *from sigat.persona as per, "
                      . "sigat.productor as prod "
                      . "where  "
                      . "per.per_cc = prod.per_cc "
                      . "and  "
                      . "prod.mun_id = ?  "
                      . "order by per.per_apellido ";

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
    public function actualizar($request){
        try {
            $sql = "update sigat.persona set "
                    . "per_cc= ?, "
                    . "per_nombre= upper (?), "
                    . "per_apellido= upper (?), "
                    . "gen_id = ?, "
                    . "per_telefono= ?, "
                    . "per_direccion= upper (?), "
                    . "tip_id =? "
                    . "where per_cc = ? ";
            $ps=$this->conexion->getConexion()->prepare($sql);
            $obj = json_decode($request); 
            $ps->execute(array(
                $obj->per_cc,
                $obj->per_nombre,
                $obj->per_apellido,
                $obj->gen_id,
                $obj->per_telefono,
                $obj->per_direccion,
                $obj->tip_id,
                $obj->per_cc_aux
                ));
            $dato = array();
            if($ps->rowCount()>0){
                $sql = "update sigat.productor set "
                      ."prod_tec_act = ?, "
                      ."prod_fecha_act = ?, "
                      ."mun_id = ?, "
                      ."per_cc = ? "
                      ."where per_cc = ? ";
                $ps=$this->conexion->getConexion()->prepare($sql);
            
                $ps->execute(array(
                    $obj->prod_tec_act,
                    $obj->prod_fecha_act,
                    $obj->mun_id,
                    $obj->per_cc,
                    $obj->per_cc_aux
                ));
                if($ps->rowCount()>0){
                    $dato['titulo'] = 'Productor';
                    $dato['mensaje'] = 'Productor Actualizado';
                    $dato['est'] = 'success';
                }
            }
            $this->conexion = null;
        } catch (PDOException $e) {
             $dato['mensaje'] = substr($e->getMessage(),strpos($e->getMessage(),"ERROR"));
             $dato ['titulo'] = 'Productor';
             $dato['est'] = 'error';
        }
        return json_encode($dato);
    }
    
    public function buscar($request){
        try {
            $sql = "select * from sigat.persona as per, "
                    . "sigat.productor as prod  "
                    . "where prod.per_cc = ? "
                    . "and prod.per_cc = per.per_cc ";
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
                   $dato['gen_id'] = $row->gen_id;
                   $dato['per_telefono'] = $row->per_telefono;
                   $dato['per_direccion'] = $row->per_direccion;
                   $dato['per_correo'] = $row->per_correo;
                   $dato['prod_fecha_reg'] = $row->prod_fecha_reg;
                   $dato['prod_fecha_act'] = $row->prod_fecha_act;
                   $dato['prod_tec_reg'] = $row->prod_tec_reg;
                   $dato['prod_tec_act'] = $row->prod_tec_act;
                   $dato['prod_estado'] = $row->prod_estado;
                   $dato['mun_id'] = $row->mun_id;  
                }
                $sql = "select * from sigat.tecnico as tec, 
                        sigat.persona as per 
                        where tec.tec_id = ? 
                        and tec.per_cc = per.per_cc ";
                $ps = $this->conexion->getConexion()->prepare($sql);   
                $ps->execute(array($dato["prod_tec_reg"]));
                $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                    $dato['tec_reg_nombre'] = $row->per_nombre." ".$row->per_apellido;  
                }
                $sql = "select * from sigat.tecnico as tec, 
                        sigat.persona as per 
                        where tec.tec_id = ? 
                        and tec.per_cc = per.per_cc ";
                $ps = $this->conexion->getConexion()->prepare($sql);   
                $ps->execute(array($dato["prod_tec_act"]));
                $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                    $dato['tec_act_nombre'] = $row->per_nombre." ".$row->per_apellido;  
                }
                $sql = "select distinct (dep_id) from sigat.municipio as mun 
                        where mun.mun_id = ? ";
                       
                $ps = $this->conexion->getConexion()->prepare($sql);   
                $ps->execute(array($dato["mun_id"]));
                $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                    $dato['dep_id'] = $row->dep_id;  
                }
            }
            $this->conexion = null;   
        } catch (PDOException $e) {
            $dato = array('mensaje' => substr($e->getMessage(),strpos($e->getMessage(),"ERROR")));
        }
        return json_encode($dato);
    }

    public function listarProductor($buscar){
        try {

            $sql="select * from "
                    . "sigat.persona as pe, "
                    . "sigat.productor as pr "
                    . "where pe.tip_id=3 and pr.prod_id = pe.per_cc";
                    
                $ps = $this->conexion->getConexion()->prepare($sql);
                $ps->execute(NULL);
                $resultado=  array();
                $lista=$ps->fetchAll(PDO::FETCH_OBJ);
                $this->conexion=null;
        } catch (PDOException $e) {
            echo 'Falló la conexión: ' . $e->getMessage();
        }
        return $lista;
    }
}