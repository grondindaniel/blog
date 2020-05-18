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
         * method for access to login form
         */
        public function editUsers()
        {
            session_start();
            $id = $_SESSION['id'];
            $d = parent::model('User')->getUsers($id);
            $twig = parent::twig();
            echo $twig->render('admin\editUsers.twig',array('users'=>$d ,'role'=>$_SESSION['role']));
        }

        /*
         * method for access user for suspend him
        */
        public function userSuspend($user_id)
        {
            session_start();
            $d = parent::model('User')->getDetailsUserForSuspension($user_id);
            $twig = parent::twig();
            echo $twig->render('admin\editUserSuspend.twig',array('user'=>$d ,'role'=>$_SESSION['role']));
        }

        /*
         * method for  suspend an user
        */
        public function suspend()
        {
            session_start();
            $user_id = $_POST['user_id'];
            $suspend = $_POST['suspend'];
            parent::model('User')->suspend($user_id, $suspend);
            $twig = parent::twig();
            echo $twig->render('admin\editUsers.twig',array('role'=>$_SESSION['role'], 'active'=>$_SESSION['active'], 'msg'=>'ok'));
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
            $token =  parent::model('User')->addUser($firstname, $lastname,$email, $pwd);
            if($token !== 'AccountAlreadyExists')
            {
                $email = strip_tags(htmlspecialchars($_POST['email']));
                $subject = "Inscription au blog proteiforme";
                $message = "http://blog.proteiforme.fr/blog/Users/confirmMail/".$_POST['email'];
                $body = "Vous avez demandé une inscription au blog\n\n"."Voici le lien de validation:\n\nLien d'activation:\n$message". "\n\n Voici le code d'activation:\n\nCode:\n$token";
                $header = "From: blog.proteiforme.fr\n";
                $header .= "Reply-To: $email";
                mail($email,$subject,$body,$header);
                $twig = parent::twig();
                echo $twig->render('mail\mail.twig', array('confirmMail'=>'ok'));
            }
            else
            {
                $twig = parent::twig();
                echo $twig->render('mail\mail.twig', array('AccountAlreadyExists'=>'ok'));
            }

        }

        /*
        * check token and email
        */
        public function confirmMailMethod()
        {
            $email = $_POST['email'];
            $token = $_POST['token'];
            $token =  parent::model('User')->confirmMail($email, $token);
            $twig = parent::twig();
            echo $twig->render('mail\mail.twig', array('confirmCompte'=>$token));
        }

        /*
         * page to receave the token
         */
        public function confirmMail($d)
        {
            $email = $d[2];
            $twig = parent::twig();
            echo $twig->render('user\confirmMail.twig', array('email'=>$email));
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
            $nbNewComments = self::nbCommentsWaitingForValidation();
            $nbComments = self::nbComments();
            $totalUsers = self::nbUsers();
            $_SESSION['role'] = $data['role'];
            $_SESSION['email'] = $data['email'];
            $_SESSION['firstname'] = $data['firstname'];
            $_SESSION['lastname'] = $data['lastname'];
            $_SESSION['id'] = $data['id'];
            if($data['valid'] === true && intval($data['role']) == 1)
            {
                $_SESSION['active'] = true;
                echo $twig->render('admin\index.twig', array('nombre'=>$nbNewUsers,'role'=>$_SESSION['role'], 'active'=>$_SESSION['active'], 'nbNewComments'=>$nbNewComments, 'totalUsers'=>$totalUsers,'nbComments'=>$nbComments));
            }
            elseif ($data['valid'] === true && $data['role'] == '2' && intval($data['status_id']) == 1 && intval($data['suspend'])== 0)
            {
                $_SESSION['active'] = true;
                echo $twig->render('user\index.twig', array('role'=>$_SESSION['role'], 'active'=>$_SESSION['active']));
            }
            elseif ($data['valid'] === false && $data['role'] != '2')
            {
                session_destroy();
                echo $twig->render('admin\login.twig', array('msg'=>'noAccount'));
            }
            elseif ($data['valid'] === false)
            {
                session_destroy();
                echo $twig->render('admin\login.twig', array('msg'=>'error'));
            }
            elseif ($data['valid'] === true && $data['role'] == '2' && intval($data['status_id']) == 2 && intval($data['suspend'])== 0)
            {
                session_destroy();
                echo $twig->render('admin\login.twig', array('status'=>'error'));
            }
            else
            {
                session_destroy();
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
        public function editAccount()
        {
            session_start();
            $twig = parent::twig();
            echo $twig->render('admin\editAdmin.twig',
                array(
                    'role'=>$_SESSION['role'],
                    'firstname'=>$_SESSION['firstname'],
                    'lastname'=>$_SESSION['lastname'],
                    'email'=>$_SESSION['email'],
                    'active'=>$_SESSION['active'],
                    'id'=>intval($_SESSION['id'])));
        }

        /*
         * Access to change pwd page
         */
        public function changePwd()
        {
            session_start();
            $twig = parent::twig();
            echo $twig->render('admin\changePwd.twig', array('role'=>$_SESSION['role'],'active'=>$_SESSION['active']));
        }

        /*
 * Access to change pwd page
 */
        public function changePwdMailing($d)
        {
            $email = $d[2];
            $twig = parent::twig();
            echo $twig->render('admin\changePwdMailing.twig', array('email'=>$email));
        }

        /*
         * Access to change pwd page
         */
        public function updatePwd()
        {
            session_start();
            $id = $_SESSION['id'];
            $pwd = $_POST['pwd'];
            parent::model('User')->updatePwd($id, $pwd);
        }

        /*
         * Access to change pwd page
         */
        public function updatePwdMailing()
        {
            $email = $_POST['email'];
            $pwd = $_POST['pwd'];
            parent::model('User')->updatePwdMailing($email, $pwd);
            $twig = parent::twig();
            echo $twig->render('mail\mail.twig', array('msg'=>'changePwdByMail'));
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
         * check how many users
         * are waiting for registration
         */
        public function nbUsers()
        {
            $n = parent::model('User')->nbUsers();
            return $n;
        }

        /*
         * check how many comments
         * are waiting for registration
         */
        public function nbCommentsWaitingForValidation()
        {
            $n = parent::model('Comment')->listWaitingComments();
            return $n;
        }

        /*
         * check how many comments
         * are waiting for registration
         */
        public function nbComments()
        {
            $n = parent::model('Comment')->nbComments();
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
         * list all the users who
         * are waiting for registration
         */
        public function validateNewComments()
        {
            session_start();
            $d = parent::model('Comment')->listWaitingCommentsForValidation();
            $twig = parent::twig();
            echo $twig->render('admin\valideNewComments.twig', array('list'=>$d,'role'=>$_SESSION['role']));
        }

        /*
         * Validate a user
         */
        public function validateUsers()
        {
            session_start();
            $tab = $_POST['tab'];
            $nbNewUsers = self::nbUsersWaitingForValidation();
            $nbNewComments = self::nbCommentsWaitingForValidation();
            parent::model('User')->changeStatus($tab);
            $twig = parent::twig();
            echo $twig->render('admin\valideNewUsers.twig', array('nombre'=>$nbNewUsers,'role'=>$_SESSION['role'], 'active'=>$_SESSION['active'], 'nbNewComments'=>$nbNewComments,'msg'=>'ok'));
        }


        /*
         * Validate comments
         */
        public function validateComments()
        {
            session_start();
            $tab = $_POST['tab'];
            parent::model('Comment')->changeStatus($tab);
            $twig = parent::twig();
            echo $twig->render('admin\valideNewComments.twig', array('role'=>$_SESSION['role'],'msg'=>'ok'));
        }

        /*
         * Reactive an user
         */
        public function stopSuspend()
        {
            session_start();
            $suspend = $_POST['suspend'];
            $user_id = $_POST['user_id'];
            parent::model('User')->stopSuspend($suspend, $user_id);
            $twig = parent::twig();
            echo $twig->render('admin\editUsers.twig',array('role'=>$_SESSION['role'],'active'=>$_SESSION['active'],'reactive'=>'ok'));

        }


        /*
         * form for password forgot
         */
        public function passwordForgot()
        {
            $twig = parent::twig();
            echo $twig->render('user\passwordForgot.twig', array());
        }

        /*
         * user home page method
         */
        public function userHomePage()
        {
            session_start();
            $twig = parent::twig();
            echo $twig->render('user\index.twig', array('role'=>$_SESSION['role'], 'active'=>$_SESSION['active']));
        }

        /*
         * user home page method
         */
        public function mentions()
        {

            $twig = parent::twig();
            echo $twig->render('mentions.twig', array());
        }

        /*
         * Method to send a link for change password
         */
        public function mailPasswordForgot()
        {
            $email = $_POST['email'];
            $d = parent::model('User')->chekedEmail($email);
            if($d['email'] === $_POST['email'])
            {
                $to = $email;
                $subject = "Modification mot de passe";
                $message = "http://blog.proteiforme.fr/blog/Users/changePwdMailing/".$_POST['email'];
                $body = "Vous avez demandé le lien pour modifier votre mot de passe.\n\n"."Voici le lien:\n\nLien pour modifier le mot de passe:\n$message";
                $header = "From: blog.proteiforme.fr\n";
                $header .= "Reply-To: $email";
                mail($to,$subject,$body,$header);
                $twig = parent::twig();
                echo $twig->render('mail\mail.twig',array('msg'=>'mailSend'));

            }
            else
            {
                $twig = parent::twig();
                echo $twig->render('user\sendMessage.twig',array('message'=>'invalid','emailSend'=>$d['email']));
            }
        }

        public function indexHome()
        {
            session_start();
            $twig = parent::twig();
            $nbNewUsers = self::nbUsersWaitingForValidation();
            $nbNewComments = self::nbCommentsWaitingForValidation();
            $_SESSION['active'] = true;
            echo $twig->render('admin\index.twig', array('nombre'=>$nbNewUsers,'role'=>$_SESSION['role'], 'active'=>$_SESSION['active'], 'nbNewComments'=>$nbNewComments));

        }
    }

