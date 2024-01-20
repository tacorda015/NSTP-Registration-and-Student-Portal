<?php
if(!function_exists('connection')){
    function connection(){
        $host = "localhost";
        $username = "id20996423_nstpportal";
        $password = "Password@015";
        $database = "id20996423_nstpportal";
        $con = new mysqli($host, $username, $password, $database);
        if($con->connect_error){
            echo $con->connect_error;
        }
        else{
            return $con;
        }
    }
}
?>