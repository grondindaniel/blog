<?php


class Posts extends Acme\Controller
{

    /*
   * Function for adding post to post table
   */
    public function addPost($data)
    {
        $title = $_POST['title'];
        $chapo = $_POST['chapo'];
        $content = $_POST['content'];
        $auteur = $_POST['auteur'];
        $data = parent::model('Post')->addPost($title, $chapo, $content);
    }

    /*
    * Create view for add a post
    */
    public function AddPostPage()
    {
        $twig = parent::twig();
        echo $twig->render('post\pageAddPost.twig', array());
    }

    public function index()
    {
        $data = parent::model('Post')->getPosts();
        $twig = parent::twig();
        echo $twig->render('post\index.twig', array('data'=>$data));
    }
}