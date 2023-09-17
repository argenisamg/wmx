<?php
    class ConnectionDB {
        private $oracleConnection;
        private $conexionMySQL;

        private $host = '';
        private $port = '';
        private $dbname = '';
        private $user = '';
        private $password = ''; 
        private $algo_ = '';
        public $params = array();
         
        public function __construct(...$params) {
            $this->params = $params;           
        }               
        
        public function conectarOracle() {                             
            $this->host = $this->params[0];    // '10.250.200.41'   
            $this->port = $this->params[1];    // '1527'
            $this->dbname = $this->params[2];    // 'WMXBO912'
            $this->user = $this->params[3];    // 'SFCFA912'
            $this->password = $this->params[4]; // 'SFCFA912'            
            #Open the connection channel to Oracle:	            
            $connectionString = "(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=$this->host)(PORT=$this->port)))(CONNECT_DATA=(SID=$this->dbname)))";
            $this->oracleConnection = oci_connect($this->user, $this->password, $connectionString);
            
            if (!$this->oracleConnection) {
                $error = oci_error();
                echo "Error connecting to Oracle: " . $error['message'];
                return false;
            }
            
            return $this->oracleConnection;
        } #end function conectarOracle
        
        public function conectarMySQL() {           
           
            $this->host = $this->params[0];    // '10.250.200.41'               
            $this->user = $this->params[1];    // 'SFCFA912'
            $this->password = $this->params[2]; // 'SFCFA912'
            $this->dbname = $this->params[3];    // 'WMXBO912'

            $this->conexionMySQL = mysqli_connect($this->host, $this->user, $this->password, $this->dbname);
            
            if (!$this->conexionMySQL) {
                echo "Error connecting to MySQL: " . mysqli_connect_error();
                return false;
            }
            
            return $this->conexionMySQL;
        } #end function conectarMySQL
        
        public function cerrarConexionOracle() {
            oci_close($this->oracleConnection);
        } #end function cerrarConexionOracle
        
        public function cerrarConexionMySQL() {
            mysqli_close($this->conexionMySQL);
        } #end function cerrarConexionMySQL
    }
?>