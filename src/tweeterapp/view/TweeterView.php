<?php

namespace tweeterapp\view;

use Illuminate\Contracts\Auth\Authenticatable;
use mf\auth\Authentification;
use \mf\router\Router as Router;
use mf\utils\HttpRequest;
use tweeterapp\auth\TweeterAuthentification;
use tweeterapp\model\Tweet;

class TweeterView extends \mf\view\AbstractView
{

    /* Constructeur 
    *
    * Appelle le constructeur de la classe parent
    */
    public function __construct($data)
    {
        parent::__construct($data);
    }

    /* Méthode renderHeader
     *
     *  Retourne le fragment HTML de l'entête (unique pour toutes les vues)
     */
    private function renderHeader()
    {
        return '<header class="theme-backcolor1"><h1>MiniTweeTR</h1>';
    }

    /* Méthode renderFooter
     *
     * Retourne le fragment HTML du bas de la page (unique pour toutes les vues)
     */
    private function renderFooter()
    {
        return "</section><footer class=\"theme-backcolor1\"> La super app créée en Licence Pro &copy;2021 </footer>";
    }

    /* Méthode renderHome
     *
     * Vue de la fonctionalité afficher tous les Tweets. 
     *  
     */

    private function renderHome()
    {
        $route = new Router();
        $tweets = $this->data;
        $viewTweet = "<article class=\"theme-backcolor2\">  <h2>Latest Tweets</h2>";
        foreach ($tweets as $tweet) {
            $author = $tweet->author()->first();
            $link_tweet = $route->urlFor('tweet', [['id', $tweet->id]]);                  //récuperer l'url du tweet sur lequel on a cliqué
            $link_user = $route->urlFor('usertweets', [['id', $author->id]]);
            $viewTweet .= "
            <div class=\"tweet\"><a href=$link_tweet><div class=\"tweet-text\">$tweet->text</div></a><div class=\"tweet-footer\"><span class=\"tweet-timestamp\">$tweet->created_at</span><span class=\"tweet-author\"><a href=$link_user>$author->username</a></span></div></div>";
        }
        $viewTweet .= "</article>";
        return $viewTweet;
    }

    /* Méthode renderFollowing
     *
     * Vue de la fonctionalité afficher tous les Tweets des gens que l'utilisateur suit
     *  
     */

    private function renderFollowing()
    {
        $route = new Router();
        $tweets = $this->data;
        $viewTweet = "<article class=\"theme-backcolor2\">  <h2>Latest Tweets</h2>";
        foreach ($tweets as $tweet) {
            foreach ($tweet as $key) {
                $author = $key->author()->first();
                $link_tweet = $route->urlFor('tweet', [['id', $key->id]]);
                $link_user = $route->urlFor('usertweets', [['id', $author->id]]);
                $viewTweet .= "
                <div class=\"tweet\"><a href=$link_tweet><div class=\"tweet-text\">".$key['text']."</div></a><div class=\"tweet-footer\"><span class=\"tweet-timestamp\">".$key['created_at']."</span><span class=\"tweet-author\"><a href=$link_user>".$author["fullname"]."</a></span></div></div>";
            }
        }

        $viewTweet .= "</article>";
       return $viewTweet;
    }

    /* Méthode renderUserTweets
     *
     * Vue de la fonctionalité afficher tout les Tweets d'un utilisateur donné. 
     * 
     */

    private function renderUserTweets()
    {
        $route = new Router();
        $tweets_user = $this->data;
        $i = 0;
        $nb_followers = $tweets_user[$i]["nb_followers"];
        $view_user_tweet = "<article class=\"theme-backcolor2\"><h2>Tweets from  user</h2><h3> $nb_followers followers</h3>";
        while($i<count($tweets_user)){
            $tweet_text = $tweets_user[$i]["text"];
            $tweet_date = $tweets_user[$i]["created_at"];
            $tweet_author =  $tweets_user[$i]["author_name"];
            $tweet_id = $tweets_user[$i]["id"];
            $tweet_author_id = $tweets_user[$i]["author_id"];
            $link_author = $route->urlfor( 'usertweets',[["id",$tweet_author_id]]);
            $link_tweet =  $route->urlfor( 'tweet',[["id",$tweet_id]]);
         
             $view_user_tweet .= 
             "<div class=\"tweet\"><a href=".$link_tweet."><div class=\"tweet-text\">$tweet_text</div></a><div class=\"tweet-footer\"><span class=\"tweet-timestamp\">$tweet_date</span><span class=\"tweet-author\"><a href=\"\">$tweet_author</a></span></div></div><div class=\"pager\"></div>";
             $i++;
            }
        $view_user_tweet .= "</article>";
        return $view_user_tweet;

      
    }

