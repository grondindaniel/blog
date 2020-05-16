<?php

    /***
     *
     * this is the main Controller
     *
     */

    namespace Acme;

    use Twig\Extra\String\StringExtension;


    class Controller
    {
        /*
         * recover data from database
         */
        public function model($model)
        {
            require_once 'app/models/'.$model.'.php';
            return new $model();
        }


        /*
         * Use Twig template for generate views
         */
        public function twig()
        {
            $loader = new \Twig\Loader\FilesystemLoader('app/views/templates');
            $loader->addPath('public/dist/css/','css');
            $loader->addPath('vendor/', 'vendor');
            $twig = new \Twig\Environment($loader, ['debug' => true]);
            $twig->addExtension(new StringExtension());
            $twig->addExtension(new \Twig\Extension\DebugExtension());
            return $twig;
        }
    }
