<?php
/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright �2015
 * All Rights Reserved
 * Date: 23/12/2015
 * Time: 12:14 AM
 *
 * Package Name: Makmesh Payroll (Kenya)
 * File Name: nhif.php
 *
 */

if (!isset($_SESSION['login']))
    echo <<<MENDE

<script>
    self.location = 'login.php';
</script>

MENDE;

$obj = new Employee();
$nhif = $obj->get_nhif();
?>

<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            Current N.H.I.F Rates
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <form method="post">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Min</th>
                            <th>Max</th>
                            <th>Deduction</th>
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
                            $deduction = "<input type='number' class='form-control' name='deduction$i' value='$row->deduction'>";
                            if ($i == 17) {
                                $i = "Any Amount Above";
                                $max = "<input type='hidden' name='max$i' value='200000'>";
                            }
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
                    <input type="hidden" name="action" value="nhif">
                </form>
            </div>
        </div>
    </div>
</div>