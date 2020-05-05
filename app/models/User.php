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
    }

    public function chekedAccount($email, $pwd)
    {

        $req = $this->bdd->prepare(
            'SELECT user.pwd, user.role_id, status.status, email.email, email.suspend FROM user 
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
            'status' => $check['status'],
            'suspend' => $check['suspend']
        );
        return $d;
    }

    public function addUser($firstname, $lastname, $pwd, $email)
    {

        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $pwd = $_POST['pwd'];
        $pwd = password_hash($pwd, PASSWORD_DEFAULT);
        $email = $_POST['email'];
        $q = $this->bdd->prepare("SELECT id FROM status WHERE status = 0");
        $q->execute();
        $status_id = $q->fetch();
        $status_id = intval($status_id['id']);

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
        $req = $this->bdd->prepare("INSERT INTO email (user_id, email) VALUES (:user_id, :email)");
        $req->execute(array(
            ':user_id'=>$user_id,
            ':email'=>$email

        ));

    }

    public function waitingForValidation()
    {
        $req = $this->bdd->prepare("SELECT id FROM user WHERE status_id != 1");
        $req->execute();
        $nb = $req->fetchAll();
        $nb = count($nb);
        return $nb;

    }

    public function listWaitingUsers()
    {
        $req = $this->bdd->prepare("SELECT user.firstname, user.lastname, user.id FROM user WHERE status_id = 2");
        $req->execute();
        $d = $req->fetchAll();
        return $d;
    }

    public function changeStatus($tab)
    {
        foreach ($tab as $id)
        {
            $req = $this->bdd->prepare("UPDATE user set status_id = 1 where id =:id");
            $req->bindValue(':id',$id,PDO::PARAM_INT);
            $req->execute();
        }

    }

}