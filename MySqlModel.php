<?php
require_once('ConnectionDB.php');

class MySqlModel {
    #GLOBALS    
    private $arrExist = array();
    public function __construct() {}

    public function askForExist($tableName, $fieldParam, $dataParam, ) {
        $responseExist = false;           
        $count = 0;
        try {
            // $connection = new ConnectionDB('10.250.36.58', 'root', 'p@ssw0rd', 'monica');            
            $connection = new ConnectionDB('127.0.0.1', 'root', '', 'storage');            
            $conn = $connection->conectarMySQL();            
            $queryExist = "SELECT * FROM `{$tableName}` WHERE `{$fieldParam}` = '{$dataParam}'";
            $stmt = mysqli_prepare($conn, $queryExist);  
            mysqli_stmt_execute($stmt);
            $executeQuery = mysqli_stmt_get_result($stmt);
            $responseExist = (mysqli_num_rows($executeQuery) > 0) ? true : false;                                      
            $connection->cerrarConexionMySQL();
        } catch (\Throwable $th) {
            if ($conn) {
                $connection->cerrarConexionMySQL();
            }
            echo "Error: ".$th->getMessage();
            exit();
        }
       
        return $responseExist;
    } #end function askForExist        
    
    public function insertMethod($jsonArray, $query, $stmntString) {             
        $connection = null;
        $conn = null;          
        try {                     
            $connection = new ConnectionDB('127.0.0.1', 'root', '', 'storage');            
            $conn = $connection->conectarMySQL();                                       
            $stmt = mysqli_prepare($conn, $query);        
            mysqli_stmt_bind_param($stmt, $stmntString, ...$jsonArray);
            mysqli_stmt_execute($stmt);
            $connection->cerrarConexionMySQL();                                                
        } catch (\Throwable $th) {
            if ($conn) {
                $connection->cerrarConexionMySQL();
            }
            return "Insert Error: ".$th->getMessage();                        
        }                
        
        return "Success";
    } # end insertMethod                              
    
    public function updateMethod($jsonArray, $query, $stmntString) {             
        $connection = null;
        $conn = null;          
        try {                     
            $connection = new ConnectionDB('127.0.0.1', 'root', '', 'storage');            
            $conn = $connection->conectarMySQL();                                       
            $stmt = mysqli_prepare($conn, $query);        
            mysqli_stmt_bind_param($stmt, $stmntString, ...$jsonArray);
            mysqli_stmt_execute($stmt);
            $connection->cerrarConexionMySQL();                                                
        } catch (\Throwable $th) {
            if ($conn) {
                $connection->cerrarConexionMySQL();
            }
            echo "Update Error: ".$th->getMessage();
            exit();
        }           
        return "Success";
    } # end updateMethod                              
    
    public function fetchAllResults($query) {             
        $connection = null;
        $conn = null;          
        $results = array();
        try {                     
            $connection = new ConnectionDB('127.0.0.1', 'root', '', 'storage');            
            $conn = $connection->conectarMySQL();                                       
            $stmt = mysqli_prepare($conn, $query);            
            mysqli_stmt_execute($stmt);
            $executeQuery = mysqli_stmt_get_result($stmt);                                
            $results= [];
            if (mysqli_num_rows($executeQuery) > 0) {    
                while ($row = mysqli_fetch_assoc($executeQuery)) $results[] = $row;
            } 
            $connection->cerrarConexionMySQL();                                                
        } catch (\Throwable $th) {
            if ($conn) {
                $connection->cerrarConexionMySQL();
            }
            echo "Select All Error: ".$th->getMessage();
            exit();
        }       
        
        return $results;
    } # end selectAllMethod                              
    
    public function fetchResult($arrParam, $stmntString, $query) {             
        $connection = null;
        $conn = null;          
        try {                     
            $connection = new ConnectionDB('127.0.0.1', 'root', '', 'storage');            
            $conn = $connection->conectarMySQL();                                       
            $stmt = mysqli_prepare($conn, $query);        
            mysqli_stmt_bind_param($stmt, $stmntString, ...$arrParam);
            mysqli_stmt_execute($stmt);
            $executeQuery = mysqli_stmt_get_result($stmt);
            $results= [];
            if (mysqli_num_rows($executeQuery) > 0) {    
                while ($row = mysqli_fetch_assoc($executeQuery)) $results[] = $row;
            } 
            $connection->cerrarConexionMySQL();                                                  
        } catch (\Throwable $th) {
            if ($conn) {
                $connection->cerrarConexionMySQL();
            }
            echo "Select One Error: ".$th->getMessage();
            exit();
        }               
            
        return $results;
    } # end selectOneMethod   

    public function lastIdInDB($tabla, $idCol) {     
        $connection = new ConnectionDB('127.0.0.1', 'root', '', 'storage');            
        $conn = $connection->conectarMySQL(); 
                
        $sql = "SELECT MAX($idCol) AS last_id FROM `{$tabla}`";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $ultimoID = $row["last_id"];            
        }                

        return $ultimoID;
    } # end lastIdInDB
        
}

?>