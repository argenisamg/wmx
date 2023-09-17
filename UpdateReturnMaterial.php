<?php
require_once('MySqlModel.php');

$response = array();
$valdecoded = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datasend = $_POST['datasend'];    
    $objectOne = new UpdateReturnMaterial();            
    if (!empty($datasend)) {                                                                      
        $res = $objectOne->updateTable($datasend);                                                                                                                      
        if ($res === "Success") {
            $res = $objectOne->updateTableCables($datasend);
            if ($res === "Success") {                                                                                                                                   
                $response = array("status" => true, "msg" => $res);
                $response['res'] = $res;
                echo json_encode($response);                
                exit();
            } else {
                $response = array("status" => false, "msg" => "Unable to update the register.");            
                echo json_encode($response);
                exit();
            }                                                                                                                                   
        } else {
            $response = array("status" => false, "msg" => "Unable to update the register.");            
            echo json_encode($response);
            exit();
        }        
    } else {
        $response = array("status" => false, "msg" => "Data no received !");            
        echo json_encode($response);
        exit();   
    }        
} else {
    $response = array("status" => false, "msg" => "There's not a request.");            
    echo json_encode($response);
    exit();   
}

class UpdateReturnMaterial {        
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

    public function prepareQueryUpdate($table, $datasend_, $discriminator) {        
        $totalValues = count($datasend_);
        $values = str_repeat("?,", $totalValues);
        $valuesQueryLessCharacter = substr($values, 0, -1);
        $fieldsDB = "";
            foreach ($datasend_ as $key => $value) {
                $fieldsDB .= "`$key` = ?,";
            }
            $valuesQuery = substr($fieldsDB, 0, -1);            
            #UPDATE `prestamo_material` SET `borrowed`='true' WHERE `id_prestamo` = 4;
        return "UPDATE `{$table}` SET {$valuesQuery} WHERE `{$discriminator}` = ?";
        //$this->insertMethod($datasend, $queryParam);
    } # end updateQueryPrepare

    /**
     * actualizar el campo 'quantity'de la tabla 'cables_material' una vez que se registro el prestamo
     *  "quantity_prestamo": "1"
     */   
    
    public function updateTable($datasend) {       
        try {
            # Update 'prestamo_material':
            $today = date('Y-m-d');
            $valdecoded = $this->decodeArray($datasend);            
            $action = new MySqlModel();

            $idPrestamo = $valdecoded['id_prestamo'];             
            $this->arrParam = [$today, $idPrestamo];
            
            $calculo = $valdecoded['calculo'];
            $query = ($calculo === 0) ? "UPDATE `prestamo_material` SET `fecha_devolucion` = ?,`borrowed` = 'true' WHERE `id_prestamo` = ?"
                                                :  "UPDATE `prestamo_material` SET `fecha_devolucion` = ?,`borrowed` = 'false' WHERE `id_prestamo` = ?";            
            $res = $action->updateMethod($this->arrParam, $query, "si");   
            
            # Update 'cables_material':
                        
         } catch (\Throwable $th) {
            return "Update Table: " . $th->getMessage();
         }
         
        return $res;
    } # end updateTable
    
    public function updateTableCables($datasend) {      
        $resQuery = []; 
        try {                        
            $action = new MySqlModel();            
            $valdecoded = $this->decodeArray($datasend); 
            $idCable = $valdecoded['idcable'];
            $id_prestamo = $valdecoded['id_prestamo'];
            $return_quantity = $valdecoded['return_quantity']; 
            $this->arrParam = [$idCable];         
            $query = "SELECT * FROM `cables_material` WHERE `id_cable` = ?";
            $resQuery = $action->fetchResult($this->arrParam, "i", $query);

            $quantityDB = $resQuery[0]['quantity'];// Data consulted
            $quantityBorrDB = $resQuery[0]['borrowed_quantity'];// Data consulted
            $subtract = $quantityDB + $return_quantity;
            $subtract_ = $quantityBorrDB - $return_quantity;
            

            //Array para preparar el Query Statement:
            $this->arrParam = ["quantity" => "", "borrowed_quantity" => ""];            
            $query = $this->prepareQueryUpdate("cables_material", $this->arrParam, "id_cable");                                  
           
            //Preparar el Array para la funcion Update de MySqlModel:            
            $jsonArray = [$subtract, $subtract_, $idCable]; 
            $res = $action->updateMethod($jsonArray, $query, "iii");

            /**
             * Now Update field 'quantity_return'  from table 'prestamo_material'
             */
            
            //Array para preparar el Query Statement:
            $this->arrParam = ["quantity_prestamo" => ""];            
            $query = $this->prepareQueryUpdate("prestamo_material", $this->arrParam, "id_prestamo");  
            
            //Preparar el Array para la funcion Update de MySqlModel:
            $updateQprestamo = 0;
            $borrowed_quantity = $valdecoded['borrowed_quantity'];
            $updateQprestamo = $borrowed_quantity - $return_quantity;
            $jsonArray = [$updateQprestamo, $id_prestamo]; 
            $res = $action->updateMethod($jsonArray, $query, "ii");

         } catch (\Throwable $th) {
            return "Update Table: " . $th->getMessage();
         }
         
        return $res;
    } # end updateTable
    
}

?>