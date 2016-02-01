<?php
/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright �2015
 * All Rights Reserved
 * Date: 23/12/2015
 * Time: 9:27 AM
 *
 * Package Name: Makmesh Payroll (Kenya)
 * File Name: view.php
 *
 */

if (!isset($_SESSION['login']))
    echo <<<MENDE

<script>
    self.location = 'login.php';
</script>

MENDE;

$obj = new Employee();

if (isset($_GET['id'], $_GET['m'], $_GET['y'])) {
    $id = (int)$_GET['id'];
    $_SESSION['title'] = "Deduction Notification.";
    if ($obj->remove_payroll($id))
        $_SESSION['message'] = "The record was deleted.";
    else
        $_SESSION['message'] = "The record was not deleted due to an internal error. Please try again later.";

    $year = (int)$_GET['y'];
    $month = (int)$_GET['m'];
    echo <<<MENDE
<script>
   $(function () {
            self.location = "?page=view&y=$year&m=$month";
        });
</script>
MENDE;

}

if (isset($_GET['m'], $_GET['y'])) {

    $year = (int)$_GET['y'];
    $month = (int)$_GET['m'];
    $deduction = $obj->saved_deductions($year, $month);

    $dateObj = DateTime::createFromFormat("!m", $month);
    $monthName = $dateObj->format('F');
    ?>
    <form method="post">
        <div class="row">
            <div class="col-lg-12">
                <h1>Deductions : <?php echo $monthName . ", " . $year ?></h1>
            </div>
        </div>
        <hr/>

        <div class="row" style="text-align: right">
            <div class="col-lg-12" style="padding-right: 40px;">
                <a href="#" data-toggle='modal' data-target='#uiModal' title="Add Deduction"
                   class="btn btn-success btn-lg btn-round btn-grad">Add Deduction</a>
            </div>
        </div>

        <div class="col-lg-12">
            <?php if ($deduction) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Saved Deductions
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Deduction</th>
                                    <th style='text-align:right;'>Value</th>
                                    <th style='text-align:right;'>Delete</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $i = 0;
                                foreach ($deduction as $row) {
                                    ++$i;
                                    $v = number_format($row->value, 2);
                                    echo "<tr><td>$i</td><td>$row->fname $row->mname $row->lname</td><td>$row->item</td>
                                            <td style='text-align:right;'>$v</td>
                                            <td style='text-align:right;'>
                                            <a href='#' id='$row->id' class='del'>Delete</a> </td></tr>";
                                }

                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } else echo "No Records" ?>
        </div>

        <div class="col-lg-12">
            <div class="modal fade" id="uiModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="H3">Add Deduction</h4>
                        </div>
                        <div class="modal-body">

                            <div class="form-group">
                                <label>Employee</label>
                                <select name="employee_id" class="form-control">
                                    <?php
                                    $deduction = $obj->get_employees();
                                    foreach ($deduction as $row)
                                        echo "<option value='$row->id'>$row->fname $row->mname $row->lname</option>";
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Deduction</label>
                                <select name="item" class="form-control">
                                    <?php
                                    $deduction = $obj->filter_deductions();
                                    foreach ($deduction as $row)
                                        echo "<option value='$row->name'>$row->name</option>";
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Value</label>
                                <input required class="form-control" type="number" name="value"/>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <input type="hidden" name="action" value="deduct">
                                <input type="hidden" name="year" value="<?php echo $year ?>">
                                <input type="hidden" name="month" value="<?php echo $month ?>">
                                <input type="hidden" name="url" value="<?php echo "view&y=$year&m=$month" ?>">
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(function () {
            $(".del").on("click", function () {
                    var id = $(this).attr("id");
                    if (confirm("Are you sure you want to remove the record."))
                        if (id.length > 0)
                            self.location = "?page=view&y=<?php echo $year . "&m=". $month?>&id=" + id;
                }
            );
        });
    </script>
<?php }