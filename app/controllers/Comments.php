<?php

class Comments extends Acme\Controller
{
    /*
     * add method for comment
     */
    public function add()
    {
        session_start();
        $comment_date = date("Y-m-d H:i:s");
        $comment = $_POST['comment'];
        $user_id = $_SESSION['id'];
        $post_id = $_POST['post_id'];
        $role = $_SESSION['role'];
        parent::model('Comment')->add($comment_date, $comment, $user_id, $post_id, $role);
        $twig = parent::twig();
        echo $twig->render('post\index.twig',array('role'=>$_SESSION['role'],'active'=>$_SESSION['active'],'commentOk'=>'ok'));
    }
}

