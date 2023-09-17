<?php
require_once('MySqlModel.php');

$response = array();
$valdecoded = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datasend = $_POST['datasend'];
    $objectOne = new InsertDataInOut();            
    if (!empty($datasend)) {              
        //Get the json decoded:                          
        $valdecoded = $objectOne->decodeArray($datasend);               
        $res = $objectOne->triggerMethod(count($valdecoded), $valdecoded);                                                                                                                      
        if ($res === "Success") {
            $response = array("status" => true, "msg" => $res);
            $response['res'] = $res;
            echo json_encode($response);                
            exit();
        } else {
            $response = array("status" => false, "msg" => "Data no received !");            
            echo json_encode($response);
            exit();
        }        
    } else {
        $response = array("status" => false, "msg" => "Data no received !");            
        echo json_encode($response);
        exit();   
    }        
} 

class InsertDataInOut {        
    private $arrSend = array();
    private $arrParam = array();
    /**
     * Class Constructor
     */
    public function __construct() {}      
    /**
     * decodeArray functions to decode the json obtained from client request:
     */
    public function decodeArray($datasend) {
        $return = "";
        $arrForeach = json_decode($datasend, true);
        return $arrForeach;

    } # end decodeArray

    /**
     * triggerMethod functions to start the process,
     */
    public function triggerMethod($jsonLength, $valdecoded) {   
        $action = new MySqlModel();
        $paramval = $valdecoded['id_persona'];               
        $resAsked = $action->askForExist("persona_material", "person_pic", $paramval);     

        //First, I have to compare if the $jsonLength param is 6 or 7:
        if ($resAsked) {    
            try {
                /**
                 * Process when user exist:
                 */
                $this->arrParam = [$paramval];           
                $idIs = $action->fetchResult($this->arrParam, "s", "SELECT `id_persona` FROM `persona_material` WHERE `person_pic` = ?");
                if ($jsonLength == 7) {
                    $valdecoded['id_cable'] = $valdecoded['serialnumber2'];                    
                }                                                            
                $valdecoded['id_persona'] = $idIs[0]['id_persona'];                            
                $valdecoded['fecha_devolucion'] = "0000-00-00";                            
                unset($valdecoded['serialnumber2']);//Delete serialnumber2 element from the array
                    foreach ($valdecoded as $key => $value) {
                        $this->arrSend[] = $value;                
                    }                                  
                $query = $this->prepareQueryInsert("prestamo_material", $valdecoded);               
                $res = $action->insertMethod($this->arrSend, $query, "iissis");
                $response = ($res !== "Success") ? array("status" => false, "msg" => $res) 
                                                : array("status" => true, "msg" => "Register recordered succesfully.");                     
            } catch (\Throwable $th) {
                return "Insert Error True: " . $th->getMessage();                    
            } # end catch 
        } else {         
                /**
                 * Process when user does not exist:
                 */          
            try {
                $this->arrParam = array("person_pic" => $valdecoded['id_persona']);            
                $query = $this->prepareQueryInsert("persona_material", $this->arrParam);            
                $this->arrParam = [$valdecoded['id_persona']];            
                $resInsertPerson = $action->insertMethod($this->arrParam, $query, "s");                                     
                
                $idResult = $action->lastIdInDB("persona_material", "id_persona");           
                $valdecoded['id_cable'] = $valdecoded['serialnumber2'];            
                $valdecoded['id_persona'] = $idResult;                            
                $valdecoded['fecha_devolucion'] = "0000-00-00";                            
                unset($valdecoded['serialnumber2']);
                foreach ($valdecoded as $key => $value) {
                    $this->arrSend[] = $value;                
                }     
                
                $query = $this->prepareQueryInsert("prestamo_material", $valdecoded);            
                $res = $action->insertMethod($this->arrSend, $query, "iissis");            
                $response = ($res !== "Success") ? array("status" => false, "msg" => $res) 
                                                : array("status" => true, "msg" => "Register recordered succesfully."); 
                
            } catch (\Throwable $th) {
                return "Insert Error False: " . $th->getMessage();
            } # end catch                                
        }

        //update the number of cables based on the ones that were borrowed: 
        $res = $this->updateTable($valdecoded, $action);
        return $res;

    } # insertOrCreateUser

    public function prepareQueryInsert($table, $datasend) {        
        $totalValues = count($datasend);
        $values = str_repeat("?,", $totalValues);
        $valuesQueryLessCharacter = substr($values, 0, -1);
        $fieldsDB = "";
            foreach ($datasend as $key => $value) {
                $fieldsDB .= "`$key`,";
            }
            $valuesQuery = substr($fieldsDB, 0, -1);
        return "INSERT INTO `{$table}` ($valuesQuery) VALUES ($valuesQueryLessCharacter)";        
    } # end prepareQuery    

    public function prepareQueryUpdate($table, $datasend, $discriminator) {        
        $totalValues = count($datasend);
        $values = str_repeat("?,", $totalValues);
        $valuesQueryLessCharacter = substr($values, 0, -1);
        $fieldsDB = "";
            foreach ($datasend as $key => $value) {
                $fieldsDB .= "`$key` = ?,";
            }
            $valuesQuery = substr($fieldsDB, 0, -1);
            # "UPDATE `cables_material` SET `quantity`= ?,`borrowed_quantity`= ? WHERE `id_cable` = ?"
        return "UPDATE `{$table}` SET {$valuesQuery} WHERE `{$discriminator}` = ?";
        //$this->insertMethod($datasend, $queryParam);
    } # end updateQueryPrepare

    /**
     * actualizar el campo 'quantity'de la tabla 'cables_material' una vez que se registro el prestamo
     *  "quantity_prestamo": "1"
     */   
    
    public function updateTable($valdecoded, $action) {       
        $jsonArray = [];
        try {
            //Desglosar la informacion del Json POST:
            $quantity_prestamo = $valdecoded['quantity_prestamo']; 
            $idCable = $valdecoded['id_cable'];
            $this->arrParam = [$idCable];         
            $query = "SELECT * FROM `cables_material` WHERE `id_cable` = ?";
            $resQuery = $action->fetchResult($this->arrParam, "i", $query);
            $quantityDB = $resQuery[0]['quantity'];
            $quantityBorrDB = $resQuery[0]['borrowed_quantity'];
            //Array para preparar el Query Statement:
            $this->arrParam = ["quantity" => "", "borrowed_quantity" => ""]; 
            if ($quantityBorrDB === 0) {                
                $subtract = $quantityDB - $quantity_prestamo; 
                //Preparar el Array para la funcion Update de MySqlModel:
                $jsonArray = [$subtract, $quantity_prestamo, $idCable];                
            } else {
                $subtract = $quantityDB - $quantity_prestamo;
                $added = $quantityBorrDB + $quantity_prestamo;
                //Preparar el Array para la funcion Update de MySqlModel:
                $jsonArray = [$subtract, $added, $idCable];    
            }
                       
            $query = $this->prepareQueryUpdate("cables_material", $this->arrParam, "id_cable");                                             
            $res = $action->updateMethod($jsonArray, $query, "iii");
         } catch (\Throwable $th) {
            return "Update Table: " . $th->getMessage();
         }
         
        return $res;
    } # end updateTable
    
}

?>