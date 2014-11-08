<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function my_autoloader($class)
{
    $filename =  str_replace('\\', '/', $class) . '.php';
    include($filename);
}
spl_autoload_register('my_autoloader');
/**
 * Description of API
 *
 * @author e
 */
class API {

    public $configOptions, $db;

    //put your code here
    public function __construct() {
        $this->configOptions = parse_ini_file("config.ini", true);
        if (empty($this->configOptions)) {
            throw new Exception('Config File Did not load properly.');
        }
        //run command line
        if(!empty($argv[1])){
            switch($argv[1]){
               case "load":
                   echo "loading process";
                   $this->loadProducts();
                 break;
            }
        }
    }
    
    public function loadProducts()
    {
        foreach ($this->configOptions['LoadKeys'] as $keyword => $ending){
            $items = $this->getProducts($keyword,$ending);
            $this->insertProducts($items);
        }
        
    }

    public function getProducts($keyword,$ending="") {
        return $this->createRequest("getProducts", "keyword={$keyword}&{$ending}");
    }

    public function insertProducts($items) {
        if (!$this->db) {
            $this->db = new $this->configOptions['Settings']['DBClient']();
        }
        $this->db->insertItems($items, "ss_products","products");
    }
    
    public function getDBClient(){
        
        switch($this->configOptions['Settings']['DBClient'])
        {
            case "MongoDBClient":
                $this->db = MongoDBClient::getInstance();
             break;
            
        }
        return $this->db;
    }

    public function parseResponse($result) {
        $resArr = array();
        if ($result) {
            $lines = explode("\n", $result);
            $head = str_getcsv(array_shift($lines), "|");

            foreach ($lines as $line) {
                $row = array_pad(str_getcsv($line, "|"), count($head), '');
                $resArr[] = array_combine($head, $row);
            }
        }
      return $resArr;
    }

    private function getRequestHeader($action) {
        
        $myAffiliateID =  $this->configOptions['Settings']['AffiliateID'];
        $APIToken = $this->configOptions['Settings']['APIToken'];"10ecldpyOJEVk1za";
        $APISecretKey = $this->configOptions['Settings']['APISecretKey'];
        $myTimeStamp = gmdate(DATE_RFC1123);
       
        $sig = $APIToken . ':' . $myTimeStamp . ':' . $action . ':' . $APISecretKey;

        $sigHash = hash("sha256", $sig);

        $myHeaders = array("x-ShareASale-Date: $myTimeStamp", "x-ShareASale-Authentication: $sigHash");

     return $myHeaders;
    }
    
    private function getRequestURL($action,$endQuery){
        $myAffiliateID =  $this->configOptions['Settings']['AffiliateID'];
        $APIToken = $this->configOptions['Settings']['APIToken'];"10ecldpyOJEVk1za";
        $APISecretKey = $this->configOptions['Settings']['APISecretKey'];
        $myTimeStamp = gmdate(DATE_RFC1123);
        $APIVersion = $this->configOptions['Settings']['APIVersion'];
        $APIURL = $this->configOptions['Settings']['APIURL'];
        
       return ("$APIURL?affiliateId=$myAffiliateID&token=$APIToken&version=$APIVersion&action=$action&$endQuery");
    }

    private function createRequest($action, $endQuery) {
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->getRequestURL($action,$endQuery));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getRequestHeader($action));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $returnResult = curl_exec($ch);

        if ($returnResult) {
            //parse HTTP Body to determine result of request
            if (stripos($returnResult, "Error Code ")) {
                // error occurred
                trigger_error($returnResult, E_USER_ERROR);
            }
        } else {
            // connection error
            trigger_error(curl_error($ch), E_USER_ERROR);
        }

        curl_close($ch);

        return $this->parseResponse($returnResult);
    }

}
