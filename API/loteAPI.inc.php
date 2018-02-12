<?php


class loteAPI {
    function __construct() {
        
    }
    public function API(){
        header('Content-Type: application/JSON');  
        header('Access-Control-Allow-Origin: *');  
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
         header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
        $method = $_SERVER['REQUEST_METHOD'];
        include_once './modelo/conexion/conexion.inc.php';
        include_once './modelo/proceso/procesoLote.inc.php';
        $p = new procesoLote();
        $v = array();
        switch ($method) {
             case 'GET':
                if(isset($_GET['accion'])){
                    switch($_GET['accion']){
                        case 'listar':
                             $r = $p->listar($_GET);
                             break;
                        case 'buscar':
                        $r = $p->buscar($_GET);
                        break;
                        case 'imp':
                        $r = $p->imp($_GET);
                        break;
                    }
                }
            echo json_encode($r);
            break;     
            case 'POST':
                $r = $p->insertar(file_get_contents('php://input')); 
                echo json_encode($r);
                break;                
            case 'PUT':
                $r = $p->actualizar(file_get_contents('php://input')); 
                echo json_encode($r);                 
                break; 
            case 'DELETE': 
                $r = $p->eliminar(file_get_contents('php://input')); 
                echo json_encode($r);
                break;
            default://metodo NO soportado
                //$r[] = $array = ["Metodo" => "METODO NO SOPORTADO"];echo json_encode($r); 
                break;
        }
                
    }
}
