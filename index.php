<?php
/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright �2015
 * All Rights Reserved
 * Date: 22/12/2015
 * Time: 5:06 PM
 *
 * Package Name: Makmesh Payroll (Kenya)
 * File Name: index.php
 *
 */

require_once "inc/core.php";

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
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
    <title><?php title() ?> </title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <!--[if IE]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <![endif]-->
    <!-- GLOBAL STYLES -->
    <link rel="stylesheet" href="assets/plugins/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="assets/css/main.css"/>
    <link rel="stylesheet" href="assets/css/MoneAdmin.css"/>
    <link rel="stylesheet" href="assets/plugins/Font-Awesome/css/font-awesome.css"/>
    <!--END GLOBAL STYLES -->

    <script src="assets/plugins/jquery-2.0.3.min.js"></script>

    <link href="assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet"/>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="assets/js/html5shiv.js"></script>
    <script src="assets/js/respond.min.js"></script>
    <![endif]-->
</head>

<!-- END HEAD -->

<!-- BEGIN BODY -->
<body class="padTop53 ">

<!-- MAIN WRAPPER -->
<div id="wrap">

    <!-- HEADER SECTION -->
    <div id="top">

        <nav class="navbar navbar-inverse navbar-fixed-top " style="padding-top: 10px;">
            <a data-original-title="Show/Hide Menu" data-placement="bottom" data-tooltip="tooltip"
               class="accordion-toggle btn btn-primary btn-sm visible-xs" data-toggle="collapse" href="#menu"
               id="menu-toggle">
                <i class="icon-align-justify"></i>
            </a>
            <!-- LOGO SECTION -->
            <header class="navbar-header">
                <a href="./" class="navbar-brand">
                    <?php echo $company->name ?>
                </a>
            </header>

            <!--ADMIN SETTINGS SECTIONS -->
            <a class="navbar-right" href="login.php?logout"><img src="assets/img/logout.png" alt="Logout"></a>
            <!--END ADMIN SETTINGS -->
        </nav>

    </div>
    <!-- END HEADER SECTION -->


    <!-- MENU SECTION -->
    <div id="left">
        <div class="media user-media well-small">
            <a class="user-link" href="#">
                <img class="media-object img-thumbnail user-img" alt="User Picture" src="assets/img/user.bmp"/>
            </a>
            <br/>

            <div class="media-body">
                <h5 class="media-heading">Supervisor</h5>
                <ul class="list-unstyled user-info">
                    <li>
                        <a class="btn btn-success btn-xs btn-circle" style="width: 10px;height: 12px;"></a> Online
                    </li>

                </ul>
            </div>
            <br/>
        </div>

        <?php
        $payroll = $overtime = $deduction = $nhif = $paye = $employee = $allowance = $profile = "";
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            if ($page === 'payroll')
                $payroll = "active";
            if ($page === 'overtime')
                $overtime = 'active';
            if ($page === 'deduction')
                $deduction = 'active';
            if ($page === 'nhif')
                $nhif = 'active';
            if ($page === 'paye')
                $paye = 'active';
            if ($page === 'allowance')
                $allowance = 'active';
            if ($page === 'profile')
                $profile = 'active';
        } else $employee = 'active';
        ?>

        <ul id="menu" class="collapse">
            <li class="panel <?php echo $employee ?>">
                <a href="./">
                    <i class="icon-table"></i> Employees
                </a>
            </li>

            <li class="panel <?php echo $payroll ?>">
                <a href="?page=payroll">
                    <i class="icon-anchor"></i> Payroll
                </a>
            </li>

            <li class="panel <?php echo $allowance ?>">
                <a href="?page=allowance">
                    <i class="icon-anchor"></i> Allowance List
                </a>
            </li>

            <li class="panel <?php echo $overtime ?>">
                <a href="?page=overtime">
                    <i class="icon-dollar"></i> Overtime
                </a>
            </li>
            <li class="panel <?php echo $deduction ?>">
                <a href="?page=deduction">
                    <i class="icon-minus"></i> Deductions List
                </a>
            </li>
            <li class="panel <?php echo $nhif ?>">
                <a href="?page=nhif">
                    <i class="icon-heart"></i> N.H.I.F
                </a>
            </li>
            <li class="panel <?php echo $paye ?>">
                <a href="?page=paye">
                    <i class="icon-ticket"></i> Income Tax
                </a>
            </li>
            <li class="panel">
                <a href="#" id="p10a">
                    <i class="icon-file"></i> P.10A
                </a>
            </li>
            <li class="panel">
                <a href="#" id="p10">
                    <i class="icon-folder-open"></i> P.10
                </a>
            </li>

            <li class="panel">
                <a href="#" id="p9a">
                    <i class="icon-puzzle-piece"></i> P.9A
                </a>
            </li>

            <li class="panel <?php echo $profile ?>">
                <a href="?page=profile" id="p9a">
                    <i class="icon-puzzle-piece"></i> Company's Profile
                </a>
            </li>
        </ul>

    </div>
    <!--END MENU SECTION -->


    <!--PAGE CONTENT -->
    <div id="content">

        <div class="inner">
            <?php echo route() ?>
        </div>

    </div>
    <!--END PAGE CONTENT -->

</div>

<!--END MAIN WRAPPER -->

<!-- FOOTER -->
<div id="footer">
    <p>&copy; <?php echo $company->name . '&nbsp;' . date('Y') ?> &nbsp;</p>
</div>
<!--END FOOTER -->


<!-- GLOBAL SCRIPTS -->
<script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/plugins/modernizr-2.6.2-respond-1.1.0.min.js"></script>
<!-- END GLOBAL SCRIPTS -->

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
    $(function () {
        var _y = '<?php echo date('Y')?>';
        $("#p10a").on("click", function (e) {
            e.preventDefault();
            var year = parseInt(prompt("Enter Year", _y));
            if (year > 0)
                self.location = "reports/p10a.php?y=" + year;
        });
        $("#p10").on("click", function (e) {
            e.preventDefault();
            var year = parseInt(prompt("Enter Year", _y));
            if (year > 0)
                self.location = "reports/p10.php?y=" + year;
        });
        $("#p9a").on("click", function (e) {
            e.preventDefault();
            var year = parseInt(prompt("Enter Year", _y));
            if (year > 0)
                self.location = "reports/p9a.php?y=" + year;
        });
    });
</script>
</body>
<!-- END BODY -->
</html>