<?php
    if(isset($_GET["recurso"])){
        switch ($_GET["recurso"]){
            case "persona":
                require_once "./API/personaAPI.inc.php"; 
                $personaAPI = new personaAPI();
                $personaAPI->API();
                break;
            case "productor":
                require_once "./API/productorAPI.inc.php"; 
                $productorAPI = new productorAPI();
                $productorAPI->API();
                break;
            case "predio":
                require_once "./API/predioAPI.inc.php"; 
                $predioAPI = new predioAPI();
                $predioAPI->API();
                break;
            case "lote":
                require_once "./API/loteAPI.inc.php"; 
                $loteAPI = new loteAPI();
                $loteAPI->API();
                break;
            case "trampa":
                require_once "./API/trampaAPI.inc.php"; 
                $tramaAPI = new trampaAPI();
                $tramaAPI->API();
                break;
            case "tecnico":
                require_once "./API/tecnicoAPI.inc.php"; 
                $tecnicoAPI = new tecnicoAPI();
                $tecnicoAPI->API();
                break;
            case "administrador":
                require_once "./API/administradorAPI.inc.php"; 
                $administradorAPI = new administradorAPI();
                $administradorAPI->API();
                break;
            case "login":
                require_once "./API/loginAPI.inc.php"; 
                $loginAPI = new loginAPI();
                $loginAPI->API();
                break;
             case "cac":
                require_once "./API/centroAcopioAPI.inc.php"; 
                $centroAcopioAPI = new centroAcopioAPI();
                $centroAcopioAPI->API();
                break;
            case "departamento":
                require_once "./API/departamentoAPI.inc.php"; 
                $departamentoAPI = new departamentoAPI();
                $departamentoAPI->API();
                break;
            case "municipio":
                require_once "./API/municipioAPI.inc.php"; 
                $municipioAPI = new municipioAPI();
                $municipioAPI->API();
                break;
            case "arbol":
                require_once "./API/arbolAPI.inc.php"; 
                $arbolAPI = new arbolAPI();
                $arbolAPI->API();
                break;
            case "ubi":
                require_once "./API/ubiAPI.inc.php"; 
                $ubiAPI = new ubiAPI();
                $ubiAPI->API();
                break;
            case "cap":
                require_once "./API/capAPI.inc.php"; 
                $capAPI = new capAPI();
                $capAPI->API();
                break;
             case "inspeccion":
                require_once "./API/inspeccionAPI.inc.php"; 
                $inspeccionAPI = new inspeccionAPI();
                $inspeccionAPI->API();
                break;
            case "alertas":
                require_once "./API/alertasAPI.inc.php"; 
                $alertasAPI = new alertasAPI();
                $alertasAPI->API();
                break;
            case "ruta":
                require_once "./API/rutaAPI.inc.php"; 
                $rutaAPI = new rutaAPI();
                $rutaAPI->API();
                break;
            case "esm":
                require_once "./API/especieMoscaAPI.inc.php"; 
                $esmAPI = new especieMoscaAPI();
                $esmAPI->API();
                break;
        }
    }
 ?>
    
