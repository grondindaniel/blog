<?php

/***
 *
 * this is the main Controller
 *
 */

namespace Acme;

class Controller
{
    /*
     * recover data from database
     */
    public function model($model)
    {
        require_once 'app/models/'.$model.'.php';
        return $model();
    }


    /*
     * Use Twig template for generate views
     */
    public function twig()
    {
        $loader = new \Twig\Loader\FilesystemLoader('app/views/templates');
        $twig = new \Twig\Environment($loader, []);

        echo $twig->render('index.html', []);
    }
}
