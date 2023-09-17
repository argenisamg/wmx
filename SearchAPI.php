<?php
require_once('MySqlModel.php');

$response = array();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {    
    $param = $_GET['param'];               
    $arrParam[] = $param;    
    $query = "
            SELECT  
                prestamo_material.id_prestamo as 'id',
                persona_material.person_pic as 'pic', 
                cable_category.name_article as 'article', 
                cables_material.id_cable as 'idcable', 
                cables_material.partnumber as 'partnumber', 
                cables_material.serialnumber as 'serialnumber', 
                prestamo_material.fecha_prestamo as 'fechaprestamo', 
                prestamo_material.fecha_devolucion as 'fechadevolucion', 
                prestamo_material.quantity_prestamo as 'cantidadprestada'
            FROM 
                `persona_material`, 
                `cables_material`, 
                `cable_category`, 
                `prestamo_material` 
            WHERE persona_material.id_persona = prestamo_material.id_persona 
            AND cable_category.id_article = cables_material.article_id 
            AND cables_material.id_cable = prestamo_material.id_cable 
            AND persona_material.person_pic = ?
            AND prestamo_material.borrowed = 'false'
            ";

    try {
        $action = new MySqlModel();    
        $dataIs = [];              
        $dataIs = $action->fetchResult($arrParam, "s", $query);        
        $iter = count($dataIs);

        $varSN = "";
        $withOutSnQantity = "";
        $arrData = array();        
            for ($i = 0; $i < $iter; $i++){ 
                $id = $dataIs[$i]['id'];           
                $idcable = $dataIs[$i]['idcable'];
                $quantityoutput = $dataIs[$i]['cantidadprestada'];
                $varSN = $dataIs[$i]['serialnumber'];
                $withOutSnQantity = ($varSN === "") ? '<input type="number" class="inputs bg-input-amg text-white-amg" 
                                                        id="numberQantityBorrowed" 
                                                        name="numberQantityBorrowed"                                                         
                                                        value="'.$quantityoutput.'" required>'
                                                    : 
                                                        '<input type="number" class="inputs bg-input-amg text-white-amg" 
                                                        id="numberQantityBorrowed" 
                                                        name="numberQantityBorrowed"                                                         
                                                        value="'.$quantityoutput.'" required disabled>';                    
                $arrData[] = array(
                                    'rows' => $i+1,                                    
                                    'pic' => $dataIs[$i]['pic'],
                                    'article' => $dataIs[$i]['article'],
                                    'pn' => $dataIs[$i]['partnumber'],
                                    'sn' => $varSN,
                                    'dateout' => $dataIs[$i]['fechaprestamo'],
                                    'datein' => $dataIs[$i]['fechadevolucion'],
                                    'quantityoutput' => $withOutSnQantity,
                                    'actions' => '<input 
                                                    type="button" class="btn-amg bg-blue-amg text-white-amg" 
                                                    id="returnbtn" 
                                                    idcable="'.$idcable.'" 
                                                    idborrowed="'.$id.'"  
                                                    quantityData="'.$quantityoutput.'"                                                    
                                                    value="Return Item"
                                                />'
                                );
            }                                
            echo json_encode($arrData);
            exit();
    } catch (\Throwable $th) {    
        $response = array("status" => false, "msg" => $th->getMessage());    
        echo json_encode($response);
        exit();
    }
    
} else {
        $response = array("status" => false, "msg" => "There's not a request.");    
        echo json_encode($response);
        exit();
}

?>
