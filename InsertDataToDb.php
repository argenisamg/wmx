<?php
require_once('MySqlModel.php');

$response = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datasend = $_POST['datasend'];        
    if (!empty($datasend)) {                
        $action = new InsertDataToDb($datasend);                  
        $queryPrepared = $action->insertQueryPrepare($datasend);        
        $arrParam = $action->decodeArray($datasend);
        $insert = new MySqlModel();
        $resInsertIt = $insert->insertMethod($arrParam, $queryPrepared, "sssssii");
        $response = array("status" => true, "msg" => $resInsertIt);
        echo json_encode($response);
        exit();
    } else {
        $response = array("status" => false, "msg" => "JSON is empty !");
        echo json_encode($response);
        exit();
    }        
} 

class InsertDataToDb {
    #GLOBALS    
    private $arrExist = array();

    public function __construct() {}  
    
    public function decodeArray($datasend) {
        $arrReturn = array();
        $arrForeach = json_decode($datasend, true);
        foreach ($arrForeach as $key => $value) {
            $arrReturn[] = $value;
        }

        return $arrReturn;
    } # end decodeArray

    public function insertQueryPrepare($datasend) {        
        $jsonDecoded = json_decode($datasend, true);
        $totalValues = count($jsonDecoded);
        $values = str_repeat("?,", $totalValues);
        $valuesQueryLessCharacter = substr($values, 0, -1);
        $fieldsDB = "";
            foreach ($jsonDecoded as $key => $value) {
                $fieldsDB .= "`$key`,";
            }
            $valuesQuery = substr($fieldsDB, 0, -1);
        return "INSERT INTO `cables_material` ($valuesQuery) VALUES ($valuesQueryLessCharacter)";        
    } # end prepareQuery    

    public function updateQueryPrepare($datasend, $discriminator) {        
        $jsonDecoded = json_decode($datasend, true);
        $totalValues = count($jsonDecoded);
        $values = str_repeat("?,", $totalValues);
        $valuesQueryLessCharacter = substr($values, 0, -1);
        $fieldsDB = "";
            foreach ($jsonDecoded as $key => $value) {
                $fieldsDB .= "`$key`,";
            }
            $valuesQuery = substr($fieldsDB, 0, -1);
        return "UPDATE `{$table}` SET {$valuesQuery} WHERE {$discriminator}";
        //$this->insertMethod($datasend, $queryParam);
    } # end updateQueryPrepare                            
    
}

?>