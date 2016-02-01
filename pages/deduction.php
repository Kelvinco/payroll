<?php
/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright �2015
 * All Rights Reserved
 * Date: 22/12/2015
 * Time: 11:53 PM
 *
 * Package Name: Makmesh Payroll (Kenya)
 * File Name: deduction.php
 *
 */


if (!isset($_SESSION['login']))
    echo <<<MENDE

<script>
    self.location = 'login.php';
</script>

MENDE;

$obj = new Employee();
$deduction = $obj->get_deductions();
?>
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            Saved Deductions
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <form method="post">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Value</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i = 0;
                        foreach ($deduction as $row) {
                            ++$i;
                            $rows = "";
                            $value = "";
                            if ($row->value > 0) {
                                $value = "<input type='number' step='0.01' value='$row->value' class='form-control' name='value$i'>";
                                $rows = "<input type='hidden' name='row$i' value='$row->name'>";
                            } elseif ($row->auto == 0)
                                $value = "Manually Entered";
                            elseif ($row->auto == 1)
                                $value = "Automatically Calculated";
                            echo "<tr><td>$i</td><td>$rows $row->name</td><td>$value</td></tr>";
                        }

                        ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="text-align:right;"><input type="submit" name="submit" value="Save"
                                                                 class="btn btn-success"></td>
                        </tr>
                        </tbody>
                    </table>
                    <input type="hidden" name="action" value="deduction">
                </form>
            </div>
        </div>
    </div>
</div>