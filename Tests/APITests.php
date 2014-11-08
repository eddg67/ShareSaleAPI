<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function my_autoloader($class)
{
    $filename =  '../' . str_replace('\\', '/', $class) . '.php';
    include($filename);
}
spl_autoload_register('my_autoloader');
/**
 * Description of APITests
 *
 * @author e
 */
class APITests extends PHPUnit_Framework_TestCase{
    //put your code here
     public function testGetProducts(){
       //  $api = new API();
       //  $results = $api->getProducts();
         
         //print_r($results);
         
 
    }
    
    public function testloadProducts(){
         $api = new API();
         $api->loadProducts();
   
    }
}
