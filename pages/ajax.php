<?php
/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright �2015
 * All Rights Reserved
 * Date: 7/13/2015
 * Time: 10:01 AM
 *
 * Package Name: skeleton
 * File Name: ajax.php
 *
 */

session_start();


function __autoload($class_name)
{
    try {
        $class_file = '../inc/class/' . strtolower($class_name) . '.php';
        if (file_exists($class_file)) {
            require_once $class_file;
        } else {
            throw new Exception("Unable to load class $class_name in the file $class_file");
        }
    } catch (Exception $e) {
        echo "Exception: " . $e->getMessage();
    }
}

$obj = new Employee();
if (isset($_POST['overtime'])) {
    $id = (int)$_POST['overtime'];
    echo $obj->get_name($id);
}

if (isset($_POST['payroll'])) {
    $y = (int)$_POST['payroll'];
    $m = (int)$_POST['m'];
    if ($obj->check_payroll($m, $y))
        echo "iko";
    else
        echo "hakuna";
}