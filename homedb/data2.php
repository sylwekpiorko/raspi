<?php

ini_set('error_reporting', E_ALL|E_STRICT);
ini_set('display_errors', 1);

	
    $username = "homedbuser";
    $password = "homedbuser";

    //$username = "homed";
    //$password = "5YdjLTFLQcT8Meqw";
	
    $host = "localhost";
	$database = "homedb";
    
    $server = mysql_connect($host, $username, $password);
    $connection = mysql_select_db($database, $server);

    $myquery = "
SELECT  `date`, `close` FROM  `data2`
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