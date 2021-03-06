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
    public function addPost(string $title, string $chapo, string $content, string $author)
    {

        $user_id = $_SESSION['id'];
        $last_update = date("Y-m-d H:i:s");
        $req = $this->bdd->prepare("INSERT INTO post (title,chapo,content,last_update,user_id, author) VALUES (:title,:chapo,:content,:last_update,:user_id, :author)");
        $req->execute(array(':title'=>$title,':chapo'=>$chapo,':content'=>$content,':last_update'=>$last_update,':user_id'=>$user_id, ':author'=>$author));
        return true;
    }

    /*
     * give all posts off post table
     */
    public function getPosts()
    {
        $req = $this->bdd->prepare('select * from post ORDER BY id DESC');
        $req->execute();
        $data = $req->fetchAll();
        return $data;
    }

    /*
     * get data for display content
     */
    public function getContent($id)
    {
        $id = $id[2];
        $req = $this->bdd->prepare(
            'SELECT post.id, post.title, post.chapo, post.content, post.last_update, post.user_id, post.author,
 comment.comment, comment.comment_date, comment.status, comment.user_id, comment.post_id, user.lastname FROM post
   LEFT JOIN comment ON 
   post.id = comment.post_id
   LEFT JOIN user ON 
   comment.user_id = user.id
   where post.id =:id');
        $req->bindValue(':id',$id,PDO::PARAM_INT);
        $req->execute();
        $data = $req->fetchAll();
        return $data;
    }

    /*
    * get data for display content
    */
    public function changePost($id, $title, $chapo, $content, $author)
    {
        $id = intval($_POST['id']);
        $title = $_POST['title'];
        $chapo = $_POST['chapo'];
        $content = $_POST['content'];
        $author = $_POST['author'];
        $last_update = date("Y-m-d H:i:s");
        $req = $this->bdd->prepare("UPDATE post SET id=:id, title=:title, chapo=:chapo, content=:content, last_update=:last_update, author=:author WHERE id=:id");
        $req->bindValue(':id',$id,PDO::PARAM_INT);
        $req->execute(array(
            ':id'=>$id,
            ':title'=>$title,
            ':chapo'=>$chapo,
            ':content'=>$content,
            ':last_update'=>$last_update,
            ':author'=>$author
        ));
    }

    /*
    * get post to destroy
    */
    public function destroyPost($id)
    {
        $id = intval($id);
        $req = $this->bdd->prepare("DELETE FROM post WHERE id=:id");
        $req->bindValue(':id',$id,PDO::PARAM_INT);
        $req->execute();
        $req = $this->bdd->prepare("DELETE FROM comment WHERE post_id=:id");
        $req->bindValue(':id',$id,PDO::PARAM_INT);
        $req->execute();
    }
}

