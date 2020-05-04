<?php

/***
 *
 * The router is going to parse url
 * for separate controller and method
 *
 */
namespace Acme;
class Router
{
    /*
    * Routing the appropriate method
    * for the controller
    */
    public function __construct()
    {
        $url = $this->getUrl();
        $controller = $url[0];

        if (!empty($controller))
        {
            if(file_exists('app/controllers/'.$controller.'.php'))
            {
                require_once 'app/controllers/'.$controller.'.php';
            }
            $method = $url[1];
            if(isset($method))
            {
                if (method_exists($controller,$method))
                {
                    call_user_func([$controller,$method],array_values($url));
                }
            }
        }
        else
        {
            $loader2 = new \Twig\Loader\FilesystemLoader('app/views/templates');
            $twig = new \Twig\Environment($loader2, []);
            $loader2->addPath('public/dist/css/','css');
            $loader2->addPath('public/dist/assets/img/','img');
            echo $twig->render('home\index.twig', ['name' => 'Fabien']);

        }
    }


    /*
      * collect controller and method
      * passed in url return in a array
      */
    public function getUrl()
    {
        $url = $_SERVER['QUERY_STRING'];
        $url = rtrim($url,'/');
        $url = explode('/', $url);
        return $url;
    }
}