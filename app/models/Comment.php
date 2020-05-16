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

    /*
     * insert data in comment table
     */
    public function add($comment_date, $comment, $user_id, $post_id, $role)
    {
        if($role != 1){$status = 0;}else{$status = 1;}
        $req = $this->bdd->prepare("INSERT INTO comment (comment_date, comment, status, user_id, post_id) VALUES (:comment_date, :comment, :status, :user_id, :post_id)");
        $req->execute(array(
            ':comment_date'=>$comment_date,
            ':comment'=>$comment,
            ':status'=>$status,
            ':user_id'=>$user_id,
            ':post_id'=>$post_id
        ));
    }

    /*
     * listing of comments not validated
    */
    public function listWaitingComments()
    {
        $req = $this->bdd->prepare("SELECT * FROM comment WHERE status = 0");
        $req->execute();
        $nb = $req->fetchAll();
        $nb = count($nb);
        return $nb;
    }

    /*
     * listing of comments not validated
     */
    public function nbComments()
    {
        $req = $this->bdd->prepare("SELECT * FROM comment WHERE status = 1");
        $req->execute();
        $nb = $req->fetchAll();
        $nb = count($nb);
        return $nb;
    }

    /*
     * listing of comments who are validated
     */
    public function listWaitingCommentsForValidation()
    {
        $req = $this->bdd->prepare("SELECT comment.id, comment.comment_date, comment.comment, comment.status, comment.user_id, comment.post_id, email.email FROM comment
 INNER JOIN email on comment.user_id = email.user_id WHERE comment.status = 0");
        $req->execute();
        $d = $req->fetchAll();
        return $d;
    }

    /*
     * Validate new comments
     */
    public function changeStatus($tab)
    {
        foreach ($tab as $id)
        {
            $req = $this->bdd->prepare("UPDATE comment set status = 1 where id =:id");
            $req->bindValue(':id',$id,PDO::PARAM_INT);
            $req->execute();
        }
    }
}

