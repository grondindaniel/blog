<?php

class Comments extends Acme\Controller
{
    /*
     * add method for comment
     */
    public function add()
    {
        session_start();
        $role = $_SESSION['role'];
        $comment_date = date("Y-m-d H:i:s");
        $comment = $_POST['comment'];
        $user_id = $_SESSION['id'];
        $post_id = $_POST['post_id'];

        parent::model('Comment')->add($comment_date, $comment, $user_id, $post_id, $role);
    }
}

