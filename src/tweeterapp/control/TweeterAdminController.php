<?php

namespace tweeterapp\control;

use Illuminate\Contracts\Auth\Access\Authorizable;
use mf\auth\Authentification;
use \tweeterapp\view\TweeterView;
use \mf\control\AbstractController;
use \tweeterapp\auth\TweeterAuthentification;
use \tweeterapp\model\User;
use \mf\router\Router;

class TweeterAdminController extends AbstractController
{

    public function login()
    {
        $formulaireConnexion = new tweeterView("");
        $formulaireConnexion->setAppTitle('Se connecter');
        $formulaireConnexion->render('renderLogin');
    }

    public function checkLogin()
    {
        if (isset($_POST['submit'])) {
            $authentification = new TweeterAuthentification();
            $authentification->loginUser($_POST['username'], $_POST['password']);
            if($authentification->logged_in){
                //récuperer les followers
                $user = User::where('username', '=', $_SESSION['user_login'])->first();
                $followers = $user->followedBy()->get();
                //créer la vue
                $vueLogin = new TweeterView($followers);
                $vueLogin->render('renderFollowers');
            }
        }
    }

    public function logOut()
    {
        if (isset($_SESSION['user_login'])) {
            $authentification = new Authentification();
            $authentification->logout();
        }
    }

    public function signUp(){
            $formulaireSignUp = new tweeterView("");
             $formulaireSignUp->setAppTitle('Sign Up');
             $formulaireSignUp->render('renderSignup');
    }

    public function checkSignUp(){
        if (isset($_POST['login_button'])) {
            //récuperer les données du formulaire : 
                $fullname = $_POST['fullname'];                                       //faire le filtrage plustard 
                $username = $_POST['username'];
                $password = $_POST['password'];
                $password_verify = $_POST['password_verify'];
                if($password === $password_verify){
                    $authentification = new TweeterAuthentification();
                    $authentification->createUser($username,$password,$fullname);       //comment mettre le level ?
                }
        }
    }

}
