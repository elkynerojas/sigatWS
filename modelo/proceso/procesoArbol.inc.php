<?php


class procesoArbol {
    private $conexion;
    function __construct() {
        
        $this->conexion=new conexion();
        $this->conexion->getConexion()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    
    public function listar(){
        try {
            $sql= "select *from sigat.arbol as arb "
                    . "order by arb.arb_id ";

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

            $this->conexion = null;
        } catch (PDOException $e) {
            $dato = array('mensaje' => substr($e->getMessage(),strpos($e->getMessage(),"ERROR")));
        }
        return json_encode($dato);
    }
   
}
