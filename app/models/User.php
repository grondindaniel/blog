<?php

/*
 * Access to user table for
 * add, edit or check a user
 */

class User
{
    public $host = 'localhost';
    public $dbname = 'blog2';
    public $user = 'root';
    public $pwd = '';
    public $bdd;

    public function __construct()
    {
        $req = $this->bdd = new pdo('mysql:host=' . $this->host . ';dbname=' . $this->dbname, $this->user, $this->pwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        return $req;
    }

    /*
     * Check email with password
     */
    public function chekedAccount($email, $pwd)
    {

        $req = $this->bdd->prepare(
            'SELECT user.id, user.pwd, user.firstname, user.lastname, user.role_id,user.status_id, status.status, email.email, email.suspend FROM user 
                      INNER JOIN email on user.id = email.user_id
                      INNER JOIN status on status.id = user.status_id
                      WHERE email =:email;');
        $req->bindValue(':email', $email, PDO::PARAM_STR);
        $req->execute();
        $check = $req->fetch();
        $pwd_hash = $check['pwd'];
        $valid = password_verify($pwd, $pwd_hash);
        $d = array(
            'valid' => $valid,
            'role' => $check['role_id'],
            'status_id'=>$check['status_id'],
            'status' => $check['status'],
            'suspend' => $check['suspend'],
            'email' => $check['email'],
            'firstname'=>$check['firstname'],
            'lastname'=>$check['lastname'],
            'id'=>$check['id']
        );
        return $d;
    }

    /*
     * Check email
     */
    public function chekedEmail($email)
    {
        $req = $this->bdd->prepare(
            'SELECT email.email FROM email WHERE email =:email');
        $req->bindValue(':email', $email, PDO::PARAM_STR);
        $req->execute();
        $check = $req->fetch();
        $d = array(
            'email' => $check['email']
        );
        return $d;
    }

    /*
     * Edit identity
     */
    public function editUser($id, $firstname, $lastname)
    {
        $id = intval($_SESSION['id']);
        $email = $_POST['email'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $req = $this->bdd->prepare("UPDATE user SET id=:id, firstname=:firstname, lastname=:lastname WHERE id=:id");
        $req->bindValue(':id',$id,PDO::PARAM_INT);
        $req->execute(array(
            ':id'=>$id,
            ':firstname'=>$firstname,
            ':lastname'=>$lastname
        ));
        $req = $this->bdd->prepare("UPDATE email SET user_id=:id, email=:email WHERE user_id=:id");
        $req->bindValue(':user_id',$id,PDO::PARAM_INT);
        $req->execute(array(
            ':id'=>$id,
            ':email'=>$email
        ));
    }

    /*
     * Change pwd
     */
    public function updatePwd($id, $pwd)
    {
        $id = intval($id);
        $pwd = $_POST['pwd'];
        $pwd = password_hash($pwd, PASSWORD_DEFAULT);
        $req = $this->bdd->prepare("UPDATE user SET id=:id, pwd=:pwd WHERE id=:id");
        $req->bindValue(':id',$id,PDO::PARAM_INT);
        $req->execute(array(
            ':id'=>$id,
            ':pwd'=>$pwd
        ));
    }


    /*
     * register new user
     */
    public function addUser($firstname, $lastname, $pwd, $email)
    {
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $pwd = $_POST['pwd'];
        $pwd = password_hash($pwd, PASSWORD_DEFAULT);
        $email = $_POST['email'];
        $q = $this->bdd->prepare("SELECT id FROM status WHERE status = 1");
        $q->execute();
        $status_id = $q->fetch();
        $status_id = intval($status_id['id']);
        $suspend = 0;

        $query = $this->bdd->prepare("SELECT id FROM role WHERE role = 'user'");
        $query->execute();
        $role_id = $query->fetch();
        $role_id = intval($role_id['id']);

        $req = $this->bdd->prepare("INSERT INTO user (role_id, firstname, lastname, pwd, status_id) VALUES (:role_id ,:firstname, :lastname, :pwd, :status_id)");
        $req->execute(array(
            ':role_id'=>$role_id,
            ':firstname'=>$firstname,
            ':lastname'=>$lastname,
            ':pwd'=>$pwd,
            ':status_id'=>$status_id
        ));

        $user_id = $this->bdd->lastInsertId();
        $req = $this->bdd->prepare("INSERT INTO email (user_id, email, suspend) VALUES (:user_id, :email, :suspend)");
        $req->execute(array(
            ':user_id'=>$user_id,
            ':email'=>$email,
            ':suspend'=>$suspend
        ));
    }

    /*
     * Count nb account waiting for validation
     */
    public function waitingForValidation()
    {
        $req = $this->bdd->prepare("SELECT id FROM user WHERE status_id != 1");
        $req->execute();
        $nb = $req->fetchAll();
        $nb = count($nb);
        return $nb;
    }

    /*
     * liste users who are waiting validation
     */
    public function listWaitingUsers()
    {
        $req = $this->bdd->prepare("SELECT user.firstname, user.lastname, user.id FROM user WHERE status_id = 2");
        $req->execute();
        $d = $req->fetchAll();
        return $d;
    }

    /*
     * Valide new status
     */
    public function changeStatus($tab)
    {
        foreach ($tab as $id)
        {
            $req = $this->bdd->prepare("UPDATE user set status_id = 1 where id =:id");
            $req->bindValue(':id',$id,PDO::PARAM_INT);
            $req->execute();
        }
    }

    /*
     * get data for suspend an user
     */
    public function getUsers($id)
    {
        $req = $this->bdd->prepare("SELECT * FROM email WHERE user_id != :id ");
        $req->bindValue(':id',$id,PDO::PARAM_INT);
        $req->execute();
        return $req->fetchAll();
    }

    /*
     * get data for suspend an user
     */
    public function getDetailsUserForSuspension($id)
    {
        $id = $id[2];
        $req = $this->bdd->prepare("SELECT user.id, user.firstname, user.lastname, user.status_id,email.user_id, email.email, email.suspend, comment.comment, comment.comment_date
 FROM user 
 INNER JOIN email ON 
 email.user_id = user.id
 LEFT JOIN comment ON 
 comment.user_id = user.id
 WHERE user.id = :id ");
        $req->bindValue(':id',$id,PDO::PARAM_INT);
        $req->execute();
        return $req->fetch();
    }

    /*
     * suspend an user
     */
    public function suspend($user_id, $suspend)
    {
        $user_id = intval($user_id);
        $req = $this->bdd->prepare("UPDATE email SET suspend=:suspend WHERE user_id=:user_id");
        $req->bindValue(':user_id',$user_id,PDO::PARAM_INT);
        $req->execute(array(
            ':user_id'=>$user_id,
            ':suspend'=>$suspend
        ));
    }
}

