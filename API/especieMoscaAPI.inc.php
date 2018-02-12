<?php


class especieMoscaAPI {
    function __construct() {
        
    }
    public function API(){
        header('Content-Type: application/JSON');  
        header('Access-Control-Allow-Origin: *');  
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
         header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
        $method = $_SERVER['REQUEST_METHOD'];
        include_once './modelo/conexion/conexion.inc.php';
        include_once './modelo/proceso/procesoEspecieMosca.inc.php';
        $p = new procesoEspecieMosca();
        $v = array();
        switch ($method) {
             case 'GET':
                if(isset($_GET['accion'])){
                    switch($_GET['accion']){
                        case 'listar':
                             $r = $p->listar($_GET);
                             break;
                        case 'imp':
                             $r = $p->listar($_GET);
                             break;       
                }
            }
            echo json_encode($r);
            break;     
        case 'POST':
            
            break;                
        case 'PUT':
                             
            break; 
        case 'DELETE': 
            
            break;
        default://metodo NO soportado
            //$r[] = $array = ["Metodo" => "METODO NO SOPORTADO"];echo json_encode($r); 
            break;
        }
                
    }
}