    /* Méthode renderViewTweet 
     * 
     * Rréalise la vue de la fonctionnalité affichage d'un tweet
     *
     */

    private function renderViewTweet()
    {
        $view_tweet = "";
        $tweets = $this->data;
        $route = new Router();
        $author_id = $tweets[0];
        $tweet_text = $tweets[1]['text'];
        $tweet_score = $tweets[1]['score'];
        $tweet_created_at = $tweets[1]['created_at'];
        $view_tweet .= "
        <article class='theme-backcolor2'><div class='tweet'><div class='tweet-text'>$tweet_text</div></a><div class='tweet-footer'><span class='tweet-timestamp'>$tweet_created_at</span></div><div class='tweet-footer'><hr><span class='tweet-score tweet-control'>$tweet_score</span><a class=\"tweet-control\" href=\"".$route->urlfor("liketweet",[['id',$tweets[1]['id']]])."\"><img alt=\"Like\" src=\"../../html/figs/like.png\"></a><a class=\"tweet-control\" href=\" ".$route->urlfor("disliketweet",[['id',$tweets[1]['id']]])."\"><img alt=\"dislike\" src=\"../../html/figs/dislike.png\"></a><a class=\"tweet-control\" href=\"".$route->urlfor("follow",[['id',$author_id]])."\"><img alt=\"follow\" src=\"../../html/figs/follow.png\"></a>
        </div></div></article>   
        ";
        return $view_tweet;
    }

    private function renderfollow(){
        return "
        <article class='theme-backcolor2'><div>Abonnemment ajouté !</div></article>  ";
    }

    private function renderlike(){
        $route = new Router();
        $id_tweet = $_GET['id'];
        $chemin = $route->urlfor('tweet',[['id',$id_tweet]]);
                return "
        <article class='theme-backcolor2'><div>Tweet liked</div><br/>
        <div><a class=\"tweet-control\" href=\" $chemin \"><img alt=\"back\" src=\"../../html/figs/back.png\"></a></div></article>";
    }
    private function renderdislike(){
        $route = new Router();
        $id_tweet = $_GET['id'];
        $chemin = $route->urlfor('tweet',[['id',$id_tweet]]);
        return "
        <article class='theme-backcolor2'><div>Tweet disliked</div><br/>
        <div><a class=\"tweet-control\" href=\" $chemin \"><img alt=\"back\" src=\"../../html/figs/back.png\"></a></div></article>";
    }
    private function renderAlerte(){
        $route = new Router();
        $id_tweet = $_GET['id'];
        $chemin = $route->urlfor('tweet',[['id',$id_tweet]]);
        $alerte = $this->data;
        return "
        <article class='theme-backcolor2' style='color : red'><div>$alerte</div><br/>
        <div><a class=\"tweet-control\" href=\" $chemin \"><img alt=\"back\" src=\"../../html/figs/back.png\"></a></div></article>";
    }

    /* Méthode renderPostTweet
     *
     * Realise la vue de régider un Tweet
     *
     */
    protected function renderPostTweet()
    {

        /* Méthode renderPostTweet
         *
         * Retourne la framgment HTML qui dessine un formulaire pour la rédaction 
         * d'un tweet, l'action (bouton de validation) du formulaire est la route "/send/"
         *
         */
        //$route->urlFor('send', [])
        $route = new Router();
            $formulaire = '
            
            <article class="theme-backcolor2">  <form action='.$route->urlfor('send',[]).' method=post>
            <textarea id="tweet-form" name="tweet" placeholder="Enter your tweet...", maxlength=140></textarea>
            <div><input id="send_button" type=submit name="send" value="Send"></div>
            </form> </article>   
            ';
        return $formulaire;
    }

    //formulaire de connexion 
    protected function renderLogin()
    {
        $route = new Router();
        $formulaire_connexion = '
        <article class="theme-backcolor2">  <form class="forms" method="post" action= ' . $route->urlFor('checklogin', []) . '>
        <input class="forms-text" type=text name=username placeholder="username">
        <input class="forms-text" type=password name=password placeholder="password">
        <button class="forms-button" name=submit type="submit">Login</button>
        </form> </article>   
        <nav id="menu" class="theme-backcolor1">  </nav> ';

        return $formulaire_connexion;
    }


