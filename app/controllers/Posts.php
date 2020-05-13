<?php


class Posts extends Acme\Controller
{

    /*
   * method for adding post to post table
   */
    public function addPost()
    {
        $title = $_POST['title'];
        $chapo = $_POST['chapo'];
        $content = $_POST['content'];
        parent::model('Post')->addPost($title, $chapo, $content);
    }

    /*
     * method to access editing list post page
     */
    public function editPostPage()
    {
        session_start();
        $data = parent::model('Post')->getPosts();
        $twig = parent::twig();
        echo $twig->render('post\editPostPage.twig', array('data'=>$data,'role'=>$_SESSION['role']));
    }

    /*
     * method to access editing a post
     */
    public function editPost($id)
    {
        session_start();
        $data = parent::model('Post')->getContent($id);
        $twig = parent::twig();
        echo $twig->render('post\editPost.twig', array('data'=>$data,'role'=>$_SESSION['role']));
    }

    /*
    * method to destroy a post
    */
    public function destroyPost($id)
    {
        $id = $_POST['id'];
        $d = parent::model('Post')->destroyPost($id);
    }

    /*
     * method to change post values
     */
    public function changePost()
    {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $chapo = $_POST['chapo'];
        $content = $_POST['content'];
        parent::model('Post')->changePost($id, $title, $chapo, $content);
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
        session_start();
        $data = parent::model('Post')->getPosts();
        $twig = parent::twig();
        echo $twig->render('post\index.twig', array('data'=>$data, 'active'=>$_SESSION['active']));
    }

    /*
     * method to show post content
     */
    public function show($id)
    {
        session_start();
        $data = parent::model('Post')->getContent($id);
        $twig = parent::twig();
        echo $twig->render('post\show.twig', array('content'=>$data,'role'=>$_SESSION['role'], 'active'=>$_SESSION['active']));
    }

}
