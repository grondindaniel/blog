<?php


class Comment
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

    public function add($comment_date, $comment, $user_id, $post_id, $role)
    {
        if($role == 1){$status = 1;}else{$status = 0;}
        $req = $this->bdd->prepare("INSERT INTO comment (comment_date, comment, status, user_id, post_id) VALUES (:comment_date, :comment, :status, :user_id, :post_id)");
        $req->execute(array(
            ':comment_date'=>$comment_date,
            ':comment'=>$comment,
            ':status'=>$status,
            ':user_id'=>$user_id,
            ':post_id'=>$post_id
        ));
        var_dump($req);
    }
}

