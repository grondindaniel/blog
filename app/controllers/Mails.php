<?php

class Mails extends \Acme\Controller
{
    public function sendMail()
    {
        $name = strip_tags(htmlspecialchars($_POST['name']));
        $email = strip_tags(htmlspecialchars($_POST['email']));
        $phone = strip_tags(htmlspecialchars($_POST['phone']));
        $message = strip_tags(htmlspecialchars($_POST['message']));

//
        $subject = "Website Contact Form:  $name";
        $body = "You have received a new message from your website contact form.\n\n"."Here are the details:\n\nName: $name\n\nEmail: $email\n\nPhone: $phone\n\nMessage:\n$message";
        $header = "From: blog.proteiforme.fr\n"; // This is the email address the generated message will be from. We recommend using something like noreply@yourdomain.com.
        $header .= "Reply-To: $email";
        mail("lesbleachos@gmail.com",$subject,$body,$header);
        $twig = parent::twig();
        echo $twig->render('mail\mail.twig');

    }
}