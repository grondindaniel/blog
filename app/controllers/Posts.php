<?php


class Posts extends Acme\Controller
{

    /*
   * Function for adding post to post table
   */
    public function addPost()
    {
        $title = $_POST['title'];
        $chapo = $_POST['chapo'];
        $content = $_POST['content'];
        parent::model('Post')->addPost($title, $chapo, $content);
    }

    /*
    * Create view for add a post
    */
    public function addPostPage()
    {
        session_start();
        $twig = parent::twig();
        echo $twig->render('post\pageAddPost.twig', array('role'=>$_SESSION['role']));
    }

    /*
     * listing posts
     */
    public function index()
    {
        $data = parent::model('Post')->getPosts();
        $twig = parent::twig();
        echo $twig->render('post\index.twig', array('data'=>$data));
    }

    /*
     * method to show post content
     */
    public function show($id)
    {
        $data = parent::model('Post')->getContent($id);
        $twig = parent::twig();
        echo $twig->render('post\show.twig', array('content'=>$data));
    }

}
