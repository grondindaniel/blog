<?php

/*
 * Home controller
 */

class Homes extends Acme\Controller
{
   public function index()
   {
       $twig = parent::twig;
       echo $twig->render('home\index.twig', []);
   }
}
