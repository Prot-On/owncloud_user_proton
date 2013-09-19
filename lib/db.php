<?php

namespace OCA\Proton;

class Database {
        
    static $db;
    
    public static function isDBConfigured() {
        return !is_null(\OC_Config::getValue('user_proton_db_connection'))
            && !is_null(\OC_Config::getValue( "user_proton_mysql_login" ))
            && !is_null(\OC_Config::getValue( "user_proton_mysql_password" ));
    }
    
    public static function openConnection() {
        if (is_null(self::$db)) {
            if (!self::isDBConfigured()) {
                throw new \Exception("Database not configured");
            }
            try {
                self::$db = new \PDO(\OC_CONFIG::getValue('user_proton_db_connection'), \OC_CONFIG::getValue('user_proton_hosting_admin_login'), \OC_CONFIG::getValue('user_proton_hosting_admin_password'));            
            } catch (PDOException $e) {
                Util::log("Error db: " . $e->getMessage());
                die();
            }
        }        
    }
    
    public static function prepare($query, $limit = null, $offset = null) {
        //Util::log("Querying: " . $query);
        self::openConnection();
        if (is_numeric($limit) && $limit != -1) {
            //PDO does not handle limit and offset.
            //FIXME: check limit notation for other dbs
            //the following sql thus might needs to take into account db ways of representing it
            //(oracle has no LIMIT / OFFSET)
            $limit = (int)$limit;
            $limitsql = ' LIMIT ' . $limit;
            if (is_numeric($offset)) {
                $offset = (int)$offset;
                $limitsql .= ' OFFSET ' . $offset;
            }
            //insert limitsql
            if (substr($query, -1) == ';') { //if query ends with ;
                $query = substr($query, 0, -1) . $limitsql . ';';
            } else {
                $query.=$limitsql;
            }
        }
        return self::$db->prepare($query);
    }
    
}
?>