    protected function renderFollowers()
    {
        $authentification = new TweeterAuthentification();
        if ($authentification->logged_in) {         //le user est connecté et donc lui afficher la liste de ses followers
            $followersHtml = "<article class=\"theme-backcolor2\"><h2>Currently following</h2>";
            $followers = $this->data;
            $nbfollow = count($followers);
            $followersHtml .= "<article class=\"theme-backcolor2\"><h2>$nbfollow user following you</h2>";
            foreach ($followers as $follower) {
                $followersHtml .= "
        <div class=\"tweet\" >
        <div class=\"Tweet-author\" >$follower->fullname follow you </div>
        </div> ";
            }
        }
     $followersHtml .= "</article> ";
     return $followersHtml;
    }

    protected function renderSignup()
    {
        $route = new Router();
        $signupHtml = '
    <article class="theme-backcolor2">  <form class="forms" action='. $route->urlFor('checksignup', []) .' method=post >
    <input class="forms-text" type=text name=fullname placeholder="full Name">
    <input class="forms-text" type=text name=username placeholder="username">
    <input class="forms-text" type=password name=password placeholder="password">
    <input class="forms-text" type=password name=password_verify placeholder="retype password">
    <button class="forms-button" name=login_button type="submit">Create</button>
    </form> </article>   
      <nav id="menu" class="theme-backcolor1">  </nav>  ';
        return $signupHtml;
    }



    protected function renderBottomMenu()
    {
        $route = new Router();
        $bottomMenuHtml = 
        '
         <nav id="menu" class="theme-backcolor1"> <div id="nav-menu"><div class="button theme-backcolor2"><a href=' . $route->urlFor('post', []) .'>New</a></div></div> </nav> ';
        return $bottomMenuHtml;
    }


    protected function renderTopMenu()
    {
        $route = new Router();
        $auth = new TweeterAuthentification();
        if ($auth->logged_in) {
            $topmenuHtml = '<nav id="navbar">
            <a class="tweet-control" href=' . $route->urlfor('maison') . '><img alt="home" src="../../html/figs/home.png"></a>
            <a class="tweet-control" href=' . $route->urlfor('following') . ' ><img alt="following" src="../../html/figs/following.png"></a>
            <a class="tweet-control" href=' . $route->urlfor('followers') . ' ><img alt="login" src="../../html/figs/followees.png"></a>
            <a class="tweet-control" href=' . $route->urlfor('logout') . ' ><img alt="signup" src="../../html/figs/logout.png"></a>
            </nav></header><section>';
        }else{
            $topmenuHtml = '
        <nav id="navbar">
        <a class="tweet-control" href=' . $route->urlfor('maison') . '><img alt="home" src="../../html/figs/home.png"></a>
        <a class="tweet-control" href=' . $route->urlfor('login') . '><img alt="login" src="../../html/figs/login.png"></a>
        <a class="tweet-control" href=' . $route->urlfor('signup') . '><img alt="signup" src="../../html/figs/signup.png"></a>
        </nav></header><section>';
        }
        return $topmenuHtml;
    }
    /* Méthode renderBody
     *
     * Retourne la framgment HTML de la balise <body> elle est appelée
     * par la méthode héritée render.
     *
     */
    protected function renderBody($selector)
    {
        $authentification = new TweeterAuthentification();
        $header = $this->renderHeader();
        $footer = $this->renderFooter();
        $topMenu = $this->renderTopMenu();
        $bottomMenu = $this->renderBottomMenu();

        switch ($selector) {
            case 'renderHome':
                $content = $this->renderHome();
                break;
            case 'renderFollowing':
                $content = $this->renderFollowing();
                break;
            case 'rendreUserTweets':
                $content = $this->renderUserTweets();
                break;
            case 'renderViewTweet':
                $content = $this->renderViewTweet();
                break;
            case 'renderPostTweet':
                $content = $this->renderPostTweet();
                break;
            case 'renderLogin':
                $content = $this->renderLogin();
                break;
            case 'renderFollowers':
                $content = $this->renderFollowers();
                break;
            case 'renderSignup':
                $content = $this->renderSignup();
                break;
            case 'renderfollow':
                $content = $this->renderfollow();
                break;
            case 'renderlike' :
                $content = $this->renderlike();
                break;
            case 'renderdislike' :
                $content = $this->renderdislike();
                    break;
            case 'renderAlerte' :
                $content = $this->renderAlerte();
                    break;
            default:
                echo "error";
        }



        if ($authentification->logged_in) {
            $body = <<<EOT
${header}  
${topMenu}                                      
${content}
${bottomMenu}
${footer}
EOT;
        } else {
            $body = <<<EOT
        ${header}  
        ${topMenu}                                      
        ${content}
        ${footer}
        EOT;
        }
        return $body;
    }
}
