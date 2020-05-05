<?php



class Users extends Acme\Controller
{
    public function login()
    {
        $twig = parent::twig();
        echo $twig->render('admin\login_admin.twig',array());
    }
}