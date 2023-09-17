<?php
    require_once('MySqlModel.php');

    $response = array();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {        
        $param = $_POST['param'];               
        $arrParam[] = $param;       
        $query = "
                    SELECT 
                        id_cable, serialnumber
                    FROM 
                        `cables_material` 
                    WHERE 
                        partnumber = ? 
                    AND quantity >= 1";
        $action = new MySqlModel();
        $dataIs = [];                  
        $dataIs = $action->fetchResult($arrParam, "s", $query);                
        $iter = count($dataIs);
        $arrData = array();        
            for ($i = 0; $i < $iter; $i++){            
                $arrData[] = array(                                    
                                    'id' => $dataIs[$i]['id_cable'],                                    
                                    'serialnumber' => $dataIs[$i]['serialnumber']                                    
                                );
            }        
        $response = array("status" => true, "msg" => "Success", "data" => $arrData);    
        echo json_encode($response);
        exit();
    } else {
        $response = array("status" => false, "msg" => "There's not a request.");    
        echo json_encode($response);
        exit();
    }
?>
