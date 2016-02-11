<?php
/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <wainaina.kelvin@gmail.com>
 * Copyright ©2016
 * All Rights Reserved
 * Date: 11 Feb 2016
 * Time: 14:51
 *
 * Package Name: payroll
 * File Name: profile.php
 *
 */


if (!isset($_SESSION['login']))
    echo <<<MENDE

<script>
    self.location = 'login.php';
</script>

MENDE;

$obj = new Employee();
$nhif = $obj->get_company();
?>

<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            Company's Profile
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <form method="post">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Value</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Name</td>
                            <td><input required type="text" class="form-control" name="name"
                                       value="<?php echo $nhif->name ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>KRA PIN</td>
                            <td><input required type="text" class="form-control" name="kra_pin"
                                       value="<?php echo $nhif->kra_pin ?>"></td>
                        </tr>
                        <tr>
                            <td>Name</td>
                            <td><input required type="text" class="form-control" name="box_address"
                                       value="<?php echo $nhif->box_address ?>"></td>
                        </tr>

                        <tr>
                            <td></td>
                            <td style="text-align:right;">
                                <input type="hidden" name="action" value="profile">
                                <input type="hidden" name="id" value="<?php echo $nhif->id ?>">
                                <input type="submit" name="submit" value="Update" class="btn btn-success">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>