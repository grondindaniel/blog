<?php

/*
 * This class has for mission to
 * collect data from post table
 */


class Post
{
    public $host = 'localhost';
    public $dbname = 'blog2';
    public $user = 'root';
    public $pwd = '';
    public $bdd;

    public  function __construct()
    {
        $this->bdd = new pdo('mysql:host='.$this->host.';dbname='.$this->dbname,$this->user,$this->pwd,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    }

    /*
     * Function for insert post to post table
     */
    public function addPost(string $title, string $chapo, string $content)
    {

        $user_id = 23;
        $last_update = date("Y-m-d H:i:s");
        $req = $this->bdd->prepare("INSERT INTO post (title,chapo,content,last_update,user_id) VALUES (:title,:chapo,:content,:last_update,:user_id)");
        $req->execute(array(':title'=>$title,':chapo'=>$chapo,':content'=>$content,':last_update'=>$last_update,':user_id'=>$user_id));
        return true;
    }

    public function getPosts()
    {
        $req = $this->bdd->prepare('select * from post ORDER BY id DESC');
        $req->execute();
        $data = $req->fetchAll();
        return $data;
    }
}