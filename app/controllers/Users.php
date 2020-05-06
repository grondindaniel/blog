<?php



class Users extends Acme\Controller
{
    /*
     * method for access to login form
     */
    public function login()
    {
        $twig = parent::twig();
        echo $twig->render('admin\login.twig',array());
    }

    /*
    *
    * register method
    * for access the page
    */
    public function register()
    {
        $twig = parent::twig();
        echo $twig->render('user\register.twig', array());
    }

    /*
    * sign in a new user
    */
    public function addUser()
    {
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $pwd = $_POST['pwd'];
        $data = parent::model('User')->addUser($firstname, $lastname,$email, $pwd);
    }


    /*
     * check login and password
     */
    public function loginCheck()
    {
        $email = $_POST['email'];
        $pwd = $_POST['pwd'];
        $data = parent::model('User')->chekedAccount($email, $pwd);
        $twig = parent::twig();
        $nbNewUsers = self::nbUsersWaitingForValidation();
        if($data['valid'] == true && intval($data['role']) == 1)
        {
            session_start();
            $_SESSION['level']= 'high';
            $level = $_SESSION['level'];
            $twig->addGlobal('email', $email);
            echo $twig->render('admin\index.twig', array('nombre'=>$nbNewUsers, 'level'=>$level));

        }
        elseif ($data['valid'] == true && $data['role'] == '2' && intval($data['status']) == 1 && intval($data['suspend'])== 0)
        {
            session_start();
            $_SESSION['level']= 'low';
            $level = $_SESSION['level'];
            echo $twig->render('user\home_user.twig', array('level'=>$level));

        }
        elseif ($data['valid'] == false)
        {
            echo $twig->render('admin\login.twig', array('msg'=>'error'));
        }
        elseif ($data['status'] == false)
        {
            echo $twig->render('admin\login.twig', array('status'=>'error'));
        }
        else
        {
            echo $twig->render('admin\login.twig', array('suspend'=>'true'));
        }
    }



    /*
     * check how many users
     * are waiting for registration
     */
    public function nbUsersWaitingForValidation()
    {
        $n = parent::model('User')->waitingForValidation();
        return $n;
    }

    /*
     * list all the users who
     * are waiting for registration
     */
    public function validate_new_users()
    {
        $d = parent::model('User')->listWaitingUsers();
        $twig = parent::twig();
        echo $twig->render('admin\validate_new_users.twig', array('list'=>$d));
    }

    /*
     * Validate a user
     */
    public function validate_users()
    {
        $tab = $_POST['tab'];
        $d = parent::model('User')->changeStatus($tab);

    }
}