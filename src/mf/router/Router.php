<?php

namespace mf\router;
use \tweeterapp\control\TweeterAdminController;
use mf\auth\Authentification as authentification;
use tweeterapp\auth\TweeterAuthentification;

class Router extends AbstractRouter
{

    public function __construct()
    {
        parent::__construct();
    }

    /*
     * Méthode run : execute une route en fonction de la requête 
     *    (la requête est récupérée dans l'atribut $http_req)
     *
     * Algorithme :
     * 
     * - l'URL de la route est stockée dans l'attribut $path_info de 
     *         $http_request
     *   Et si une route existe dans le tableau $route sous le nom $path_info
     *     - créer une instance du controleur de la route
     *     - exécuter la méthode de la route 
     * - Sinon 
     *     - exécuter la route par défaut : 
     *        - créer une instance du controleur de la route par défault
     *        - exécuter la méthode de la route par défault
     * 
     */

   
    public function run() {
        $requet = $this->http_req;
        $url_route = $requet->path_info;
        $authentification = new authentification();
        /**
         * voir si le path_info existe déjà fans le tableau $routes
         */
          if (array_key_exists($url_route,self::$routes)){
              if ($authentification->checkAccessRight(self::$routes[$url_route][2])) {
                  $ctrl = self::$routes[$url_route][0];
                  $mth = self::$routes[$url_route][1];
                  $controleur = new $ctrl();
                  $controleur->$mth();
              }
          }else{
              $route_default = self::$aliases['default'];
              $ctrl = self::$routes[$route_default][0];
              $mth = self::$routes[$route_default][1];
              $controleur_default = new $ctrl();
              $controleur_default->$mth();
          }

    }


    public function urlFor($route_name, $param_list = [])
    {                //$route_name c'est l'alias 

        $url = self::$aliases[$route_name];
        $fullUrl = $this->http_req->script_name . $url;

        if (count($param_list) > 0) {
            $fullUrl .= "?";

            for ($i = 0; $i < count($param_list); $i++) {
                if ($i == (count($param_list) - 1)) {
                    $fullUrl .= $param_list[$i][0] . '=' . $param_list[$i][1];
                } else {
                    $fullUrl .= $param_list[$i][0] . '=' . $param_list[$i][1] . '&amp;';
                }
            }
        }

        return $fullUrl;
    }

    static function executeRoute($chaine_alias){
        //recuperer l'url correspondant à l'alias depuis le tableau des alias
        if(array_key_exists($chaine_alias,self::$aliases)){         //l'alias existe bien dans la table des alias
            $url = self::$aliases[$chaine_alias];
            //récuprer la méthode et le controleur correspondant à cet url 
            $ctrl = self::$routes[$url][0];
            $mth = self::$routes[$url][1];
            $controleur = new $ctrl();
            $controleur->$mth();
        }
    }

    public function addRoute($name, $url, $ctrl, $mth, $access_level)
    {
        self::$routes[$url] = [$ctrl, $mth, $access_level];
        self::$aliases[$name] = $url;
    }

    public function setDefaultRoute($url)
    {
        self::$aliases['default'] = $url;                    //la route par défaut si aucune URL n'est fourni 
    }
}
