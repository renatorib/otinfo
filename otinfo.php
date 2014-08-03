<?php

/**
 * @package Open Tibia Information <otinfo>
 * @version 1.2.1
 * @author Renato Ribeiro <renatoribeiro@tibiaking.com>
 * @author Ranieri Althoff <ranieri@inf.ufsc.br>
 * @contributors yrpen, gpedro, DSpeichert
 * @copyright 2014 (C) by Renato Ribeiro <http://tibiaking.com>, Ranieri Althoff <http://otserv.com.br>
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt GNU Lesser General Public License, Version 3
 * 
 * Official repository: https://github.com/renatorib/otinfo
 *
 */

class Otinfo {

    // Server attributes.
    private $attributes;
    
    // Server connection information.
    private static $host;
    private static $port;
    
    // Cache time in seconds. Set to zero if unwanted. */
    private static $cache = 120;
    
    /** 
     * Magic message
     * what makes Open Tibia servers answer with their info. 
     */
    private static $message;
	
    function __construct($host, $port = 7171) {
        
        static::$host = $host;
        static::$port = $port;
        
        /**
         * We initialize here since PHP does not 
         * support non-trivial initializers 
         */
        static::$message = chr(6).chr(0).chr(255).chr(255).'info';
    }
	
    /**
     * Retrieve informations from cache or socket.
     *
     * @return bool
     */
    public function execute() {
	    
        // Localization of the cache file
        $cache_uri = 'cache' . DIRECTORY_SEPARATOR . static::$host . '.json';
        
        if (static::$cache && file_exists($cache_uri) && filemtime($cache_uri) + static::$cache >= time()) {
        	
       	    $json = file_get_contents($cache_uri);
       	    $this->attributes = json_decode($json, true);
       	    return true;
       	    
       	} else {
       			
            /* Open socket with server */
            if ($socket = @fsockopen(static::$host, static::$port, $errno, $errstr, 5)) {

                /* Write magic string to the socket and get the response */
                $data = '';
                fwrite($socket, static::$message);
                while(!feof($socket)) {
                    $data .= fread($socket, 1024);
                }
                fclose($socket);
				
                /* Parse XML response */
                $this->parseFromXml($data);
				
                /* Write response to a file if caching is enabled */
                if (static::$cache && (is_dir('cache')) || mkdir('cache')) {
                    file_put_contents($cache_uri, $this);
                }
                
                return true;
            }
        return false;
        }
    }

    private function parseFromXml($xml){
        $array = simplexml_load_string($xml);
        $atributes_array = array('serverinfo', 'owner', 'players', 'monsters', 'map', 'rates', 'npcs');
        
        /* Check if is set and loop over the atributes_array's nodes. */
        foreach ($atributes_array as $atribute_name) {
            if (isset($array->$atribute_name)) {
                foreach ($array->$atribute_name->attributes() as $index => $value) {
                    $this->attributes[$atribute_name][$index] = (string)$value;
                }
            }
        }

        /* Check if is set and set the motd node. */
        if (isset($array->motd)) {
            $this->attributes['motd'] = (string)$array->motd;
        }
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key) {
        return $this->attributes[$key];
    }
	
    /**
     * Dynamically set attributes on the model.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function __set($key, $value) {
        $this->attributes[$key] = $value;
    }
	
    /**
     * Convert the model to its string representation.
     *
     * @return string
     */
    public function __toString() {
        return json_encode($this->attributes);
    }
}
