<?php
/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright �2015
 * All Rights Reserved
 * Date: 23/12/2015
 * Time: 6:25 PM
 *
 * Package Name: Makmesh Payroll (Kenya)
 * File Name: bank.php
 *
 */

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

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

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


$pdf->SetTitle("Makmesh Payroll (Kenya) - Bank Slip");

$month = (int)$_GET['m'];
$year = (int)$_GET['y'];

$obj = new Employee();

$employees = $obj->get_employees();

/**********************************************************************************************************************/
$pdf->AddPage();
$pdf->SetFont('', 'B', 12, '', true);
$width = array(10, 45, 25, 60, 25, 30, 50, 10);

$h = 10;
$pdf->Cell($width[0], $h, "", 1);
for ($i = 1; $i < 7; ++$i) {
    $pdf->Cell($width[$i], $h, "", "TB");
}
$pdf->Cell($width[7], $h, "", 1, 1);

$h = 15;
$date = date("d/m/Y");
$pdf->Cell($width[0], $h, "", 1);
$pdf->Cell($width[1] + $width[2], $h, "KCB LTD SALARIES TEMPLATE");
$pdf->Cell($width[3] + $width[4] + $width[5], $h, "   FROM COMPANY: Makmesh Payroll (Kenya).");
$pdf->Cell($width[6], $h, "DATE: $date");
$pdf->Cell($width[7], $h, "", 1, 1);

$pdf->Cell($width[0], $h, "", 1);
$pdf->Cell($width[1], $h, "EMPLOYEE NAME", 1);
$pdf->MultiCell($width[2], $h, "ACCOUNT NUMBER", 1, "C", false, 0);
$pdf->Cell($width[3], $h, "BANK NAME/BRANCH", 1);
$pdf->MultiCell($width[4], $h, "BRANCH CODE", 1, "C", false, 0);
$pdf->MultiCell($width[5], $h, "AMOUNT (KSH)", 1, "C", false, 0);
$pdf->MultiCell($width[6], $h, "REFERENCE (COMPANY NAME)", 1, "C", false, 0);
$pdf->Cell($width[7], $h, "", 1, 1);

$pdf->SetFont('', '', 10);

$employees = $obj->get_employees();
$h = 8;
$i = 0;
$total = 0;
$p = false;
foreach ($employees as $row) {
    ++$i;
    if ($a = $obj->get_pay($row->id, $month, $year)) {
        $p = true;
        $total += $a;
        $pdf->Cell($width[0], $h, $i, 1);
        $pdf->Cell($width[1], $h, "$row->fname $row->mname $row->lname", 1);
        $pdf->Cell($width[2], $h, $row->account_no, 1);
        $pdf->Cell($width[3], $h, $row->bank . "/" . $row->branch, 1);
        $pdf->Cell($width[4], $h, $row->code, 1);
        $pdf->Cell($width[5], $h, number_format($a, 2), 1, 0, "R");
        $pdf->Cell($width[6], $h, "Makmesh Payroll (Kenya)", 1);
        $pdf->Cell($width[7], $h, "", 1, 1);
    }
}

$pdf->SetFont("", "B");
$h = 15;
$pdf->Cell($width[0], $h, "", 1);
$pdf->Cell($width[1], $h, "TOTAL", 1, 0, "C");
$pdf->Cell($width[2], $h, "", 1);
$pdf->Cell($width[3], $h, "", 1);
$pdf->Cell($width[4], $h, "", 1);
$pdf->Cell($width[5], $h, number_format($total, 2), 1, 0, "R");
$pdf->Cell($width[6], $h, "", 1);
$pdf->Cell($width[7], $h, "", 1, 1);

$h = 10;
$voucher = (int)$_GET['v'];
$cheque = (int)$_GET['c'];
$dateObj = DateTime::createFromFormat("!m", $month);
$monthName = $dateObj->format('F');
$pdf->Cell($width[0], $h, "", 1);
$pdf->Cell($width[1] + $width[2] + $width[3] + $width[4] + $width[5] + $width[6], $h,
    "   MAPS Voucher No.: $voucher     Cheque No.: $cheque     Dated: $date    Month of Pay: $monthName    Year: $year", 1);
$pdf->Cell($width[7], $h, "", 1, 1);

$h = 15;
$pdf->Cell($width[0], $h, "", 1);
$pdf->Cell($width[1] + $width[2], $h, "   Authorized Signature:..................................", "B");
$pdf->Cell($width[3] + $width[4] + $width[5] + $width[6], $h, "Official Company Stamp:", "B");
$pdf->Cell($width[7], $h, "", 1, 1);

if ($p)
    $pdf->Output("Bank.pdf");
else echo "Please Prepare the Payroll first";