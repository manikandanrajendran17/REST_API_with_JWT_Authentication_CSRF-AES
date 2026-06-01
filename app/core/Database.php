<?php

class Database
{
    private $conn;

    public function connect()
    {
        $host = "localhost";
        $user = "root";
        $pass = "";
        $dbname = "hospital1_db";
        $port = 3307;

        mysqli_report( MYSQLI_REPORT_OFF);

        $this->conn =new mysqli($host,$user, $pass,$dbname, $port);

        if( $this->conn->connect_error)
        {
            die("DB ERROR : ".$this->conn->connect_error );
        }

        return $this->conn;
    }
}