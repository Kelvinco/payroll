<?php
/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright �2015
 * All Rights Reserved
 * Date: 22/12/2015
 * Time: 8:38 PM
 *
 * Package Name: Makmesh Payroll (Kenya)
 * File Name: home.php
 *
 */

if (!isset($_SESSION['login']))
    echo <<<MENDE

<script>
    self.location = 'login.php';
</script>

MENDE;


$obj = new Employee();
$members = $obj->get_employees();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $meso = $obj->exclude($id);

    $_SESSION['title'] = 'Employee Leave.';

    $_SESSION['message'] = $meso;

    echo <<<MENDE

<script>
    $(function () {
        self.location = './';
    });
</script>

MENDE;
}
?>

<div class="row">
    <div class="col-lg-12">
        <h1> Employees</h1>
    </div>
</div>
<hr/>

<div class="row" style="text-align: right">
    <div class="col-lg-12" style="padding-right: 40px;">
        <a href="?page=register" class="btn btn-success btn-lg btn-round btn-grad">Add</a>
    </div>
</div>

<link href="assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet"/>
<div style="height: 30px;"></div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Registered Employees
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>KRA PIN</th>
                                <th>Email</th>
                                <th>National ID</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $i = 0;
                            $class = array('odd', 'even');
                            if ($members)
                                foreach ($members as $row) {
                                    ++$i;
                                    $j = ($i % 2);
                                    $checked = '';
                                    if($row->exclude == 1)
                                        $checked = 'checked';
                                    echo "<tr $class[$j]><td>$i</td>
                                            <td><a href='?page=record&id=$row->id'
                                            title='Click to Update/Delete Employee'>$row->fname $row->mname $row->lname
                                            </a></td><td>$row->pin</td><td>$row->email</td><td>$row->national_id</td>
                                            <td><input $checked class='form-control leave' type='checkbox' lang='$row->id'></td>
                                            </tr>";
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--<script src="assets/plugins/dataTables/jquery.dataTables.js"></script>-->
<!--<script src="assets/plugins/dataTables/dataTables.bootstrap.js"></script>-->
<!--<script>-->
<!--    $(document).ready(function () {-->
<!--        $('#dataTables-example').dataTable();-->
<!--    });-->
<!--</script>-->


<script>
    $(function () {
        $('.leave').on('click', function () {
            var id = $(this).attr('lang');
            if (confirm('Do you want to exclude the employee from payroll?'))
                self.location = './?id=' + id;
        });
    });
</script>