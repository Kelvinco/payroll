<?php
/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright �2015
 * All Rights Reserved
 * Date: 23/12/2015
 * Time: 12:32 AM
 *
 * Package Name: Makmesh Payroll (Kenya)
 * File Name: overtime.php
 *
 */

if (!isset($_SESSION['login']))
    echo <<<MENDE

<script>
    self.location = 'login.php';
</script>

MENDE;

$cyear = date("Y");
$cmonth = date("n");
$iko = "";

if (isset($_GET['y'], $_GET['m'])) {
    $cyear = (int)$_GET['y'];
    $cmonth = (int)$_GET['m'];
    $iko = "&y=$cyear&m=$cmonth";
}

$obj = new Employee();
if (isset($_GET['del'])) {
    $id = (int)$_GET['del'];
    $_SESSION['title'] = 'Delete Overtime!';
    if ($obj->rem_overtime($id))
        $_SESSION['message'] = 'The record was removed.';
    else
        $_SESSION['message'] = 'Something went wrong. The record was not removed.';

    echo <<<MENDE
<script>
    self.location = "?page=overtime$iko"
</script>
MENDE;

}


$members = $obj->overtime($cyear, $cmonth);
?>

<link href="assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet"/>
<div class="row">
    <div class="col-lg-12">
        <h1> Overtime</h1>
    </div>
</div>
<hr/>

<div class="row">
    <div class="col-lg-4">
        <label>Year</label>
        <select id="year" class="form-control" name="year">
            <?php
            for ($i = 2015; $i <= 2025; ++$i) {
                echo $cyear == $i ? "<option value='$i' selected>$i</option>" :
                    "<option value='$i'>$i</option>";
            }
            ?>
        </select>
    </div>
    <div class="col-lg-4">
        <label>Month</label>
        <select id="month" class="form-control" name="month">
            <?php
            for ($i = 1; $i <= 12; ++$i) {
                $dateObj = DateTime::createFromFormat("!m", $i);
                $monthName = $dateObj->format('F');
                echo $cmonth == $i ? "<option selected value='$i'>$monthName</option>" :
                    "<option value='$i'>$monthName</option>";
            }
            ?>
        </select>
    </div>
    <div class="col-lg-4">
        <a href="#" data-toggle='modal' data-target='#uiModal' class="btn btn-lg btn-success">Add Overtime</a>
    </div>
</div>

<div class="row" style="margin-top: 20px;">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Overtime
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <?php if ($members) { ?>
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Employee</th>
                                <th>Amount</th>
                                <th>Delete</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $i = 0;
                            $class = array('odd', 'even');
                            foreach ($members as $row) {
                                ++$i;
                                $j = ($i % 2);
                                $r = number_format($row->amount, 2);
                                echo "<tr $class[$j]><td>$i</td><td>$row->fname $row->mname</td>
                                        <td style='text-align: right'>$r</td>
                                        <td style='text-align: center'><a href='#' id='$row->id'
                                        class='del'>Delete</a> </td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    <?php } else echo 'No records found' ?>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="col-lg-12">
    <div class="modal fade" id="uiModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="H3">Add Overtime</h4>
                </div>
                <div class="modal-body">
                    <form role="form" method="post">

                        <div class="form-group">
                            <label>Employees</label>
                            <select name="employee_id" class="form-control">
                                <?php
                                $emp = $obj->get_employees();
                                if ($emp)
                                    foreach ($emp as $row)
                                        echo "<option value='$row->id'>$row->fname $row->mname $row->lname</option>";
                                else
                                    echo "<option>No Members</option>";
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Amount</label>
                            <input class="form-control" required type="number" name="amount"/>
                        </div>

                        <div class="form-group">
                            <label>Month</label>
                            <select class="form-control" name="month">
                                <?php
                                for ($i = 1; $i <= 12; ++$i) {
                                    $dateObj = DateTime::createFromFormat("!m", $i);
                                    $monthName = $dateObj->format('F');
                                    echo $cmonth == $i ? "<option selected value='$i'>$monthName</option>" :
                                        "<option value='$i'>$monthName</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Year</label>
                            <select class="form-control" name="year">
                                <?php
                                for ($i = 2015; $i <= 2025; ++$i) {
                                    echo $cyear == $i ? "<option value='$i' selected>$i</option>" :
                                        "<option value='$i'>$i</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <input type="hidden" name="action" value="overtime">
                            <input type="hidden" name="iko" value="<?php echo $iko ?>">
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        $(".del").on("click", function (e) {
            e.preventDefault();
            var id = $(this).attr("id");
            if (confirm("Do you want to remove the item?"))
                self.location = "?page=overtime&del=" + id + '<?php echo $iko?>';
        });

        $("#year, #month").on("change", function () {
            var m = $("#month").val();
            var y = $("#year").val();
            self.location = '?page=overtime&y=' + y + "&m=" + m;
        });
    });
</script>