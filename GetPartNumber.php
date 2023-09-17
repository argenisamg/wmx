<?php
    require_once('MySqlModel.php');

    $response = array();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {        
        $param = $_POST['param'];
        $arrDecoded = array();
        $arrDecoded[] = json_decode($param, true);       
        $action = new MySqlModel();                  
        
        $query = "
                    SELECT `serialnumber` 
                    FROM `cables_material` 
                    WHERE `article_id` = ? ";
        $dataIs = $action->fetchResult($arrDecoded, "i", $query);    
        $isSerial = $dataIs[0]['serialnumber'];  
        $query = ($isSerial !== "") ? "SELECT DISTINCT `article_id`, `partnumber` 
                                        FROM `cables_material` 
                                        WHERE `article_id` = ?
                                        AND quantity >= 1"
                                        : 
                                        "SELECT `id_cable`, `partnumber` 
                                        FROM `cables_material` 
                                        WHERE `article_id` = ?
                                        AND quantity >= 1";

            $dataIs = $action->fetchResult($arrDecoded, "i", $query);
            $iter = count($dataIs);
            $arrData = array();        
                for ($i = 0; $i < $iter; $i++){  
                    $arrData[] = ($isSerial !== "") ? array(                                    
                                                            'id' => $dataIs[$i]['article_id'],
                                                            'partnumber' => $dataIs[$i]['partnumber'],
                                                            'sn' => 'yes'                                                      
                                                            )
                                                            : array(                                    
                                                                'id' => $dataIs[$i]['id_cable'],
                                                                'partnumber' => $dataIs[$i]['partnumber'],
                                                                'sn' => 'not'                                                      
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