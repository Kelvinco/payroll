<?php
/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright �2015
 * All Rights Reserved
 * Date: 05/01/2016
 * Time: 5:09 PM
 *
 * Package Name: Makmesh Payroll (Kenya)
 * File Name: login.php
 *
 */

require_once 'inc/core.php';
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
}
$obj = new Employee();
$company = $obj->get_company();
?>
<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en"> <!--<![endif]-->

<!-- BEGIN HEAD -->
<head>
    <meta charset="UTF-8"/>
    <title><?php echo $company->name ?> | Login</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <!--[if IE]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <![endif]-->
    <!-- GLOBAL STYLES -->
    <!-- PAGE LEVEL STYLES -->
    <link rel="stylesheet" href="assets/plugins/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="assets/css/login.css"/>
    <link href="assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet"/>
    <!-- END PAGE LEVEL STYLES -->
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="assets/js/html5shiv.js"></script>
    <script src="assets/js/respond.min.js"></script>
    <![endif]-->
</head>
<!-- END HEAD -->

<!-- BEGIN BODY -->
<body>

<!-- PAGE CONTENT -->
<div class="container">
    <div class="text-center">
        <?php echo $company->name?>
    </div>
    <div class="tab-content">
        <div id="login" class="tab-pane active">
            <form method="post" class="form-signin">
                <p class="text-muted text-center btn-block btn btn-primary btn-rect">
                    Enter your username and password
                </p>
                <input type="text" required name="username" placeholder="Username or Email" class="form-control"/>
                <input type="password" name="password" required placeholder="Password" class="form-control"/>
                <input type="hidden" name="action" value="login">
                <button class="btn text-muted text-center btn-danger" type="submit">Sign in</button>
            </form>
        </div>
        <div id="forgot" class="tab-pane">
            <form method="post" class="form-signin">
                <p class="text-muted text-center btn-block btn btn-primary btn-rect">Enter your valid e-mail</p>
                <input type="email" name="email" required="required" placeholder="Your E-mail" class="form-control"/>
                <br/>
                <input type="hidden" name="action" value="recover">
                <button class="btn text-muted text-center btn-success" type="submit">Recover Password</button>
            </form>
        </div>
    </div>
    <div class="text-center">
        <ul class="list-inline">
            <li><a class="text-muted" href="#login" data-toggle="tab">Login</a></li>
            <li><a class="text-muted" href="#forgot" data-toggle="tab">Forgot Password</a></li>
        </ul>
    </div>
</div>

<!--END PAGE CONTENT -->

<!-- PAGE LEVEL SCRIPTS -->
<script src="assets/plugins/jquery-2.0.3.min.js"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/js/login.js"></script>
<script src="assets/plugins/gritter/js/jquery.gritter.js"></script>
<!--END PAGE LEVEL SCRIPTS -->
<script>
    <?php
    if(isset($message) and isset($mstitle)){
    ?>
    $.gritter.add({
        title: '<?php echo $mstitle?>',
        text: '<?php echo $message?>',
        sticky: false,
        time: ''
    });
    <?php
    }
    ?>
</script>
</body>
<!-- END BODY -->
</html>