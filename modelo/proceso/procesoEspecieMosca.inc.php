<?php


class procesoEspecieMosca {
    private $conexion;
    function __construct() {
        
        $this->conexion=new conexion();
        $this->conexion->getConexion()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    
    public function listar(){
        try {
            $sql= "select *from sigat.especie_mosca as esm "
                    . "order by esm.esm_id ";

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
    
}
