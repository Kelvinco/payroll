<?php
/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright �2015
 * All Rights Reserved
 * Date: 23/12/2015
 * Time: 12:23 AM
 *
 * Package Name: Makmesh Payroll (Kenya)
 * File Name: paye.php
 *
 */

if (!isset($_SESSION['login']))
    echo <<<MENDE

<script>
    self.location = 'login.php';
</script>

MENDE;

$obj = new Employee();
$nhif = $obj->get_paye();
?>

<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            Current Income Tax Rates
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <form method="post">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Percentage</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i = 0;
                        foreach ($nhif as $row) {
                            ++$i;
                            $rows = "<input type='hidden' name='row$i' value='$i'>";
                            $min = "<input type='number' class='form-control' name='min$i' value='$row->min'>";
                            $max = "<input type='number' class='form-control' name='max$i' value='$row->max'>";
                            $rate = $row->rate * 100;
                            $deduction = "<input type='number' class='form-control' name='rate$i' value='$rate'>";
                            if ($i == 5)
                                $max = "<input type='hidden' name='max$'>--";
                            echo "<tr><td>$i</td><td>$rows $min</td><td>$max</td><td>$deduction</td></tr>";
                        }

                        ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="text-align:right;"><input type="submit" name="submit" value="Save"
                                                                 class="btn btn-success"></td>
                        </tr>
                        </tbody>
                    </table>
                    <input type="hidden" name="action" value="paye">
                </form>
            </div>
        </div>
    </div>
</div>