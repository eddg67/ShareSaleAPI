<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DBBase
 *
 * @author e
 */


interface IDB {
    
    public static function getInstance();
    public function insertItems($items,$db,$table);
    public function getDB($name);  
    public function setDB($name);
    
}
