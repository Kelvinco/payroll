<?php
/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright ©2016
 * All Rights Reserved
 * Date: 25/01/2016
 * Time: 7:59 PM
 *
 * Package Name: Makmesh Payroll (Kenya)
 * File Name: allowance.php
 *
 */


if (!isset($_SESSION['login']))
    echo <<<MENDE

<script>
    self.location = 'login.php';
</script>

MENDE;

$obj = new Employee();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $_SESSION['title'] = 'Deduction Notification.';

    $_SESSION['message'] = $obj->remove_allowance($id);

    echo <<<MENDE

<script>
    $(function () {
        self.location = '?page=allowance';
    });
</script>

MENDE;

}

$allowances = $obj->allowance_list();
?>

<div class="row">
    <div class="col-lg-12">
        <h1> Allowances</h1>
    </div>
</div>
<hr/>

<div class="row" style="text-align: right">
    <div class="col-lg-12" style="padding-right: 40px;">
        <a href="#" data-toggle='modal' data-target='#uiModal' class="btn btn-success btn-lg btn-round btn-grad">Add</a>
    </div>
</div>

<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Delete</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i = 0;
                    if ($allowances)
                        foreach ($allowances as $allowance) {
                            ++$i;
                            echo "<tr><td>$i</td><td>$allowance->name</td><td style='text-align: center'><a href='#'
                                    id='$allowance->id'  class='del'>Delete</a> </td></tr>";
                        }
                    else
                        echo '<tr><td></td><td>No records found</td><td></td></tr>';
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-12">
    <div class="modal fade" id="uiModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="H2">Add Allowance</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input class="form-control" name="name"/>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="action" value="allowance_list_add">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('.del').on('click', function (e) {
            e.preventDefault();
            var id = $(this).attr('id');
            if (confirm('Do you want to remove this record?'))
                self.location = '?page=allowance&id=' + id;
        });
    });
</script>