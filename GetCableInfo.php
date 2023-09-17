<?php
    require_once('MySqlModel.php');

    $response = array();
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {        
        $query = "
        SELECT             
            cable_category.name_article,
            cables_material.partnumber,
            cables_material.serialnumber,
            cables_material.registerdate,
            cables_material.descriptioncable,
            cables_material.quantity,
            cables_material.borrowed_quantity
        FROM 
            `cables_material`, `cable_category`
        WHERE 
            cables_material.article_id = cable_category.id_article";
        $action = new MySqlModel();                  
        $dataIs = $action->fetchAllResults($query);
        $iter = count($dataIs);
        $arrData = array();        
            for ($i = 0; $i < $iter; $i++){        
                $articleis = $dataIs[$i]['partnumber'];
                $actions = ($dataIs[$i]['borrowed_quantity'] != 0) ? '<input 
                                                                        type="button" class="btn-amg bg-blue-amg text-white-amg" 
                                                                        id="actiondetails"
                                                                        articleis="'.$articleis.'"                                                                        
                                                                        value="Details"
                                                                    />' 
                                                                    : 'No actions' ;
                $arrData[] = array(
                                    'rows' => $i+1,                                
                                    'article' => $dataIs[$i]['name_article'],
                                    'partnumber' => $dataIs[$i]['partnumber'],
                                    'serialnumber' => $dataIs[$i]['serialnumber'],
                                    'registerdate' => $dataIs[$i]['registerdate'],
                                    'descriptioncable' => $dataIs[$i]['descriptioncable'],
                                    'quantity' => $dataIs[$i]['quantity'],
                                    'borrowed' => $dataIs[$i]['borrowed_quantity'],
                                    'actions' => $actions
                                );
            }        
        echo json_encode($arrData);
        exit();
    } else {
        $response = array("status" => false, "msg" => "There's not a GET request.");    
        echo json_encode($response);
        exit();
    }
?>