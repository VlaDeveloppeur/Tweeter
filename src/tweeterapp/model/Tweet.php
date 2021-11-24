<?php

namespace tweeterapp\model;
class Tweet extends \Illuminate\Database\Eloquent\Model{

    protected $table= 'tweet';
    protected $primaryKey= 'id';
    public $timestamps = true;


    // un Tweet est rédigé par un utilisateur et un utilisateur rédige plusieurs Tweets. En s'inspirant de l'exemple ci-dessus :
    //dans le model tweet on crée la méthode User 
    public function author(){
        return $this->belongsTo('tweeterapp\model\User','author');              // a tweet with a certain Id belongs to a author
    }

    public function likedBy(){
        return $this->belongsToMany('tweeterapp\model\User','like','tweet_id','user_id');
    }
}

