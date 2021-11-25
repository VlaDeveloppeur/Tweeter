<?php
/**
 * Les autoloader : 
 */
require_once 'src/mf/utils/AbstractClassLoader.php';
require_once 'src/mf/utils/ClassLoader.php';
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Route;
use tweeterapp\control\TweeterController as TweeterController;

/**
 * Instance du loader
 */
$loader = new \mf\utils\ClassLoader('src');
$loader->register();

session_start();
/**
 * Connexion à la base de donnée utilisant un fichier config.ini
 */
$config = parse_ini_file("conf/config.ini");
$db = new Illuminate\Database\Capsule\Manager();
$db->addConnection($config); /* configuration avec nos paramètres */
$db->setAsGlobal();            /* rendre la connexion visible dans tout le projet */
$db->bootEloquent();           /* établir la connexion */
/**
 * Les alias des classes :
 */
use \tweeterapp\model\User as User;
use \tweeterapp\model\Follow as Follow;
use \tweeterapp\model\Like as Like;
use \tweeterapp\model\Tweet as Tweet;
use \mf\router\Router;
use \tweeterapp\view\TweeterView as TweeterView;
use \tweeterapp\auth\TweeterAuthentification as TweeterAuthentification;

TweeterView::addStyleSheet('html/style.css');

/**
 * ViewHome
 */
 $ctrl = new tweeterapp\control\TweeterController();
 //$ctrl->viewHome();
/**
 * Ajout de route pour viewHome
 */
 $router = new Router();
 $router->addRoute('maison','/home/','\tweeterapp\control\TweeterController','viewHome',TweeterAuthentification::ACCESS_LEVEL_NONE);
 $router->addRoute('following','/following/','\tweeterapp\control\TweeterController','viewFollowingTweet',TweeterAuthentification::ACCESS_LEVEL_NONE);
 $router->setDefaultRoute('/home/');
 $router->addRoute('tweet', '/tweet/', '\tweeterapp\control\TweeterController', 'viewTweet',TweeterAuthentification::ACCESS_LEVEL_NONE);
 $router->addRoute('usertweets','/usertweets/','\tweeterapp\control\TweeterController','viewUserTweets',TweeterAuthentification::ACCESS_LEVEL_NONE);
 $router->addRoute('post','/post/','\tweeterapp\control\TweeterController','viewFormulaire',TweeterAuthentification::ACCESS_LEVEL_USER);
 $router->addRoute('send','/send/','\tweeterapp\control\TweeterController','dataForm',TweeterAuthentification::ACCESS_LEVEL_USER);
 $router->addRoute('login','/login/','\tweeterapp\control\TweeterAdminController','login',TweeterAuthentification::ACCESS_LEVEL_NONE);
 $router->addRoute('checklogin','/checklogin/','\tweeterapp\control\TweeterAdminController','checkLogin',TweeterAuthentification::ACCESS_LEVEL_NONE);
 $router->addRoute('logout','/logout/','\tweeterapp\control\TweeterAdminController','logOut',TweeterAuthentification::ACCESS_LEVEL_USER);
 $router->addRoute('signup','/signup/','\tweeterapp\control\TweeterAdminController','signUp',TweeterAuthentification::ACCESS_LEVEL_NONE);
 $router->addRoute('checksignup','/checksignup/','\tweeterapp\control\TweeterAdminController','checkSignUp',TweeterAuthentification::ACCESS_LEVEL_NONE);
 $router->addRoute('followers','/followers/','\tweeterapp\control\TweeterController','viewFollowers',TweeterAuthentification::ACCESS_LEVEL_USER);
 $router->addRoute('follow','/follow/','\tweeterapp\control\TweeterController','following',TweeterAuthentification::ACCESS_LEVEL_USER);
 $router->addRoute('liketweet','/liketweet/','\tweeterapp\control\TweeterController','liketweet',TweeterAuthentification::ACCESS_LEVEL_USER);
 $router->addRoute('disliketweet','/disliketweet/','\tweeterapp\control\TweeterController','disliketweet',TweeterAuthentification::ACCESS_LEVEL_USER);

//$router->run();

$router->run();