<?php


class procesoDepartamento {
    private $conexion;
    function __construct() {
        
        $this->conexion=new conexion();
        $this->conexion->getConexion()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    
    public function listar(){
        try {
            $sql= "select *from sigat.departamento as dep "
                    . "order by dep.dep_descripcion ";

            $ps = $this->conexion->getConexion()->prepare($sql);
            
              $ps->execute(NULL);
           
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
            $sql = "select dep.dep_id, dep.dep_descripcion, dep.reg_id "
                    . "from sigat.departamento as dep, "
                    . "sigat.municipio as mun, "
                    . "sigat.ruta as rut "
                    . "where rut.rut_id = ? "
                    . "and rut.mun_id = mun.mun_id "
                    . "and mun.dep_id = dep.dep_id "
                    . "order by dep.dep_id ";
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

    
     
}
