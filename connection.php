<?php
if(!function_exists('connection')){
    function connection(){
        $host = "localhost";
        $username = "root";
        $password = "";
        $database = "upload_db";
        // $database = "nstpportal";

        // $host = "localhost";
        // $username = "u222940158_nstp";
        // $password = "CCATnstp-123";
        // $database = "u222940158_nstp";

        // $host = "sql200.infinityfree.com";
        // $username = "if0_35508108";
        // $password = "QyRx5kFTJUxDP1";
        // $database = "if0_35508108_nstp_portal";

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