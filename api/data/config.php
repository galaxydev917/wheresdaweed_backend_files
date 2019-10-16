<?php

$deploy=FALSE;

$dbhost = 'localhost';
$dbport = '61000';
$dbuser = 'root';
$dbpass = 'kartoos';
$dbname = 'wheredaweed';

if ($deploy){
    $dbhost='localhost';
    $dbuser='rameezus_rameez';
    $dbpass='master123';
    $dbname='rameezus_wheredaweed';
    $dbport='3306';

    $dbhost='localhost';
    $dbuser='jadedssc_wdw';
    $dbpass='wheredaweed@123';
    $dbname='jadedssc_wheredaweed';
    $dbport='3306';
}

require('rb.php');

function openDbConnection(){
    global $dbhost,$dbuser,$dbpass,$dbname,$dbport;
    R::setup('mysql:host='.$dbhost.';port='.$dbport.';dbname='.$dbname,$dbuser,$dbpass);
    R::freeze(true);
}

function closeDbConnection($con=null){
    R::close();
}

?>