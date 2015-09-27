<?php
/**
 * Created by PhpStorm.
 * User: Rob
 * Date: 19-9-2015
 * Time: 15:05
 */
class database
{
    private $conn;

    function __construct(){

    }

    /**
     * Establishing database connection
     * @return database connection handler
     */
    function connect(){
        include_once './config.php';

        // Connecting to database
        $this->conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB NAME);

        // Check for database connection error
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        // returning connection resource
        return $this->conn;
    }
}