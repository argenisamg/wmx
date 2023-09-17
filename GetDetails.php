<?php
    require_once('MySqlModel.php');

    $response = array();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {        
        $param = $_POST['param'];
        $arrDecoded = array();
        $arrDecoded[] = $param;       
        $action = new MySqlModel();                           
        $query = "
                    SELECT                     
                    cables_material.partnumber,
                    cable_category.name_article,
                    persona_material.person_pic,
                    prestamo_material.quantity_prestamo
                    FROM 
                        `cables_material`, 
                        `prestamo_material`,
                        `persona_material`,
                        `cable_category`
                    WHERE 
                    cables_material.id_cable = prestamo_material.id_cable
                    AND cables_material.article_id = cable_category.id_article
                    AND prestamo_material.id_persona = persona_material.id_persona
                    AND prestamo_material.borrowed = 'false'
                    AND cables_material.partnumber = ?
                ";
        $dataIs = $action->fetchResult($arrDecoded, "s", $query);        
        $iter = count($dataIs);
        $arrData = array();        
            for ($i = 0; $i < $iter; $i++){  
                $arrData[] = array(      
                                    'rows' => $i+1,                                                                                      
                                    'partnumberDetail' => $dataIs[$i]['partnumber'],
                                    'categoryDetail' => $dataIs[$i]['name_article'],
                                    'personDetail' => $dataIs[$i]['person_pic'],
                                    'quantityDetail' => $dataIs[$i]['quantity_prestamo']                                                                                                            
                                    );                                                        
            } # end for       
        $response = array("status" => true, "msg" => "Success", "data" => $arrData);    
        echo json_encode($response);
        exit();
    } else {
        $response = array("status" => false, "msg" => "There's not a POST request.");    
        echo json_encode($response);
        exit();
    }
?>