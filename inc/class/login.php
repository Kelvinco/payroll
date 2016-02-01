<?php

/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright �2015
 * All Rights Reserved
 * Date: 05/01/2016
 * Time: 5:03 PM
 *
 * Package Name: Makmesh Payroll (Kenya)
 * File Name: Login.php
 *
 */
class Login extends OurDB
{

    protected $username,
        $password;

    public function __construct($username = '', $passowrd = '')
    {
        if (trim($username) and trim($passowrd)) {
            $this->username = $username;
            $this->password = $passowrd;
        }
    }

    public function user_login()
    {
        $user = self::get_rows("SELECT id, password FROM admin WHERE username = '$this->username' OR
                                  email = '$this->username'");
        $_SESSION['title'] = "Login Notification";
        $_SESSION['message'] = "Wrong username/password combination.";
        if (!$user) {
            header("Location: login.php");
            exit;
        }
        $user = array_shift($user);
        $hashed = self::hash_password();
        if ($user->password != $hashed) {
            header("Location: login.php");
            exit;
        }

        $_SESSION['message'] = "Welcome, $this->username";

        $_SESSION['login'] = array(
            'id' => $user->id, 'username' => $this->username
        );
        header("Location: ./?page=payroll");
        exit;
    }

    protected function hash_password($password = '')
    {
        $pswd = $this->password;
        if (trim($password)) $pswd = $password;
        return hash_hmac('whirlpool', $pswd, '?A?3!?H9??]W:?');
    }

    public function recover_email($email)
    {
        $check = self::get_val("SELECT id FROM admin WHERE email = '$email'");
        $_SESSION['title'] = "Password Recovery!";
        if (!$check) {
            $_SESSION['message'] = 'The email was not found in the system.';
            header("Location: login.php");
            exit;
        }
        $chr = 1234567890;
        $chr = str_shuffle($chr);
        $pswrd = substr($chr, 0, 6);
        $hash = $this->hash_password($pswrd);

        require_once 'PHPMailerAutoload.php';
        $mail = new PHPMailer;

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your_username@gmail.com';
        $mail->Password = 'your_password';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your_username@gmail.com', 'Your Name');
        $mail->Subject = 'Password Recovery';
        $mail->Body = 'Your new password is : ' . $pswrd;

        $mail->addAddress($email);

        if (!$mail->send()) {
            $_SESSION['message'] = "Cannot recover password. Please check your internet connection";
        } else {
            self::execute_query("UPDATE admin SET password = '$hash' WHERE email = '$email'");
            $_SESSION['message'] = "A new Password was sent to your email.";
        }

        header("Location: login.php");
        exit;
    }
}