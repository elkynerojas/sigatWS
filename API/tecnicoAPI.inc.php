<?php


class tecnicoAPI {
    function __construct() {
        
    }
    public function API(){
        header('Content-Type: application/JSON');  
        header('Access-Control-Allow-Origin: *');  
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
         header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
        $method = $_SERVER['REQUEST_METHOD'];
        include_once './modelo/conexion/conexion.inc.php';
        include_once './modelo/proceso/procesoTecnico.inc.php';
        $p = new procesoTecnico();
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
                 switch($_GET['accion']){
                        case 'insert':
                            $r = $p->insertar(file_get_contents('php://input')); 
                            echo json_encode($r);
                            break;
                        case 'export':
                             $r = $p->exp(file_get_contents('php://input'));
                              echo json_encode($r);
                             break;
                        case 'clave':
                             $r = $p->clave(file_get_contents('php://input'));
                              echo json_encode($r);
                             break;
                    }
                break;                
            case 'PUT':
                switch($_GET['accion']){
                        case 'update':
                            $r = $p->actualizar(file_get_contents('php://input')); 
                            echo json_encode($r);
                            break;
                        case 'credencial':
                             $r = $p->credenciales(file_get_contents('php://input'));
                              echo json_encode($r);
                             break;
                        
                    }
                
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
