<?php

namespace tweeterapp\model;
class User extends \Illuminate\Database\Eloquent\Model{

    protected $table= 'user';
    protected $primaryKey= 'id';
    public $timestamps = false;

    //dans le model User on crée la fonction tweet pour dire qu'un user Has many tweets
    public function tweets(){
       return $this->hasMany('tweeterapp\model\Tweet','author');                   //a user can have many tweets
    }

    //retourner les tweet apprécié par l'utilisateur
    public function liked(){
        return $this->belongsToMany('tweeterapp\model\Tweet','like','user_id','tweet_id');
    }

    //retourner les utilisateur qui suivent un certain user avec un certain id
    public function followedBy(){
        return $this->belongsToMany('tweeterapp\model\User','follow','followee','follower');
    }

    //retourner les utilisateur suivi par l'auteur 
    public function follow(){
        return $this->belongsToMany('tweeterapp\model\User','follow','follower','followee');
    }

}
