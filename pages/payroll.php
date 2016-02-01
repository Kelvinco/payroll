<?php
/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright �2015
 * All Rights Reserved
 * Date: 23/12/2015
 * Time: 8:39 AM
 *
 * Package Name: Makmesh Payroll (Kenya)
 * File Name: payroll.php
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

$currMonth = date("n");
$currYear = date("Y");

if (isset($_GET['y']))
    $currYear = (int)$_GET['y'];
if (isset($_GET['m']))
    $currMonth = (int)$_GET['m'];

?>
<form method="post">
    <div class="row">
        <div class="col-lg-12">
            <h1>Payroll Generation</h1>
        </div>
    </div>
    <hr/>

    <div class="row">
        <div class="col-lg-6 payroll">
            <div class="form-group">
                <label>Year</label>
                <select id="year" class="form-control" name="year">
                    <?php
                    for ($i = 2015; $i <= 2025; ++$i) {
                        echo $currYear == $i ? "<option value='$i' selected>$i</option>" :
                            "<option value='$i'>$i</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="col-lg-6 payroll">
            <div class="form-group">
                <label>Month</label>
                <select id="month" class="form-control" name="month">
                    <?php
                    for ($i = 1; $i <= 12; ++$i) {
                        $dateObj = DateTime::createFromFormat("!m", $i);
                        $monthName = $dateObj->format('F');
                        echo $currMonth == $i ? "<option selected value='$i'>$monthName</option>" :
                            "<option value='$i'>$monthName</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-2 payroll">
            <a href="?page=_allowance&y=<?php echo $currYear . "&m=" . $currMonth ?>" class="btn btn-success">View
                Allowances</a>
        </div>

        <div class="col-lg-2 payroll">
            <a href="?page=view&y=<?php echo $currYear . "&m=" . $currMonth ?>" class="btn btn-success">View
                Deductions</a>
        </div>

        <div class="col-lg-2 payroll">
            <a href="#" id="payroll" class="btn btn-success">
                Generate Payroll</a>
        </div>

        <div class="col-lg-2 payroll">
            <a href="#" data-toggle="modal" data-target="#formModal" class="btn btn-success">
                Bank Statement</a>
        </div>

        <div class="col-lg-2 payroll">
            <a href="#" id="payslip" class=" btn btn-success">Print
                Payslip</a>
        </div>

        <div class="col-lg-2 payroll">
            <a href="reports/email.php?y=<?php echo $currYear . "&m=" . $currMonth ?>" target="_blank"
               class="btn btn-success">Email Payslip</a>
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
                                <th>Name</th>
                                <th>Basic</th>
                                <th>Gross Pay</th>
                                <th>P.A.Y.E</th>
                                <th>Monthly Relief</th>
                                <th>N.S.S.F</th>
                                <th>N.H.I.F</th>
                                <th>Pension</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $i = 0;
                            $class = array('odd', 'even');
                            $duduction = $obj->get_deductions();
                            if ($members)
                                foreach ($members as $row) {
                                    ++$i;
                                    $j = ($i % 2);
                                    $pay = $row->basic_pay;
                                    $paye = number_format($obj->get_tax($pay), 2);
                                    $nssf = number_format($duduction[1]->value, 2);
                                    $nhif = number_format($obj->nhif_value($pay) / 1, 2);
                                    $relief = number_format($duduction[0]->value, 2);
                                    $pension = number_format($duduction[5]->value * $pay, 2);
                                    $pay = number_format($pay, 2);
                                    echo "<tr $class[$j]>
                                            <td><a href='#' class='overtime' data-toggle='modal' data-target='#uiModal'
                                            id='$row->id' title='Click to Add Other Deductions'>
                                            $row->fname $row->mname $row->lname</a></td>
                                            <td style='text-align: right'>$pay</td>
                                            <td style='text-align: right'>$pay</td>
                                            <td style='text-align: right'>$paye</td>
                                            <td style='text-align: right'>  $relief</td>
                                            <td style='text-align: right'>$nssf</td>
                                            <td style='text-align: right'>$nhif</td>
                                            <td style='text-align: right'>$pension</td>
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

    <div class="col-lg-12">
        <div class="modal fade" id="uiModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="H3">Add Deduction - <span id="mende">Loading...</span></h4>
                    </div>
                    <div class="modal-body">

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
                            <input type="hidden" name="employee_id" value="0" id="employee_id">
                            <input type="hidden" name="url" value="<?php echo "payroll&y=$currYear&m=$currMonth" ?>">
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>

<div class="col-lg-12">
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="H2">Add Voucher No. & Cheque No.</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Voucher No.</label>
                        <input class="form-control" type="number" id="voucher"/>
                    </div>

                    <div class="form-group">
                        <label>Cheque No.</label>
                        <input class="form-control" type="number" id="cheque"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" id="print" class="btn btn-primary">Print</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/plugins/dataTables/jquery.dataTables.js"></script>
<script src="assets/plugins/dataTables/dataTables.bootstrap.js"></script>
<script>
    $(document).ready(function () {
        $('#dataTables-example').dataTable();

        $(".overtime").on("click", function () {
            var id = $(this).attr("id");
            $("#employee_id").val(id);
            $.post("pages/ajax.php", {overtime: id}, function (data) {
                $("#mende").text(data);
            });
        });

        var m = '<?php echo $currMonth?>';
        var y = '<?php echo $currYear?>';

        $("#print").on("click", function (e) {
            e.preventDefault();
            var v = $("#voucher").val();
            var c = $("#cheque").val();

            $.post("pages/ajax.php", {payroll: y, m: m}, function (data) {
                if (data == 'iko')
                    if (v.length > 0 && c.length > 0)
                        self.location = "reports/bank.php?y=" + y + "&m=" + m + "&v=" + v + "&c=" + c;
                    else alert("Please enter Cheque/Voucher No");
                else
                    alert("Please generate the payroll first.");
            });
        });

        $("#payslip").on("click", function (e) {
            e.preventDefault();
            $.post("pages/ajax.php", {payroll: y, m: m}, function (data) {
                if (data == "iko")
                    self.location = 'reports/payslip.php?y=' + y + '&m=' + m;
                else
                    alert("Please generate the payroll first.");
            });
        });

        $("#payroll").on("click", function (e) {
            e.preventDefault();
            var sheet = prompt("Enter Payroll Sheet Number")
            if (sheet.length > 0)
                self.location = "reports/print.php?m=" + m + "&y=" + y + "&s=" + sheet;
            else
                alert("Please enter a value.");
        });

        $("#year, #month").on("change", function () {
            var y = $("#year").val();
            var m = $("#month").val();
            self.location = "?page=payroll&y=" + y + "&m=" + m;
        });
    });
</script>