<?php
/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright �2015
 * All Rights Reserved
 * Date: 24/12/2015
 * Time: 2:38 PM
 *
 * Package Name: Makmesh Payroll (Kenya)
 * File Name: p10.php
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

$pdf = new TCPDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);

$pdf->setPrintFooter(false);
$pdf->setPrintHeader(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, 3, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(5);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 10);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


$pdf->setFontSubsetting(true);
$pdf->SetFont('', 'BI', 10, '', true);

$obj = new Employee();
$company = $obj->get_company();
$pdf->SetTitle("$company->name - P10A");

$pdf->AddPage();
$year = (int)$_GET['y'];
$pdf->Image("../assets/img/kra.png", 70);
$pdf->Ln(30);

$pdf->Cell(0, 10, "P.10", 0, 1);

$pdf->SetFont('', 'B');

$pdf->Cell(153, 5, "EMPLOYER'S PIN", 0, 1, "R");

$pdf->Cell(110, 5, "");
$pdf->SetFont('');

for ($i = 0; $i < 11; ++$i)
    $pdf->Cell(5, 5, $company->kra_pin[$i], 1, 0, "C");

$pdf->SetFont('', 'B');

$pdf->Ln(5);

$pdf->Cell(0, 10, "P.A.Y.E - EMPLOYER'S CERTIFICATE", 0, 1, "C");
$pdf->Cell(0, 5, "YEAR $year", 0, 1, "C");

$pdf->Cell(0, 5, "To Senior Assistant Commissioner", 0, 1);
$pdf->Cell(0, 10, "................................................................", 0, 1);

$ntax2 = $obj->sum_tax(0, $year, true);
$ptx2 = "";
if ($ntax2)
    $ptx2 = number_format($ntax2 / 1, 2);
$pdf->SetFont('');
$pdf->MultiCell(0, 10, "We/I forward herewith ............... Tax Deduction Cards (P9A/P9B) showing the total tax
deducted (as listed on P.10A) amounting to Kshs. $ptx2", 0, "L");

$pdf->Cell(0, 10, "This total tax has been paid as follows:-", 0, 1);

$headers = array('MONTH', "PAYE TAX KSHS.", 'AUDIT TAX, INTEREST/PENALTY (KSHS.)', 'FRINGE BENEFIT TAX (KSHS.)',
    'DATE PAID PER (RECEIVING BANK)');

$width = array(30, 35, 40, 35, 40);
$i = 0;
$pdf->SetFont('', 'B');
foreach ($headers as $head) {
    $pdf->MultiCell($width[$i], 15, $head, 1, "L", false, 0);
    ++$i;
}
$pdf->Ln();

$pdf->SetFont('');
$months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October',
    'November', 'December');

$h = 7;
$i = 0;
foreach ($months as $month) {
    ++$i;
    $ntax = $obj->sum_tax($i, $year);
    $ptx = "";
    if ($ntax)
        $ptx = number_format($ntax / 1, 2);
    $pdf->Cell($width[0], $h, strtoupper($month), 1);
    $pdf->Cell($width[1], $h, $ptx, 1, 0, 'R');
    $pdf->Cell($width[2], $h, "0.00", 1, 0, 'R');
    $pdf->Cell($width[3], $h, "0.00", 1, 0, 'R');
    $pdf->Cell($width[4], $h, "", 1, 1);
}

$pdf->Cell($width[0], $h, "TOTAL TAX SHS");
$pdf->Cell($width[1], $h, $ptx2, 1, 0, 'R');
$pdf->Cell($width[2], $h, "0.00", 1, 0, 'R');
$pdf->Cell($width[3], $h, "0.00", 1, 0, 'R');
$pdf->Cell($width[4], $h, "", 0, 1);

$pdf->Ln(5);

$h = 7;
$pdf->Cell(0, $h, 'NOTE:-', 0, 1);
$pdf->MultiCell(0, $h, "(1) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Attach photostat copies of <strong>ALL the Pay-In Credit
Slips (P11s)</strong> for the year.", 0, "L", false, 1, '', '', true, 0, true);
$pdf->MultiCell(0, $h, "(4) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Complete this form in tripicate sending the top two copies w
ith the enclosures to your <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>
Income Tax Office not later than 28<sup>th</sup>February</strong>", 0, "L", false, 1, '', '', true, 0, true);
$pdf->Cell(0, $h, '(5)       Provide statistical information required by the Central Bureau of Statistics.');

$pdf->Ln(7);
$h = 8;
$pdf->Cell(0, $h, "We/I certify that the particulars entered above are correct.", 0, 1);

$pdf->SetFont('', 'B');
$pdf->Cell(50, $h, "NAME OF EMPLOYER:");
$pdf->SetFont('');
$pdf->Cell(50, $h, $company->name, 0, 1);

$pdf->SetFont('', 'B');
$pdf->Cell(50, $h, "ADDRESS");
$pdf->SetFont('');
$pdf->Cell(50, $h, $company->box_address, 0, 1);

$pdf->SetFont('', 'B');
$pdf->Cell(50, $h, "SIGNATURE");
$pdf->SetFont('');
$pdf->Cell(50, $h, "..................................", 0, 1);

$pdf->SetFont('', 'B');
$pdf->Cell(50, $h, "DATE");
$pdf->SetFont('');
$pdf->Cell(50, $h, date("d-m-Y"), 0, 1);

$pdf->Output("P10.pdf");