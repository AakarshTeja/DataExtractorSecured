<?php

require_once 'constants.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);


if($conn->connect_error){
div('Database error:' .$conn->connect_error);

}

?>


