<?php

namespace tweeterapp\control;

use mf\utils\HttpRequest;
use \tweeterapp\model\Tweet as Tweet;
use \tweeterapp\model\User as User;
use \tweeterapp\view\TweeterView as tweeterView;
use \tweeterapp\model\Follow as Follow;
use \tweeterapp\model\Like as Like;

use \mf\router\Router;


class TweeterController extends \mf\control\AbstractController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * récuperer les données depuis le modèle tweet
     * instancier la vue correspondante
     * appeller la vue
     */
    public function viewHome()
    {
        $tweets = Tweet::select()->get();
        $vue = new TweeterView($tweets);
        $vue->setAppTitle('Accueil');
        $vue->render('renderHome');
    }

    /**
     * récuperer les tweet des abonnement
     * 
     * 
     */
    public function viewFollowingTweet()
    {
        $tweets = Tweet::select()->get();
        $vue = new TweeterView($tweets);
        $vue->setAppTitle('Accueil');
        $vue->render('renderHome');
    }


    /* Méthode viewTweet : 
     *  
     * Réalise la fonctionnalité afficher un Tweet correspondant à un certain id
     *
     */

    public function viewTweet()
    {
        //récuperer l'id du tweet depuis l'url
        $id_tweet = $this->request->get;                                   //recuperer l'id passé en GET
        $tweet = Tweet::select()->where('id', '=', $id_tweet)->first();         //récuperer l'objet tweet correspondant depuis le modèl
        $author = $tweet->author()->first();
        $tab_tweet[0]=$author->id;
        $tab_tweet[1]= ["id"=>$tweet->id,"text"=>$tweet->text,"score"=>$tweet->score,"created_at"=>$tweet->created_at];
        $vue_tweet = new tweeterView($tab_tweet);                           //instancier une vue corresponsante en lui fournissant les données recupéré depuis le model
        $vue_tweet->setAppTitle('Tweet');
        $vue_tweet->render('renderViewTweet');                         //appeller la vue
    }


    /* Méthode viewUserTweets :
     *
     * Réalise la fonctionnalité afficher les tweet d'un utilisateur
     *
     */

    public function viewUserTweets()
    {
        $requet = new HttpRequest();
        $id_user = $requet->get;
        $i = 0;
        $user = User::where('id', '=', $id_user)->first();
        $tweets = $user->tweets()->get();
        $nb_followers = count($user->followedBy()->get());
        foreach($tweets as $t){
            $tab_user_tweets[$i] = ["id"=>$t->id,"text"=>$t->text,"score"=>$t->score,"created_at"=>$t->created_at,"author_name"=>$user['fullname'],"author_id"=>$t->author,"nb_followers"=>$nb_followers];
            $i++;
        }
        $viewTweets = new tweeterView($tab_user_tweets);
        $user_fullname = $user['fullname'];
        $viewTweets->setAppTitle("Tweets de $user_fullname");
        $viewTweets->render('rendreUserTweets');
    }

    public function viewFormulaire()
    {
        $viewformulaire = new tweeterView("");
        $viewformulaire->setAppTitle('Tweet something...');
        $viewformulaire->render('renderPostTweet');
    }

    public function viewFollowers()
    {
        $userUsername = $_SESSION['user_login'];
        $user = User::where('username', '=', $userUsername)->first();
        $followers = $user->followedBy()->get();
        $viewFollowers = new tweeterView($followers);
        $viewFollowers->setAppTitle('Followers');
        $viewFollowers->render('renderFollowers');
    }
    public function dataForm()
    {
            $data = filter_var($_POST['tweet'], FILTER_SANITIZE_SPECIAL_CHARS);
            $nv_tweet = new Tweet();
            $nv_tweet->text = $data;
            //récuperer l'id du user connecté 
            $connected_user_username = $_SESSION['user_login'];
            $connected_user = User::where('username','=',$connected_user_username)->first();
            $connected_user_id = $connected_user->id;
            $nv_tweet->author = $connected_user_id;     
            $nv_tweet->score = 0;
            if($nv_tweet->save() >0){
                $route = new Router();
                $route->executeRoute('maison');
            }
    }
    //follower une personne
    public function following(){
        $id_followee = $_GET['id'];                                //sécuriser ça
        //récuperer le follower
        $follower = user::where('username','=',$_SESSION['user_login'])->first();
        $follower_id = $follower->id;
        //créer une instance de la classe follow :
        $follow = new Follow();
        $follow->follower = $follower->id;
        $follow->followee = $id_followee;
        if($follow->save()){
            $view_follow = new tweeterView("");
            $view_follow->render('renderfollow');
        }
    }

    public function liketweet(){
        $tweet_id = $_GET['id'];                        //sécuriser ça
        $tweet = Tweet::find($tweet_id);
        $user = user::where('username','=',$_SESSION['user_login'])->first();
        //créer une instance de la classe Like:
        //vérifier que le user n'a pas déjà liké le tweet
         $bool = false;
        $all_likes = Like::select()->get();
        foreach($all_likes as $like){
            if(($like->user_id === $user->id) && ($like->tweet_id === $tweet->id)){
                $bool = true;                   //le tweet a déjà été liké par ce user
            }
        }
        if($bool === false){        
            $like = new Like();
            $like->user_id = $user->id;
            $like->tweet_id = $tweet_id;             //le tweet n'a jamais été liké par ce user
            if($like->save()){
                $tweet->score = $tweet->score + 1;
                $tweet->save();
                $view_like = new tweeterView("");
                $view_like ->render('renderlike'); 
            }
        }else{
            $alerte = "Tweet already liked";
            $view_dislike = new tweeterView($alerte);
            $view_dislike ->render('renderAlerte');
        }  
    }

    public function disliketweet(){
        $tweet_id = $_GET['id'];                        //sécuriser ça
        $tweet = Tweet::find($tweet_id);
        //créer une instance de la classe Like:
        $delete_like = Like::select()->where('tweet_id',"=",$tweet_id);
        if($delete_like->delete()){
            $view_dislike = new tweeterView("");
            $tweet->score = $tweet->score - 1;
            $tweet->save();
            $view_dislike ->render('renderdislike');
        }else{
            $alerte = "Tweet can not be disliked";
            $view_dislike = new tweeterView($alerte);
            $view_dislike ->render('renderAlerte');
        }
    }
}
