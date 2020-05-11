<?php

class Comments extends Acme\Controller
{
    public function index($id)
    {
        $id = $id[2];
        $twig = parent::twig();
        echo $twig->render('comment\index.twig',array('id'=>$id));
    }
}

