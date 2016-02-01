<?php
/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright ©2015
 * All Rights Reserved
 * Date: 6/20/2015
 * Time: 6:53 AM
 *
 * Package Name: skeleton
 * File Name: core.php
 *
 */

session_start();


$dir = __DIR__;

$directories = explode(DIRECTORY_SEPARATOR, $dir);

$base_dir = $directories[count($directories) - 2];

!defined("URL") ? define("URL", str_replace("/$base_dir/", "", $_SERVER["REQUEST_URI"])) : false;

if (isset($_SESSION['login'])) {
    // Log out user after 10 minutes of inactivity
    if (!isset($_SESSION['now'])) {
        $_SESSION['now'] = time();
    }
    $now = time();
    if (isset($_SESSION['now'])) {
        $S = $now - $_SESSION['now'];
        if (($S) > 1800) {
            unset($_SESSION['now'], $_SESSION['login']);
            $_SESSION['redirect'] = URL;
            $_SESSION['success'] = "You were logged out after 30 minutes of inactivity.";
            header("Location: " . $_SESSION['redirect']);
            exit;
        } else {
            $_SESSION['now'] = time();
        }
    }
}

function title()
{
    $title = $param = "";
    if (isset($_GET['page']))
        $param = $_GET['page'];
    $titles = file("title");
    $final = array();
    foreach ($titles as $k) {
        $temp = explode(",", $k);
        $final[$temp[0]] = $temp[1];
    }
    if (strlen(URL) > 0) {
        $file = scandir("pages");
        if (in_array($param . ".php", $file))
            $title = isset($final[$param]) ? $final[$param] : "";
        else $title = "Page Not Found";
    } else $title = "Home Page";
    echo "Makmesh Payroll (Kenya) | " . $title;
}

function __autoload($class_name)
{
    try {
        $class_file = 'inc/class/' . strtolower($class_name) . '.php';
        if (file_exists($class_file)) {
            require_once $class_file;
        } else {
            throw new Exception("Unable to load class $class_name in the file $class_file");
        }
    } catch (Exception $e) {
        echo "Exception: " . $e->getMessage();
    }
}

function route()
{
    $file = scandir("pages");
    if (isset($_GET['page'])) {
        $params = $_GET['page'];
        if (!in_array($params . ".php", $file) or $params[0] == "ajax")
            include "pages/notfound.php";
        else include "pages/" . $params . ".php";
    } else include 'pages/home.php';
}

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
if (isset($_SESSION['title'])) {
    $mstitle = $_SESSION['title'];
    unset($_SESSION['title']);
}

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $results = $action();
    if (isset($results[2]) and $results[2] !== "") {
        $_SESSION['message'] = $results[2];
    }
    if (isset($results[1]) and $results[1] !== "") {
        $_SESSION['title'] = $results[1];
    }

    header("Location: ?page=" . $results[0]);
    exit;
}

function sanitize_input($options = array())
{
    $input = array();
    $error = "";
    foreach ($_POST as $key => $val) {
        if ($val == "" and !in_array($key, $options)) {
            $key = ucfirst($key);
            $keys = explode('_', $key);
            $error = "Please enter " . ucfirst($keys[0]) . " ";
            $error .= isset($keys[1]) ? ucfirst($keys[1]) : "";
            break;
        }
        $input[$key] = trim(filter_var($_POST[$key], 513));
        $_SESSION['post'][$key] = $val;
    }
    if ($error != "") {
        return $error;
    }
    return $input;
}

function register()
{
    $input = sanitize_input(array('nssf_no', 'nhif_no'));
    if (is_string($input))
        return array('home', 'Employee Registration', $input);

    $obj = new Employee($input);
    return $obj->maint_emp();
}

function deduct()
{
    $input = sanitize_input();
    if (is_string($input))
        return array($_POST['url'], 'Deduction Notification', $input);

    $obj = new Employee();
    return $obj->save_deduction($input);
}

function credit()
{
    $input = sanitize_input();
    if (is_string($input))
        return array('payroll', 'Credit Notification', $input);

    $obj = new Employee();
    return $obj->add_credit($input);
}

function overtime()
{
    $input = sanitize_input(array('iko'));
    if (is_string($input))
        return array('overtime', 'Overtime Notification', $input);

    $obj = new Employee();
    return $obj->add_overtime($input);
}

function login()
{
    $username = filter_input(0, 'username', 513);
    $login = new Login($username, $_POST['password']);
    $login->user_login();
}

function recover()
{
    $email = filter_input(0, 'email', 274);
    $login = new Login();
    $login->recover_email($email);
}

function paye()
{
    $obj = new Employee();
    return $obj->update_paye($_POST);
}

function nhif()
{
    $obj = new Employee();
    return $obj->update_nhif($_POST);
}

function deduction()
{
    $obj = new Employee();
    return $obj->maint_deduction($_POST);
}

function allowance_list_add()
{
    $name = filter_input(0, 'name', 513);
    $obj = new Employee();
    return $obj->new_allowance($name);
}

function allowance()
{
    $url = $_POST['url'];
    $input = sanitize_input();
    if (is_string($input))
        return array($url, 'Allowance Notification', $input);

    $obj = new Employee();
    return $obj->save_allowance($input, $url);
}