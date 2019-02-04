<?php 
namespace framework;

use framework\utils\LogUtils;

/**
 * Singleton permettant l'accès aux données de configuration
 * 
 * Le système va rechercher un fichier nommé config.ini devant être à la racine du projet
 * 
 */
class Config {
    
    /*
     * Clés disponible dans la configuration
     */
    const DB_HOST = "db_host";
    const DB_NAME = "db_name";
    const DB_USER = "db_user";
    const DB_PASSWORD = "db_password";
    const ROOT_URL = "root_url";
    const ROOT_PATH = "root_path";
    const EMAIL_INFO = "email_info";
    const DEFAULT_LOCAL = "default_local";
    
    /**
     * Instance du singleton
     * 
     * @var Config
     */
    private static $instance = null;
    
    /**
     * Tableau associatif contenant les données de configuration
     * 
     * @var array
     */
    private $params;
    
    /**
     * Construit la config depuis le fichier config.ini qui doit être à la racine du projet
     */
    function __construct() {
    	$this->params = parse_ini_file(dirname(__FILE__) . DS . ".." . DS . "conf" . DS . "config.ini");      
    }
    
    /**
     * Permet de récupérer une donnée de configuration
     * 
     * @param string $key clé permettant d'accéder à la donnée
     * 
     * @return string donnée de configuration
     */
    public static function get(string $key) : string {
        if(self::$instance === null) {
            self::$instance = new Config();
        }
        if(array_key_exists($key, self::$instance->params)) {
            return self::$instance->params[$key];
        } else {
            LogUtils::warning("key not found in config.ini file : " . $key);
        }
    }
}
?>