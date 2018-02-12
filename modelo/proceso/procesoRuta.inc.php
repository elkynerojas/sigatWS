<?php


class procesoRuta {
    private $conexion;
    function __construct() {
        
        $this->conexion=new conexion();
        $this->conexion->getConexion()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    
    public function listar($request){
        try {
            $sql= "select rut_id, rut_nombre from sigat.ruta as rut "
                    . "where rut.cac_id = ? "
                    . "order by rut.rut_id ";

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
    public function insertar($request){
        try {
            $sql= "select rut_id, rut_nombre from sigat.ruta as rut "
                    . "where rut.cac_id = ? "
                    . "order by rut.rut_id ";

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
            $sql = "select * "
                    . "from sigat.ruta as rut "
                    . "where rut.mun_id = ? "
                    . "order by rut.rut_nombre ";
            $ps = $this->conexion->getConexion()->prepare($sql);   
            $ps->execute(array($request["id"]));
            $dato = array();
            if(!$ps->rowCount()>0){
                $dato = array('mensaje' => 'No existe');
            }
            $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                   $dato['rut_id'] = $row->rut_id;
                   $dato['rut_nombre'] = $row->rut_nombre;
                   $dato['cac_id'] = $row->cac_id;
                   $dato['rut_descripcion'] = $row->rut_descripcion;
                   $dato['rut_fecha_reg'] = $row->rut_fecha_reg;
                   $dato['rut_fecha_act'] = $row->rut_fecha_act;
                   $dato['rut_adm_reg'] = $row->rut_adm_reg;
                   $dato['mun_id'] = $row->mun_id;  
                }

            $this->conexion = null;
        } catch (PDOException $e) {
            $dato = array('mensaje' => substr($e->getMessage(),strpos($e->getMessage(),"ERROR")));
        }
        return json_encode($dato);
    }
    public function imp($request){
        try {
            $sql = "select * "
                    . "from sigat.ruta as rut "
                    . "where rut.rut_id = ? "
                    . "order by rut.rut_nombre ";
            $ps = $this->conexion->getConexion()->prepare($sql);   
            $ps->execute(array($request["id"]));
            $dato = array();
            if(!$ps->rowCount()>0){
                $dato = array('mensaje' => 'No existe');
            }else{
                $dato=$ps->fetchAll(PDO::FETCH_OBJ);
            }
            
            $this->conexion = null;
        } catch (PDOException $e) {
            $dato = array('mensaje' => substr($e->getMessage(),strpos($e->getMessage(),"ERROR")));
        }
        return json_encode($dato);
    }
   public function mtdRuta($request){
        try {
            $sql = "select  "
                    . "count(tra_id) as ntr from sigat.ubi where rut_id = ? ";
            $ps = $this->conexion->getConexion()->prepare($sql);   
            $ps->execute(array($request["id"]));
            $dato = array();
            if(!$ps->rowCount()>0){
                $dato = array('mensaje' => 'ntr no definido');
            }else{
                 $lista=$ps->fetchAll(PDO::FETCH_OBJ); 
                foreach ($lista as $row) {
                   $ntr = $row->ntr;  
                }
            }
            
            $this->conexion = null;
        } catch (PDOException $e) {
            $dato = array('mensaje' => substr($e->getMessage(),strpos($e->getMessage(),"ERROR")));
        }
        return json_encode($dato);
    }
}
