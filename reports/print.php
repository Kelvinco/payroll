<?php
/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright �$w15
 * All Rights Reserved
 * Date: 23/12/$w15
 * Time: 11:24 AM
 *
 * Package Name: Makmesh Payroll (Kenya)
 * File Name: print.php
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
$pdf->SetFont('', 'B', 16, '', true);

$obj = new Employee();
$company = $obj->get_company();
$pdf->SetTitle($company->name . ' - Payroll');

$pdf->AddPage();

$pdf->Cell(0, 0, $company->name, 0, 1);

$year = (int)$_GET['y'];
$month = (int)$_GET['m'];
$sheet = $_GET['s'];
$pdf->Ln(5);
$pdf->SetFont('', 'B', 12, '', true);
$pdf->Cell(100, 0, 'PAYROLL');
$pdf->Cell(100, 0, "YEAR $year");
$pdf->Cell(100, 0, "SHEET $sheet", 0, 1);


$employees = $obj->get_employees();

$rows = array('MONTH', 'DETAILS', 'EARNINGS', 'BASIC', 'ALLOWANCES', 'OVERTIME', '', 'a. GROSS PAY', 'T/CALCULATION',
    'b. Round to pounds', 'c. 15% Hse.', '15% Sub. Hse.', 'd. C/Pay (b+c)', 'e.Tax Charged', 'f. Monthly Relief',
    'Insurance Relief', '', 'DEDUCTIONS', 'h. Tax Deducted', 'N.S.S.F', 'N.H.I.F', 'PENSION PLAN', '', 'STANDING ORDER',
    'ADVANCES', 'LOAN REP/HELB', 'MISC DED/REF', 'PENSION REFUND', '', 'T/DEDUCTIONS', 'NET PAY', 'NAME');

