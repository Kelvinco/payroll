<?php
/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright ©2016
 * All Rights Reserved
 * Date: 25/01/2016
 * Time: 8:35 PM
 *
 * Package Name: Makmesh Payroll (Kenya)
 * File Name: _allowance.php
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
    $_SESSION['title'] = 'Allowance Notification.';
    $_SESSION['message'] = $obj->del_allowance($id);

    $year = (int)$_GET['y'];
    $month = (int)$_GET['m'];
    echo <<<MENDE
<script>
   $(function () {
            self.location = "?page=_allowance&y=$year&m=$month";
        });
</script>
MENDE;

}

if (isset($_GET['m'], $_GET['y'])) {

    $year = (int)$_GET['y'];
    $month = (int)$_GET['m'];
    $allowances = $obj->saved_allowances($month, $year);

    $dateObj = DateTime::createFromFormat("!m", $month);
    $monthName = $dateObj->format('F');
    ?>
    <form method="post">
        <div class="row">
            <div class="col-lg-12">
                <h1>Allowances : <?php echo $monthName . ", " . $year ?></h1>
            </div>
        </div>
        <hr/>

        <div class="row" style="text-align: right">
            <div class="col-lg-12" style="padding-right: 40px;">
                <a href="#" data-toggle='modal' data-target='#uiModal' title="Add Allowance"
                   class="btn btn-success btn-lg btn-round btn-grad">Add Allowance</a>
            </div>
        </div>

        <div class="col-lg-12">
            <?php if ($allowances) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Saved Allowances
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Allowance</th>
                                    <th style='text-align:right;'>Value</th>
                                    <th style='text-align:right;'>Delete</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $i = 0;
                                foreach ($allowances as $allowance) {
                                    ++$i;
                                    $v = number_format($allowance->amount, 2);
                                    echo "<tr><td>$i</td><td>$allowance->fname $allowance->mname $allowance->lname</td>
                                            <td>$allowance->allowance</td>
                                            <td style='text-align:right;'>$v</td>
                                            <td style='text-align:right;'>
                                            <a href='#' id='$allowance->id' class='del'>Delete</a> </td></tr>";
                                }

                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } else echo 'No Records found.' ?>
        </div>

        <div class="col-lg-12">
            <div class="modal fade" id="uiModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="H3">Add Allowance</h4>
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
                                <label>Allowance</label>
                                <select name="allowance" class="form-control">
                                    <?php
                                    $allowance = $obj->allowance_list();
                                    foreach ($allowance as $row)
                                        echo "<option value='$row->name'>$row->name</option>";
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Amount</label>
                                <input required class="form-control" type="number" name="amount"/>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <input type="hidden" name="action" value="allowance">
                                <input type="hidden" name="year" value="<?php echo $year ?>">
                                <input type="hidden" name="month" value="<?php echo $month ?>">
                                <input type="hidden" name="url" value="<?php echo "_allowance&y=$year&m=$month" ?>">
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
                            self.location = "?page=_allowance&y=<?php echo $year . "&m=" . $month?>&id=" + id;
                }
            );
        });
    </script>
<?php }