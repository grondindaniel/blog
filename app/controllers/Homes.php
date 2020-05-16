    <?php

    /*
     * Home controller
     */

    class Homes extends Acme\Controller
    {
       public function index()
       {
           session_start();
           $twig = parent::twig();
           echo $twig->render('home\index.twig', array('active'=>$_SESSION['active'], 'role'=>$_SESSION['role']));
       }
    }
