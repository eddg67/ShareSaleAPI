<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$path = str_replace("Jobs", "", __DIR__);
include ($path."/IDB.php");
include ($path."/MongoDBClient.php");
include ($path."/API.php");

$api = new API();
$api->clearAllProducts();
$api->loadProducts();
