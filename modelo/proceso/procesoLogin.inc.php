<?php


class procesoLogin {
    private $conexion;
    function __construct() {
        
        $this->conexion=new conexion();
        $this->conexion->getConexion()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    public function validar($request){
        try {
            $obj = json_decode($request);
            
            $sql="select *from sigat.usuarios as usu, "
                    . "sigat.persona as per "
                    . "where (usu_usuario = ? "
                    . "and usu_contrasena = md5 (?)) "
                    . "and usu.per_cc = per.per_cc ";
            $ps=$this->conexion->getConexion()->prepare($sql);
            
            $ps->execute(array(
                $obj->usu_usuario,
                $obj->usu_contrasena
                ));
            $dato = array();
            if($ps->rowCount()>0){
               $lista=$ps->fetchAll(PDO::FETCH_OBJ);
                foreach ($lista as $row) {
                    $dato['usuario'] = $row->usu_usuario;
                    $dato['contrasena'] = $row->usu_contrasena;
                    $dato['tipo'] = $row->tiu_id;
                    $dato['per_cc'] = $row->per_cc;
                    $dato['estado'] = $row->est_id;
                }
                switch ($dato["tipo"]){
                    case '1':
                        $sql="select *from sigat.administrador as adm 
                            inner join sigat.persona as per on (adm.per_cc = per.per_cc)
                            where per.per_cc = ? ";
                        $ps=$this->conexion->getConexion()->prepare($sql);
                        $ps->execute(array(
                         $dato['per_cc']
                        ));
                        $lista=$ps->fetchAll(PDO::FETCH_OBJ);
                        foreach ($lista as $row) {
                          $dato['location'] = 'adminREST/AdminHome.html';
                          $dato['adm_id'] = $row->adm_id;
                          $dato['cac_id'] = $row->cac_id;
                          $dato['adm_nombre'] = $row->per_nombre." ".$row->per_apellido;  
                        }
                        break;
                    case '2':
                         $sql="select *from sigat.tecnico as tec 
                                inner join sigat.persona as per on (tec.per_cc = per.per_cc)
                                where per.per_cc = ? ";
                            $ps=$this->conexion->getConexion()->prepare($sql);
                            $ps->execute(array(
                             $dato['per_cc']
                            ));
                            $lista=$ps->fetchAll(PDO::FETCH_OBJ);
                            foreach ($lista as $row) {
                              $dato['location'] = 'tecnicoREST/TecnicoHome.html';
                              $dato['tec_id'] = $row->tec_id;
                              $dato['cac_id'] = $row->cac_id;
                              $dato['tec_nombre'] = $row->per_nombre." ".$row->per_apellido;  
                            }
                        break;
                    case '3':
                        break;
                }
               $dato['titulo']= 'Ingreso Correcto';
               $dato['mensaje'] = 'Sesión Iniciada para '.$dato['usuario'];
               $dato['est'] = 'success';
            }else{
                $dato['titulo']= 'Ingreso Fallido';
                $dato['mensaje'] = 'Usuario o contraseña incorrecto';
                $dato['est'] = 'error';
                $dato['location'] = 'index.html';
            }
            $this->conexion = null;
        } catch (PDOException $e) {
            $dato = array('mensaje' => substr($e->getMessage(),strpos($e->getMessage(),"ERROR")));
        }
        return json_encode($dato);
    }
    public function validar2($rquest){
        try {
            
            $sql = "SELECT * FROM sigat.usuarios WHERE usu_usuario = ? and usu_contrasena= MD5(?) ";
            $ps=$this->conexion->getConexion()->prepare($sql);
            $obj = json_decode($request);
            $ps->execute(array($obj->usu_usuario,$obj->usu_contrasena));
            if($ps->rowCount()>0){ 
                $lista=$ps->fetchAll(PDO::FETCH_OBJ);
                foreach ($lista as $row){
                    $dato = array('mensaje' => 'Ingreso Correcto',
                                  'usuario' => $row->usu_usuario,
                                  'location' => 'productor.html',
                                  'activo' => '1');
                }
            }else{
                $dato = array('mensaje' => 'Usuario o Contraseña Incorrecto',
                              'location' => 'index.html',
                              'activo' => '2');
            }
            $this->conexion = null;
    } catch (PDOException $e) {
        $msj=$e->getMessage();
        echo $msj;
    }
    return $dato;
   }
    public function sesion ($request){
        try {
            session_start();
            if($_SESSION["activa"]){
                $dato = array ('activa' => '1');
            } 
            
            
        } catch (PDOException $e) {
            $dato = array('mensaje' => substr($e->getMessage(),strpos($e->getMessage(),"ERROR")));
        }
        return json_encode($dato);
    }
    
    
}
