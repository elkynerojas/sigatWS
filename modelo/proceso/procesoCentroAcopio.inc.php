<?php


class procesoCentroAcopio {
    private $conexion;
    function __construct() {
        
        $this->conexion=new conexion();
        $this->conexion->getConexion()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    
    public function listar($request){
        try {
            $sql= "select * from sigat.centro_acopio as cac "
                    . "where cac.cac_id = ? "
                    . "order by cac.cac_descripcion ";

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
                    . "from sigat.centro_acopio as cac "
                    . "where cac.cac_id = ? "
                    . "order by cac.cac_descripcion ";
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
            $sql = "select cac.cac_id, cac.cac_descripcion, cac.dep_id "
                    . "from sigat.centro_acopio as cac, sigat.ruta as rut "
                    . "where cac.cac_id = rut.cac_id "
                    . "and rut.rut_id = ? "
                    . "order by cac.cac_descripcion ";
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
}
