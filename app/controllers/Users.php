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
        parent::model('User')->addUser($firstname, $lastname,$email, $pwd);
    }


    /*
     * check login and password
     */
    public function loginCheck()
    {
        session_start();
        $email = $_POST['email'];
        $pwd = $_POST['pwd'];
        $data = parent::model('User')->chekedAccount($email, $pwd);

        $twig = parent::twig();
        $nbNewUsers = self::nbUsersWaitingForValidation();
        $_SESSION['role'] = $data['role'];
        $_SESSION['email'] = $data['email'];
        $_SESSION['firstname'] = $data['firstname'];
        $_SESSION['lastname'] = $data['lastname'];
        $_SESSION['id'] = $data['id'];
        if($data['valid'] === true && intval($data['role']) == 1)
        {
            $_SESSION['active'] = true;
            echo $twig->render('admin\index.twig', array('nombre'=>$nbNewUsers,'role'=>$_SESSION['role'], 'active'=>$_SESSION['active']));
        }
        elseif ($data['valid'] === true && $data['role'] == '2' && intval($data['status']) == 1 && intval($data['suspend'])== 0)
        {
            echo $twig->render('user\home_user.twig', array());
        }
        elseif ($data['valid'] === false)
        {
            echo $twig->render('admin\login.twig', array('msg'=>'error'));
        }
        elseif ($data['status'] === false)
        {
            echo $twig->render('admin\login.twig', array('status'=>'error'));
        }
        else
        {
            echo $twig->render('admin\login.twig', array('suspend'=>'true'));
        }
    }

    /*
     * Destroy the active session
     */
    public function destroy()
    {
        session_start();
        $_SESSION = array();
        if (ini_get("session.use_cookies"))
        {$params = session_get_cookie_params();setcookie(session_name(), '', time() - 42000,$params["path"], $params["domain"],$params["secure"], $params["httponly"]);}
        session_destroy();
        $twig = parent::twig();
        echo $twig->render('admin\login.twig',array());
    }

    /*
     * Access to edit page
     */
    public function editAdmin()
    {
        session_start();
        $twig = parent::twig();
        echo $twig->render('admin\editAdmin.twig',
            array(
                'role'=>$_SESSION['role'],
                'firstname'=>$_SESSION['firstname'],
                'lastname'=>$_SESSION['lastname'],
                'email'=>$_SESSION['email'],
                'id'=>intval($_SESSION['id'])));
    }

    /*
     * Edit identity
     */
    public function editUser()
    {
        session_start();
        $id = $_SESSION['id'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        parent::model('User')->editUser($id, $firstname, $lastname);
        $_SESSION = array();
        if (ini_get("session.use_cookies"))
        {$params = session_get_cookie_params();setcookie(session_name(), '', time() - 42000,$params["path"], $params["domain"],$params["secure"], $params["httponly"]);}
        session_destroy();
        echo 'session morte';
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
    public function validateNewUsers()
    {
        session_start();
        $d = parent::model('User')->listWaitingUsers();
        $twig = parent::twig();
        echo $twig->render('admin\valideNewUsers.twig', array('list'=>$d,'role'=>$_SESSION['role']));
    }

    /*
     * Validate a user
     */
    public function validateUsers()
    {
        $tab = $_POST['tab'];
        parent::model('User')->changeStatus($tab);
    }
}

