<?php
namespace framework\utils;

use framework\models\Entity;

/**
 * Classe utilitaire permettant la gestion des requêtes HTTP
 * 
 * @author Vince
 *
 */
class RequestUtils {

    const REQUIRED_FIELD = "Champ requis !";
    
    /**
     * Permet de créer une liste simple depuis une donnée de la requête HTTP
     * 
     * @param array $data               Données de la requête HTTP
     * @param string $fieldName         Clé afin d'accéder à la donnée souhaitée
     * @param array $responseParams     Réponse à envoyer au client Web
     * @param bool $required            Indique si le champ est requis
     * @param bool $addToRequest        Indique si un message doit être ajouter à la réponse
     * 
     * @return array 
     */
    public static function bindListRequestParam(array $data, string $fieldName, array &$responseParams, bool $required=false, bool $addToRequest=true) : array {
        $result = array();
        
        $liste = $data[$listFieldName];
        for($i=0; $i < count($liste); $i++) {
            $result[] = RequestUtils::bindRequestParam($liste, $i,  $fieldName . "[" . $i . "]", $responseParams, $required, $addToRequest);
        }
        
        return $result;
    }
    
    /**
     * Permet de créer une liste d'objets depuis une donnée de la requête HTTP
     * 
     * @param array $data               Données de la requête HTTP
     * @param string $fieldName         Clé afin d'accéder à la donnée souhaitée
     * @param string $class             Classe de l'objet à instancier dans la liste
     * @param array $responseParams     Réponse à envoyer au client Web
     * @param bool $addToRequest        Indique si un message doit être ajouter à la réponse
     * 
     * @return Entity[]
     */
    public static function bindListObjectRequestParam(array $data, string $fieldName, string $class, array &$responseParams, bool $addToRequest=true) : array {
        $result = array();
        
        $liste = $data[$fieldName];
        for($i=0; $i < count($liste); $i++) {
            $item = $liste[$i];
            $result[] = RequestUtils::bindObjectRequestParam($item, $fieldName . "[" . $i . "]", $class, $responseParams, $addToRequest);
        }
        
        return $result;
    }
    
    /**
     * Permet de créer un objet depuis une donnée de la requête HTTP
     * 
     * @param array $data               Données de la requête HTTP
     * @param string $fieldName         Clé afin d'accéder à la donnée souhaitée
     * @param string $class             Classe de l'objet à instancier
     * @param array $responseParams     Réponse à envoyer au client Web
     * @param bool $addToRequest        Indique si un message doit être ajouter à la réponse
     * 
     * @return mixed
     */
    public static function bindObjectRequestParam(array $data, string $fieldName, string $class, array &$responseParams, bool $addToRequest=true) {
        return $class::bindFromRequest($data, $fieldName, $responseParams, $addToRequest);
    }
    
    /**
     * Permet de récupérer une données une requête HTTP 
     * 
     * @param array $data               Données de la requête HTTP
     * @param string $fieldName         Clé afin d'accéder à la donnée souhaitée
     * @param string $longFieldName     Id de l'élément HTML
     * @param array $responseParams     Réponse à envoyer au client Web
     * @param bool $required            Indique si le champ est requis
     * @param bool $addToRequest        Indique si un message doit être ajouter à la réponse
     * 
     * @return mixed|NULL
     */
    public static function bindRequestParam(array $data, string $fieldName, string $longFieldName, array &$responseParams, bool $required=false, bool $addToRequest=true) {
        if($required && RequestUtils::checkRequestParam($fieldName, $responseParams, $addToRequest)) {
            return $data[$fieldName];
        } else if(isset($data[$fieldName])) {
            if($addToRequest) {
                $responseParams[$longFieldName] = serialize( array (
                    "value" => $data[$fieldName]
                ));
            }
            return $data[$fieldName];
        }
        
        return null;
    }
    
    /**
     * Check si le champ existe dans la requête HTTP
     * 
     * @param array $data 				Tableau de données à contrôler 
     * @param string $fieldName         Clé afin d'accéder à la donnée souhaitée
     * @param array $responseParams     Réponse à envoyer au client Web
     * @param bool $addToRequest        Indique si un message doit être ajouter à la réponse
     * 
     * @return bool
     */
    public static function checkRequestParam(array $data, string $fieldName, array &$responseParams, bool $addToRequest=true): bool {
        // TODO: remove $_POST access
    	if (! isset($data[$fieldName])) {
            if($addToRequest){
                $responseParams[$fieldName] = serialize(array(
                    "error" => RequestUtils::REQUIRED_FIELD
                ));
            }
            return false;
        } else {
            if($addToRequest){
                $responseParams[$fieldName] = serialize(array(
                	"value" => $data[$fieldName]
                ));
            }
            
            return true;
        }
    }

    /**
     * Ajoute un message d'erreur à la réponse fournie au client Web
     * 
     * @param string $fieldName         Clé afin d'accéder à la donnée souhaitée
     * @param string $errorMessage      Message d'erreur
     * @param array $responseParams     Réponse à envoyer au client Web
     */
    public static function addErrorRequestParam(string $fieldName, string $errorMessage, array &$responseParams) {
        if (isset($responseParams[$fieldName])) {
            $fieldValues = unserialize($responseParams[$fieldName]);
            $fieldValues["error"] = $errorMessage;
            $responseParams[$fieldName] = serialize($fieldValues);
        } else {
            $responseParams[$fieldName] = serialize(array(
                "error" => $errorMessage
            ));
        }
    }
    
    /**
     * Récupère une depuis une requête HTTP
     * 
     * @param string $fieldName         Clé afin d'accéder à la donnée souhaitée
     * @return mixed|NULL
     */
    public static function getRequestParamValue(string $fieldName) {
        // TODO: remove GET access
        if (isset($_GET[$fieldName])) {
            $fieldValues = unserialize($_GET[$fieldName]);
            if(isset($fieldValues["value"])) {
                return $fieldValues["value"];
            }
        } 
        
        return null;
    }
}
?>