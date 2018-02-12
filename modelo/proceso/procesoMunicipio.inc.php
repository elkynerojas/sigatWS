<?php


class procesoMunicipio {
    private $conexion;
    function __construct() {
        
        $this->conexion=new conexion();
        $this->conexion->getConexion()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    
    public function listar($request){
        try {
            $sql= "select *from sigat.municipio as mun "
                    . "where mun.dep_id = ? "
                    . "order by mun.mun_descripcion ";

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
    
     
    public function imp($request){
        try {
            $sql = "select mun.mun_id, mun.mun_descripcion, mun.dep_id "
                    . "from sigat.municipio as mun, "
                    . "sigat.ruta as rut "
                    . "where rut.rut_id = ? "
                    . "and rut.mun_id = mun.mun_id "
                    . "order by mun.mun_id ";
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
