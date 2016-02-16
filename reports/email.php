<?php
/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright �2015
 * All Rights Reserved
 * Date: 23/12/2015
 * Time: 11:25 AM
 *
 * Package Name: Makmesh Payroll (Kenya)
 * File Name: email.php
 *
 */

ini_set('max_execution_time', 3000);

session_start();

if (!isset($_SESSION['login'])) {
    $_SESSION['title'] = 'Login Notification';
    $_SESSION['message'] = 'Please login to view this page.';
    header('Location: ../login.php');
    exit;
}

function __autoload($class_name)
{
    $file = strtolower("../inc/class/" . $class_name . ".php");
    try {
        if (file_exists($file)) {
            require_once $file;
        } else {
            throw new Exception("Unable to load class $class_name in the file $file");
        }
    } catch (Exception $e) {
        echo "Exception: " . $e->getMessage();
    }
}

if (isset($_GET['m'], $_GET['y'])) {
    $obj = new Employee();
    $month = (int)$_GET['m'];
    $year = (int)$_GET['y'];
    if (!$obj->check_payroll($month, $year)) die("Please generate the payroll first.");

    $employees = $obj->get_employees();
    $count = count($employees);
    $other = $obj->other_deductions($year, $month);
    foreach ($employees as $employee) {
        $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(PDF_AUTHOR);

        $pdf->setPrintFooter(false);
        $pdf->setPrintHeader(false);

// set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 10);

// set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


        $pdf->setFontSubsetting(true);
        $pdf->SetFont('', 'B', 16, '', true);

        $pdf->SetTitle("Mapsurveys (K) LTD. - Payslip");


        $id = $employee->id;
        $basic = $employee->basic_pay;
        $pdf->AddPage();
        $pdf->setColor('fill', 220, 220, 220);
        $pdf->Cell(130, 0, 'SALARY VOUCHER', 0, 1, 'C', true);
        $pdf->SetFont('', 'B', 10);
        $pdf->Ln(9);

        $pdf->Rect(15, 40, 130, 150);

        $dateObj = DateTime::createFromFormat("!m", $month);
        $pdf->Cell(50, 0, "Mapsurveys (K) Limited");
        $pdf->Cell(40, 0, "Month of Pay:");
        $pdf->SetFont('');
        $pdf->Cell(40, 0, $monthName = $dateObj->format('F'));
        $pdf->SetFont('', 'B');
        $pdf->Ln(10);

        $pdf->Cell(15, 0, "Name:");
        $pdf->SetFont('');
        $pdf->Cell(35, 0, $employee->fname[0] . "." . $employee->mname[0] . ". " . $employee->lname);
        $pdf->SetFont('', 'B');
        $pdf->Cell(40, 0, "Date Paid:");
        $pdf->SetFont('');
        $pdf->Cell(40, 0, date("d-M-Y"));
        $pdf->Ln(6);

        $pdf->SetFont('', 'B');
        $pdf->Cell(15, 0, "PIN:");
        $pdf->SetFont('');
        $pdf->Cell(35, 0, $employee->pin);
        $pdf->Ln(6);
        $pdf->SetFont('', 'B');
        $pdf->Cell(15, 0, "ID:");
        $pdf->SetFont('');
        $pdf->Cell(35, 0, $employee->national_id, 0, 1);
        $pdf->Cell(130, 0, "", "T");
        $pdf->Ln(0.5);

        $w = 50;
        $w2 = 50;
        $w3 = 30;

        $pdf->Cell($w, 0, "EARNINGS");
        $pdf->Cell($w2, 0, "");
        $pdf->Cell($w3, 0, "KSH", 0, 1, "R", true);

        $pdf->SetFont('');
        $pdf->Cell($w, 5, "BASIC PAY");
        $pdf->Cell($w2, 5, "");
        $pdf->Cell($w3, 5, number_format($basic, 2), 0, 1, "R", true);

        $allowances = $obj->list_allowances($id, $month, $year);
        $gross = $basic;
        if ($allowances)
            foreach ($allowances as $allowance) {
                $gross += $allowance->amount;
                $pdf->Cell($w, 5, $allowance->allowance);
                $pdf->Cell($w2, 5, "");
                $pdf->Cell($w3, 5, number_format($allowance->amount, 2), 0, 1, "R", true);
            }

        $pdf->Cell(130, 0, "", "T");
        $pdf->Ln(0.5);

        $pdf->SetFont('', 'B');
        $pdf->Cell($w, 0, "GROSS PAY");
        $pdf->Cell($w2, 0, "");
        $pdf->Cell($w3, 0, number_format($gross, 2), 0, 1, "R", true);

        $pdf->Cell(130, 0, "", "T");
        $pdf->Ln(0.5);

        $h = 6;
        $pdf->Cell($w, $h, "DEDUCTIONS");
        $pdf->Cell($w2, $h, "");
        $pdf->Cell($w3, $h, "", 0, 1, "R", true);

        $pdf->SetFont('');

        $paye = $obj->get_tax($gross);
        $totalDeduction = $paye;

        $pdf->Cell($w, $h, "P.A.Y.E");
        $pdf->Cell($w2, $h, "");
        $pdf->Cell($w3, $h, "(" . number_format($paye, 2) . ")", 0, 1, "R", true);

        $deductions = $obj->get_deductions();

        $nssf = $deductions[1]->value;
        $totalDeduction += $nssf;
        $pdf->Cell($w, $h, "N.S.S.F");
        $pdf->Cell($w2, $h, "");
        $pdf->Cell($w3, $h, "(" . number_format($nssf, 2) . ")", 0, 1, "R", true);

        $nhif = $obj->nhif_value($basic);
        if ($basic > 100000)
            $nhif = 1700;
        $totalDeduction += $nhif;
        $pdf->Cell($w, $h, "N.H.I.F");
        $pdf->Cell($w2, $h, "");
        $pdf->Cell($w3, $h, "(" . number_format($nhif / 1, 2) . ")", 0, 1, "R", true);

        $ad = $obj->monthly_payroll('Advances', $id, $month, $year);
        $advances = "";
        if ($ad) $advances = "(" . number_format($ad / 1, 2) . ")";
        $pdf->Cell($w, $h, "ADVANCES");
        $pdf->Cell($w2, $h, "");
        $pdf->Cell($w3, $h, $advances, 0, 1, "R", true);

        $lp = "";
        $loan = $obj->monthly_payroll('Loan Repayment', $id, $month, $year);
        if ($loan)
            $lp = "(" . number_format($loan / 1, 2) . ")";
        $pdf->Cell($w, $h, "LOAN REPAYMENT");
        $pdf->Cell($w2, $h, "");
        $pdf->Cell($w3, $h, $lp, 0, 1, "R", true);

        $pension = $deductions[5]->value * $basic;
        $totalDeduction += $pension;
        $pdf->Cell($w, $h, "PENSION PLAN (5%)");
        $pdf->Cell($w2, $h, "");
        $pdf->Cell($w3, $h, "(" . number_format($pension, 2) . ")", 0, 1, "R", true);

        $he = "";
        $helb = $obj->monthly_payroll('HELB', $id, $month, $year);
        if ($helb)
            $he = "(" . number_format($helb / 1, 2) . ")";
        $pdf->Cell($w, $h, "HELB");
        $pdf->Cell($w2, $h, "");
        $pdf->Cell($w3, $h, $he, 0, 1, "R", true);

        $relief = $deductions[0]->value;
        $totalDeduction -= $relief;
        $pdf->Cell($w, $h, "MONTHLY RELIEF");
        $pdf->Cell($w2, $h, "");
        $pdf->Cell($w3, $h,  number_format($relief, 2), 0, 1, "R", true);

        if ($other)
            foreach ($other as $oth)
                if ($id == $oth->employee_id) {
                    $pdf->Cell($w, $h, strtoupper($oth->item));
                    $pdf->Cell($w2, $h, "");
                    $pdf->Cell($w3, $h, "(" . number_format($oth->value, 2) . ")", 0, 1, "R", true);
                }

        $pdf->Cell(130, 0, "", "T");
        $pdf->Ln(0.5);

        $totalDeduction += $obj->sum_payroll($id, $month, $year);

        $pdf->SetFont('', 'B');
        $pdf->Cell($w, $h, "TOTAL DEDUCTIONS", "B");
        $pdf->Cell($w2, $h, "", "B");
        $pdf->Cell($w3, $h, "(" . number_format($totalDeduction, 2) . ")", "B", 1, "R", true);

        $pdf->Cell($w, $h, "NET PAY", "B");
        $pdf->Cell($w2, $h, "", "B");
        $np = $gross - $totalDeduction;
        $pdf->Cell($w3, $h, number_format($np, 2), "B", 1, "R", true);


        $pdf->Cell($w, $h, "TOTAL POSTED", "B");
        $pdf->Cell($w2, $h, "", "B");
        $pdf->Cell($w3, $h, number_format(($np) / 1, 2), "B", 1, "R", true);

        $fname = "payroll/Payslip_00$id.pdf";
        if (is_file($fname))
            unlink($fname);

        $pdf->Output($fname, "F");
    }

    file_put_contents('sent.php', 0);

// Turn off output buffering
    ini_set('output_buffering', 'off');
// Turn off PHP output compression
    ini_set('zlib.output_compression', false);

//Flush (send) the output buffer and turn off output buffering
//ob_end_flush();
    while (@ob_end_flush()) ;

// Implicitly flush the buffer(s)
    ini_set('implicit_flush', true);
    ob_implicit_flush(true);

    header("Content-type: text/html");
    header('Cache-Control: no-cache');

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Map Surveys (K): Sending Payslip</title>
    </head>
    <body>
    Sending...
    <div style="width: 800px; background-color: #0029FF;">
        <div id='output'></div>
    </div>

    <script src="../assets/plugins/jquery-2.0.3.min.js"></script>
    <script>
        var i = <?php echo $count?>, j;

        window.setInterval(function () {
            $.post("sent.php", {id: 1}, function (data) {
                j = (data / i) * 100;
                $("#output").css({
                    'width': j + "%",
                    'background-color': '#68D72F',
                    'height': '20px',
                    'text-align': 'center'
                }).text(parseInt(j) + "%");
            });
        }, 1000);
    </script>
    <?php

    /**********************************************************************************************************************/

    require_once '../inc/class/PHPMailerAutoload.php';

    $mail = new PHPMailer;

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'mapsurveysk@gmail.com';
    $mail->Password = 'mapsurveys321';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('noreply@mapsurveys.co.ke', 'Mapsurveys (K) Limited');
    $mail->Subject = date("F") . ' Payslip';
    $mail->Body = 'Attached.';

    $i = 0;
    foreach ($employees as $row) {
        $mail->addAddress($row->email, "$row->fname $row->mname");
        $mail->addAttachment("payroll/Payslip_00$row->id.pdf", 'Payslip.pdf');

        if (!$mail->send()) {
            echo 'Error: Cannot send email.<br> Please check your internet connection and try again.<br><br>';
            file_put_contents('sent.php', $i);
            break;
        } else {
            ++$i;
            $mail->ClearAddresses();
            $mail->clearAttachments();
            file_put_contents('sent.php', $i);
        }
    }

    if ($i > 0)
        echo "$i out of $count emails sent.";

    //ob_flush();
    flush();
    ?>
    </body>
    </html>
<?php } ?>