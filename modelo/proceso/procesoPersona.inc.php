<?php


class procesoPersona {
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
            $this->conexion = null;
        } catch (PDOException $e) {
            $dato = array('mensaje' => substr($e->getMessage(),strpos($e->getMessage(),"ERROR")));
        }
        return json_encode($dato);
    }
    
    public function listar($request){
        try {
            $sql= "select * from sigat.persona as per, "
                    . "sigat.genero as gen, "
                    . "sigat.tipo_persona as tip "
                    . " where (per.gen_id = gen.gen_id "
                    . "and per.tip_id = tip.tip_id) "
                    . "and lower(per.per_nombre) like lower (?) "
                    . "or lower (per.per_apellido) like lower(?) "
                    . "order by per.per_apellido";

            $ps = $this->conexion->getConexion()->prepare($sql);
            if(isset($request["id"])){
              $ps->execute(array ('%'.$request["id"].'%','%'.$request["id"].'%'));
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
                    . "per_direccion= ?, "
                    . "tip_id =? "
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
                $obj->tip_id,
                $obj->per_cc_aux
                ));
            $dato = array();
            if($ps->rowCount()>0){
                $dato = array('mensaje' => 'Actualizado');
            }
            $this->conexion = null;
        } catch (PDOException $e) {
             $dato = array('mensaje' => substr($e->getMessage(),strpos($e->getMessage(),"ERROR")));
        }
        return json_encode($dato);
    }
    
    public function buscar($request){
        try {
            $sql = "select * "
                    . "from sigat.persona as per, "
                    . "sigat.tipo_persona as tip, "
                    . "sigat.genero as gen "
                    . "where per.per_cc = ? "
                    . "and per.tip_id = tip.tip_id "
                    . "and per.gen_id = gen.gen_id "
                    . "order by per.per_nombre ";
            $ps = $this->conexion->getConexion()->prepare($sql);   
            $ps->execute(array($request["id"]));
            $dato = array();
            if(!$ps->rowCount()>0){
                $dato = array('mensaje' => 'No existe');
            }
            $dato=$ps->fetchAll(PDO::FETCH_OBJ);
//            while($row=$ps->fetch(PDO::FETCH_OBJ)) {
//                $dato = array('per_id' => $row->per_id,
//                    'per_nombre' => $row->per_nombre,
//                    'per_apellido' => $row->per_apellido,
//                    'per_genero' => $row->per_genero,
//                    'per_direccion' => $row->per_direccion,
//                    'per_telefono'=> $row->per_telefono,
//                    'per_correo'=>$row->per_correo,
//                    'tip_id'=>$row->tip_id,
//                    'tip_descripcion'=>$row->tip_descripcion,
//                    'pue_id'=>$row->pue_id,
//                    'pue_nombre'=>$row->pue_nombre,
//                    );
//            } 
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
