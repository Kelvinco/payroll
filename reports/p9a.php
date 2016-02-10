<?php
/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <wainaina.kelvin@gmail.com>
 * Copyright �2015
 * All Rights Reserved
 * Date: 02/01/2016
 * Time: 6:24 PM
 *
 * Package Name: Map Surveys (K) Ltd.
 * File Name: p9a.php
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
$pdf->SetMargins(10, 3, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(0);

// set auto page breaks
$pdf->SetAutoPageBreak(false);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


$pdf->setFontSubsetting(true);
$pdf->SetFont('', 'B', 10, '', true);

$pdf->SetTitle("Mapsurveys (K) LTD. - P9A");

$year = (int)$_GET['y'];

$obj = new Employee();
$employees = $obj->get_employees();
foreach ($employees as $emp) {
    $id = $emp->id;
    $basic = $emp->basic_pay;

    $pdf->AddPage();
    $year = (int)$_GET['y'];
    $pdf->Image("../assets/img/kra2.png", 100);
    $pdf->Ln(26);

    $pdf->Cell(10, 5, "P9A");
    $pdf->Cell(150, 5, "TAX DEDUCTION CARD YEAR $year", 0, 1, "R");

    $pdf->SetFont('', '', 10);

    $pdf->Cell(170, 5, 'Employer\'s Name:                 MAPSURVEYS (K) LIMITED');
    $pdf->Cell(50, 5, 'Employer\'s PIN:');

    $pin = "P051107674F";
    for ($i = 0; $i < 11; ++$i)
        $pdf->Cell(5, 5, $pin[$i], 1, 0, "C");

    $pdf->Ln(5);

    $pdf->Cell(170, 5, 'Employee\'s Main Name:       ' . $emp->lname);
    $pdf->Ln(5);

    $pdf->Cell(170, 5, "Employee's Other Names:     $emp->fname $emp->lname");
    $pdf->Cell(50, 5, 'Employee\'s PIN:');

    for ($i = 0; $i < strlen($emp->pin); ++$i)
        $pdf->Cell(5, 5, $emp->pin[$i], 1, 0, "C");

    $pdf->Ln(7);

    $headers = array('MONTH', 'Basic Salary Kshs.', "Benefits - Non - Cash Kshs.", 'Value of Quarters Kshs.',
        'Total Gross Pay <br> Kshs.', 'Defined Contribution Retirement Scheme <br> Kshs.', 'Owner - Occupied Interest Kshs.',
        'Retirement Contribution & Owner Occupied Interest Kshs.', 'Chargeable Pay <br>Kshs.', 'Tax Charged Kshs.',
        'Personal Relief + Insurance Relief <br>Kshs.', 'PAYE Tax (J-K) <br>Kshs.');

    $width = array(20, 20, 17, 17, 20, 51, 20, 35, 20, 20, 20, 20, 20, 20);

    $headers2 = array('', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L');
    $h = 23;
    $i = 0;
    $pdf->SetFont('', '', 10);
    foreach ($headers as $head) {
        $pdf->MultiCell($width[$i], $h, $head, 1, 'C', false, 0, '', '', true, 0, true);
        ++$i;
    }
    $pdf->Ln();

    $h = 7;
    $i = 0;
    foreach ($headers2 as $head) {
        $pdf->MultiCell($width[$i], $h, $head, "TRL", 'C', false, 0);
        ++$i;
    }

    $pdf->Ln();
    $i = 0;
    $h = 10;
    for ($i = 0; $i < 14; $i++) {
        if ($i == 5)
            $pdf->MultiCell(17, $h, "E1 30% of A", 1, 'C', false, 0);
        elseif ($i == 6)
            $pdf->MultiCell(17, $h, "E2 Actual", 1, 'C', false, 0);
        elseif ($i == 7)
            $pdf->MultiCell(17, $h, "E3 Fixed", 1, 'C', false, 0);
        elseif ($i == 8)
            $pdf->MultiCell($width[$i], $h, "Amount of Interest", "BR", 'C', false, 0);
        elseif ($i == 9)
            $pdf->MultiCell($width[7], $h, "The lowest of E added to F", "BR", 'C', false, 0);
        else
            $pdf->MultiCell($width[$i], $h, "", "BRL", 'C', false, 0);
    }

    $months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October',
        'November', 'December', 'TOTALS');
    $pdf->Ln();

    $h = 5;
    $j = 0;

    /**********************************************************************************************************************/
    $deduction = $obj->get_deductions();
    $data = array("", '0.00', '0.00', "", "",
        '', '', '0.00', "0.00", "",
        '', "", '');

    $pdf->SetFont('', '', 8);
    $mende = 1;

    $a = $b = $e2 = $e3 = $jT = $k = $chargable = $totalT = $totalA = 0;
    for ($i = 0; $i < count($headers) + 1; ++$i) {

        if ($i == 12)
            $h = 7;
        else
            $h = 5;
        $pdf->SetFont('', 'B', 10);
        $pdf->Cell($width[$j], $h, $months[$i], 1);
        $month = $i + 1;
        $allowance = $obj->get_allowance($id, $month, $year);
        $totalA += $allowance;
        ++$j;
        $pdf->SetFont('', '', 8);
        $actual = $obj->get_actual($mende, $year, $id);
        $multiply = $obj->get_multiplier($year, $id);
        $gross = $basic;
        if ($allowance)
            $gross += $allowance;

        foreach ($data as $hh) {
            if ($actual) {
                $row = $hh;
                $ded = $deduction[5]->value * $basic;

                $k = $deduction[0]->value * $multiply;
                $jT = $obj->sum_taxes($id, $year)->actual_tax;
                $a = $basic * $multiply;
                $e3 = 20000 * $multiply;
                $e2 = $ded * $multiply;
                if ($allowance)
                    $b = $a + $allowance;

                if ($j == 2) {
                    if ($allowance)
                        $row = number_format($allowance / 1, 2);
                    if ($i == 12)
                        $row = number_format($totalA / 1, 2);
                } elseif ($j == 1) {
                    $row = number_format($basic, 2);
                    if ($i == 12)
                        $row = number_format($a, 2);
                } elseif ($j == 4) {
                    $row = number_format($gross / 1, 2);
                    if ($i == 12)
                        $row = number_format($b, 2);
                } elseif ($j == 5) {
                    $row = number_format($basic * .3, 2);
                    if ($i == 12)
                        $row = number_format($a * .3, 2);
                } elseif ($j == 6 || $j == 9) {
                    $row = number_format($ded, 2);
                    if ($i == 12)
                        $row = number_format($e2, 2);
                } elseif ($j == 7) {
                    $row = "20,000.00";
                    if ($i == 12)
                        $row = number_format($e3, 2);
                } elseif ($j == 10) {
                    $row = number_format(($gross - $ded) / 1, 2);
                    if ($i == 12) {
                        $chargable = number_format($b - $e2, 2);
                        $row = $chargable;
                    }
                } elseif ($j == 11) {
                    $row = number_format($actual / 1, 2);
                    if ($i == 12)
                        $row = number_format($jT / 1, 2);
                } elseif ($j == 12) {
                    $row = number_format($deduction[0]->value, 2);
                    if ($i == 12)
                        $row = number_format($k, 2);
                } elseif ($j == 13) {
                    $row = number_format(($actual - $deduction[0]->value) / 1, 2);
                    if ($i == 12) {
                        $totalT = number_format(($jT - $k) / 1, 2);
                        $row = $totalT;
                    }
                }
            } else
                $row = "";
            if ($j == 5)
                $pdf->Cell(17, $h, $row, 1, 0, 'R');
            elseif ($j == 6)
                $pdf->Cell(17, $h, $row, 1, 0, 'R');
            elseif ($j == 7)
                $pdf->Cell(17, $h, $row, 1, 0, 'R');
            elseif ($j == 9)
                $pdf->Cell($width[7], $h, $row, 1, 0, 'R');
            else
                $pdf->Cell($width[$j], $h, $row, 1, 0, 'R');
            ++$j;
        }
        ++$mende;
        if ($mende == 13)
            $mende = 1;
        $j = 0;
        $pdf->Ln();
    }

    $h = 0;
    $pdf->SetFont('', 'B', 9);
    $pdf->Cell(200, $h, "TOTAL TAX (COL. L) KSHS. $totalT", 0, 1, "R");
    $pdf->SetFont('');

    $width = 160;
    $pdf->Cell($width, $h, 'To be completed by Employer at the end of year', 0, 1);
    $pdf->SetFont('', 'B');
    $pdf->Cell($width, 10, "TOTAL CHARGEABLE PAY (COL. H) KSHS. $chargable", 0, 1);
    $pdf->SetFont('');

    $pdf->Cell($width, $h, "IMPORTANT");

    $pdf->SetFont('');
    $width2 = 120;
    $pdf->MultiCell($width2, $h, "Attach &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(i) Photostat copy of interest
                    certificate and statement of account from the <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Financial Institution.", 0, 'L', false, 1, '', '', true, 0, true);

    $pdf->MultiCell($width, $h, "1. Use P9A &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (a) For all liable employees and where
                director/employee received <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;Benefits
                in addition to cash emoluments.", 0, 'L', false, 0, '', '', true, 0, true);

    $pdf->Cell($width2, 8, "                    (ii) The DECLARATION duly signed by the employee.", 0, 1);

    $pdf->Cell($width, $h, "                         (b) Where an employee is eligible to deduction on owner occupier interest.");

    $pdf->SetFont('', "B");

    $pdf->Cell($width2, $h, "NAMES OF FINANCIAL INSTITUTION ADVANCING MORTGAGE LOAN", 0, 1);

    $pdf->SetFont('');

    $pdf->Cell($width, $h, "2. (a) Allowable interest in respect of any month must not exceed Kshs. 12,500/= or Kshs. 150,000 per year.");

    $pdf->SetFont('', "B");

    $pdf->Cell($width2, $h, "________________________________________________________", 0, 1);

    $pdf->Cell($width, $h, "");

    $pdf->Cell($width2, $h, "L R NO. OF OWNER OCCUPIED PROPERTY:.........................................", 0, 1);

    $pdf->Cell($width, $h, "            (See back of this card for further information required by the Department.)");
    $pdf->Cell($width2, $h, "DATE OF OCCUPATION OF HOUSE:.........................................................");
}

$pdf->Output("P9A.pdf");