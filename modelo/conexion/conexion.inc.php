<?php
class conexion {
    private $conexion;
    private $driver;
    private $db;
    private $host;
    private $port;
    private $user;
    private $password;
    
    public function __construct() {
        $this->driver = "pgsql";
        $this->db = "sigat";
        $this->host = "localhost";
        $this->port = "5432";
        $this->user = "postgres";
        $this->password = "p05tgr3s";
        $this->setConexion();
    }
    private function setConexion(){
        $this->conexion = new PDO("$this->driver:dbname=$this->db;host=$this->host;port=$this->port",  $this->user,  $this->password);
    }
    public function getConexion(){
        return $this->conexion;
    }
}
