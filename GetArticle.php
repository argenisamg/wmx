<?php
    require_once('MySqlModel.php');

    $response = array();
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {        
        $query = "SELECT * FROM `cable_category`";
        $action = new MySqlModel();                  
        $dataIs = $action->fetchAllResults($query);
        $iter = count($dataIs);
        $arrData = array();        
            for ($i = 0; $i < $iter; $i++){            
                $arrData[] = array(
                                    'id' => $dataIs[$i]['id_article'],
                                    'article' => $dataIs[$i]['name_article']                                   
                                );
            }        
        $response = array("status" => true, "msg" => "Success", "data" =>$arrData);    
        echo json_encode($response);
        exit();
    } else {
        $response = array("status" => false, "msg" => "There's not a GET request.");    
        echo json_encode($response);
        exit();
    }
?>