$i = 0;
$deductions = $obj->get_deductions();
$pdf->setColor('fill', 200, 200, 200);
$pdf->SetFont('', '', 10);
$netPay = array();
$dateObj = DateTime::createFromFormat('!m', $month);
$netSum = $total_deduction = 0;
foreach ($rows as $rw) {
    if ($i == 0 || $i == 7 || $i == 13 || $i == 29)
        $fill = true;
    else
        $fill = false;

    if ($i == 31 || $i == 30 || $i == 0 || $i == 2 || $i == 7 || $i == 8 || $i == 17 || $i == 29)
        $pdf->SetFont('', 'B');

    if ($i == 6)
        $pdf->Cell(35, 0, $rw, 1, 0, 'R');
    else
        $pdf->Cell(35, 0, $rw, 1, 0, '', $fill);

    $w = 20;
    $monthName = $dateObj->format('M');
    $net = 0;
    $id = 0;
    foreach ($employees as $rec) {
        $id = $rec->id;
        $gross = $rec->basic_pay;

        $pdf->SetFont('', '', 10);
        if ($i == 0)
            $pdf->Cell($w, 0, $monthName, 1, 0, '', $fill);

        if ($i == 1 || $i == 2 || $i == 6 || $i == 8 || $i == 10 || $i == 11 || $i == 16 || $i == 17
            || $i == 22 || $i == 28
        )
            $pdf->Cell($w, 0, '', 1);

        if ($i == 3)
            $pdf->Cell($w, 0, number_format($gross, 2), 1, 0, 'R');

        $t_allowance = $obj->get_allowance($id, $month, $year);
        if ($t_allowance)
            $gross += $t_allowance;

        $allowance = '';
        if ($t_allowance)
            $allowance = number_format($t_allowance / 1, 2);
        if ($i == 4)
            $pdf->Cell($w, 0, $allowance, 1, 0, 'R');

        if ($i == 5)
            if ($ot = $obj->get_overtime($rec->id, $year, $month)) {
                $gross += $ot;
                $pdf->Cell($w, 0, number_format($ot / 1, 2), 1, 0, 'R');
            } else
                $pdf->Cell($w, 0, '', 1);

        if ($i == 7)
            $pdf->Cell($w, 0, number_format($gross / 1, 2), 1, 0, 'R', $fill);

        $pound = floor($gross / 20);
        if ($i == 9 || $i == 12)
            $pdf->Cell($w, 0, number_format($pound, 2), 1, 0, 'R');

        $paye = $obj->get_tax($gross);
        if ($i == 13)
            $pdf->Cell($w, 0, number_format($paye, 2), 1, 0, 'R', $fill);

        $relief = $deductions[0]->value;
        if ($i == 14)
            $pdf->Cell($w, 0, '(' . number_format($relief, 2) . ')', 1, 0, 'R');

        $insurance = $deductions[3]->value;
        if ($i == 15)
            $pdf->Cell($w, 0, number_format($insurance, 2), 1, 0, 'R');

        $tax = $paye - $relief - (int)$insurance;

        if ($i == 12)
            $netSum += $tax;

        if ($i == 18)
            $pdf->Cell($w, 0, number_format($tax, 2), 1, 0, 'R');

        $nssf = $deductions[1]->value;
        if ($i == 19)
            $pdf->Cell($w, 0, number_format($nssf, 2), 1, 0, 'R');

        $nhif = $obj->nhif_value($rec->basic_pay);
        if ($rec->basic_pay > 100000)
            $nhif = 1700;
        if ($i == 20)
            $pdf->Cell($w, 0, number_format((float)$nhif, 2), 1, 0, 'R');

        $pension = $deductions[5]->value * $rec->basic_pay;
        if ($i == 21)
            $pdf->Cell($w, 0, number_format($pension, 2), 1, 0, 'R');

        if ($i == 23)
            if ($standing = $obj->emp_deduction('Standing Order', $rec->id, $month, $year))
                $pdf->Cell($w, 0, number_format($standing / 1, 2), 1, 0, 'R');
            else
                $pdf->Cell($w, 0, '', 1);

        if ($i == 24)
            if ($standing = $obj->emp_deduction('Advances', $rec->id, $month, $year))
                $pdf->Cell($w, 0, number_format($standing / 1, 2), 1, 0, 'R');
            else
                $pdf->Cell($w, 0, '', 1);

        $loans = null;
        if ($loan = $obj->emp_deduction('Loan Repayment', $rec->id, $month, $year))
            $loans = $loan;

        if ($helb = $obj->emp_deduction('HELB', $rec->id, $month, $year))
            $loans += $helb;

        if ($i == 25)
            if ($loans)
                $pdf->Cell($w, 0, number_format($loans / 1, 2), 1, 0, 'R');
            else
                $pdf->Cell($w, 0, '', 1);

        if ($i == 26)
            if ($standing = $obj->emp_deduction('Misc Ded/Ref', $rec->id, $month, $year))
                $pdf->Cell($w, 0, number_format($standing / 1, 2), 1, 0, 'R');
            else
                $pdf->Cell($w, 0, '', 1);

        if ($i == 27)
            if ($standing = $obj->emp_deduction('Pension Refund', $rec->id, $month, $year))
                $pdf->Cell($w, 0, number_format($standing / 1, 2), 1, 0, 'R');
            else
                $pdf->Cell($w, 0, '', 1);

        $sub = $obj->total_deduction($rec->id, $month, $year);
        $total = $tax + $sub + $pension + $nssf + $nhif;

        if ($i == 29) {
            $pdf->Cell($w, 0, number_format($total, 2), 1, 0, 'R', $fill);
            $total_deduction += $total;
        }


        $net = $gross - $total;

        $netPay[$rec->id] = "$net|$tax|$gross|$paye";

        if ($i == 30)
            $pdf->Cell($w, 0, number_format($net / 1, 2), 1, 0, 'R');

        if ($i == 31) {
            $pdf->SetFont('', 'B', 8);
            $pdf->Cell($w, 4.7, $rec->fname[0] . $rec->mname[0] . ' ' . $rec->lname, 1, 0, 'R');
        }
    }

    $pdf->SetFont('', 'B', 10);

    if ($i == 1 || $i == 2 || $i == 5 || $i == 6 || $i == 8 || $i == 9 || $i == 10 || $i == 11
        || $i == 12 || $i == 15 || $i == 16 || $i == 17 || $i == 22 || $i == 23 || $i == 24 || $i == 25
        || $i == 26 || $i == 27 || $i == 28 || $i == 31
    )
        $pdf->Cell($w, 0, '', 1);

    if ($i == 0)
        $pdf->Cell($w, 0, $monthName, 1, 0, '', $fill);

    $sb = $obj->sum_basicpay() / 1;
    $sa = 0;
    if ($obj->sum_allowance($year, $month))
        $sa = $obj->sum_allowance($year, $month);

    if ($i == 3)
        $pdf->Cell($w, 0, number_format($sb, 2), 1, 0, 'R');

    if ($i == 4)
        $pdf->Cell($w, 0, number_format($sa, 2), 1, 0, 'R');

    if ($i == 7)
        $pdf->Cell($w, 0, number_format($sb + $sa, 2), 1, 0, 'R', $fill);

    $emp = $obj->get_employees();
    $p = $mr = $ns = $nh = $pp = 0;
    foreach ($emp as $item) {
        $p += $obj->get_tax($gross);
        $mr += $deductions[0]->value;
        $ns += $deductions[1]->value;
        $nh += $obj->nhif_value($item->basic_pay);
        $pp += $deductions[5]->value * $item->basic_pay;
    }

    if ($i == 13)
        $pdf->Cell($w, 0, number_format($p, 2), 1, 0, 'R', $fill);

    if ($i == 14)
        $pdf->Cell($w, 0, '(' . number_format($mr, 2) . ')', 1, 0, 'R');


    if ($i == 18)
        $pdf->Cell($w, 0, number_format($netSum, 2), 1, 0, 'R');

    if ($i == 19)
        $pdf->Cell($w, 0, number_format($ns, 2), 1, 0, 'R');

    if ($i == 20)
        $pdf->Cell($w, 0, number_format($nh, 2), 1, 0, 'R');

    if ($i == 21)
        $pdf->Cell($w, 0, number_format($pp, 2), 1, 0, 'R');

    if ($i == 29)
        $pdf->Cell($w, 0, number_format($total_deduction, 2), 1, 0, 'R');

    if ($i == 30)
        $pdf->Cell($w, 0, number_format(($sb + $sa) - $total_deduction, 2), 1, 0, 'R');

    $pdf->Ln();
    ++$i;
}


foreach ($netPay as $key => $val) {
    $obj->save_pay($key, $month, $year, $val);
}

$pdf->Output('Payroll.pdf');