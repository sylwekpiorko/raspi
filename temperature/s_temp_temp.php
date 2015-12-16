<?php

ini_set('error_reporting', E_ALL|E_STRICT);
ini_set('display_errors', 1);

	$username = "root";
	$password = "5YdjLTFLQcT8Meqw";
	$host = "localhost";
	$database = "measurements";
    
    $server = mysql_connect($host, $username, $password);
    $connection = mysql_select_db($database, $server);

    $myquery = "
SELECT `dtg`, `temperature` FROM `temperature` LIMIT 0 , 30
";
    $query = mysql_query($myquery);
    
    if ( ! $query ) {
        echo mysql_error();
        die;
    }
    
    $data = array();
    
    for ($x = 0; $x < mysql_num_rows($query); $x++) {
        $data[] = mysql_fetch_assoc($query);
    }
    
    echo json_encode($data);     
     
    mysql_close($server);
?>