<?php
/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright �2015
 * All Rights Reserved
 * Date: 22/12/2015
 * Time: 11:44 PM
 *
 * Package Name: Makmesh Payroll (Kenya)
 * File Name: record.php
 *
 */

if (!isset($_SESSION['login']))
    echo <<<MENDE

<script>
    self.location = 'login.php';
</script>

MENDE;

$obj = new Employee();
if (isset($_GET['mem'])) {
    $mem = (int)$_GET['mem'];
    $_SESSION['title'] = "Employee Notification.";
    if ($obj->rem_employee($mem))
        $_SESSION['message'] = "The employee was deleted.";
    else
        $_SESSION['message'] = "Something went wrong. The member was not removed.";

    echo <<<MENDE

<script>
    self.location = "./"
</script>
MENDE;

}
$id = (int)$_GET['id'];
$input = $obj->get_employee($id);
?>
<div style="height: 20px;"></div>

    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <header>
                    <div class="icons"><i class="icon-th-large"></i></div>
                    <h5>Employee Maintenance</h5>

                    <div class="toolbar">
                        <ul class="nav">
                            <li>
                                <div class="btn-group">
                                    <a class="accordion-toggle btn btn-xs minimize-box" data-toggle="collapse"
                                       href="#collapseOne">
                                        <i class="icon-chevron-up"></i>
                                    </a>
                                    <button class="btn btn-xs btn-danger close-box del">
                                        <i class="icon-remove"></i>
                                    </button>
                                </div>
                            </li>
                        </ul>
                    </div>

                </header>
                <div id="collapseOne" class="accordion-body collapse in body">
                    <form method="post" class="form-horizontal" id="block-validate">

                        <div class="form-group">
                            <label class="control-label col-lg-5">First Name</label>

                            <div class="col-lg-5">
                                <input type="text" required name="fname" class="form-control"
                                       value="<?php echo $input->fname ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-5">Middle Name</label>

                            <div class="col-lg-5">
                                <input type="text" required name="mname" class="form-control"
                                       value="<?php echo $input->mname ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-5">Last Name</label>

                            <div class="col-lg-5">
                                <input type="text" required name="lname" class="form-control"
                                       value="<?php echo $input->lname ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-5">KRA PIN</label>

                            <div class="col-lg-5">
                                <input type="text" required name="pin" class="form-control"
                                       value="<?php echo $input->pin ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-5">National ID</label>

                            <div class="col-lg-5">
                                <input type="number" required name="national_id" class="form-control"
                                       value="<?php echo $input->national_id ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-5">Email</label>

                            <div class="col-lg-5">
                                <input type="email" required name="email" class="form-control"
                                       value="<?php echo $input->email ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-5">N.S.S.F NO</label>

                            <div class="col-lg-5">
                                <input type="number" name="nssf_no" class="form-control" placeholder="Optional"
                                       value="<?php echo $input->nssf_no ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-5">N.H.I.F NO</label>

                            <div class="col-lg-5">
                                <input type="number" name="nhif_no" class="form-control" placeholder="Optional"
                                       value="<?php echo $input->nhif_no ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-5">Bank Name</label>

                            <div class="col-lg-5">
                                <input type="text" required name="bank" class="form-control"
                                       value="<?php echo $input->bank ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-5">Bank Branch</label>

                            <div class="col-lg-5">
                                <input type="text" required name="branch" class="form-control"
                                       value="<?php echo $input->branch ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-5">Bank Code</label>

                            <div class="col-lg-5">
                                <input type="number" required name="code" class="form-control"
                                       value="<?php echo $input->code ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-5">Account NO</label>

                            <div class="col-lg-5">
                                <input type="number" required name="account_no" class="form-control"
                                       value="<?php echo $input->account_no ?>"/>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="control-label col-lg-5">Basic Pay</label>

                            <div class="col-lg-5">
                                <input type="number" required name="basic_pay" class="form-control"
                                       value="<?php echo $input->basic_pay ?>"/>
                            </div>
                        </div>

                        <div class="form-actions no-margin-bottom" style="text-align:center;">
                            <input type="hidden" name="id" value="<?php echo $id ?>">
                            <input type="hidden" name="action" value="register">
                            <input type="submit" value="Update" class="btn btn-primary btn-lg "/>
                            <a href="#" class="btn btn-danger btn-lg del">Delete</a>
                            <a href="./" class="btn btn-lg btn-warning">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $(".del").on("click", function (e) {
                e.preventDefault();
                if (confirm("Do you want to remove the Employee?"))
                    self.location = "?page=record&mem=<?php echo $id?>";
            });
        });
    </script>
<?php
if (isset($_GET['id'])) {
}

