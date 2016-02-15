<?php
/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright �2015
 * All Rights Reserved
 * Date: 24/12/2015
 * Time: 12:37 AM
 *
 * Package Name: Makmesh Payroll (Kenya)
 * File Name: payslip.php
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

$obj = new Employee();
$company = $obj->get_company();
$pdf->SetTitle($company->name . ' - Payslip');

/**********************************************************************************************************************/


$month = (int)$_GET['m'];
$year = (int)$_GET['y'];

$employees = $obj->get_employees();
$other = $obj->other_deductions($year, $month);
foreach ($employees as $employee) {
    $id = $employee->id;
    $basic = $employee->basic_pay;
    $pdf->AddPage();
    $pdf->setColor('fill', 220, 220, 220);
    $pdf->Cell(130, 0, 'SALARY VOUCHER', 0, 1, 'C', true);
    $pdf->SetFont('', 'B', 10);
    $pdf->Ln(9);

    $pdf->Rect(15, 40, 130, 150);

    $dateObj = DateTime::createFromFormat("!m", $month);
    $pdf->Cell(50, 0, $company->name);
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
    $pdf->Cell($w3, $h, "(" . number_format($relief, 2) . ")", 0, 1, "R", true);

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
}


$pdf->Output("Payslip.pdf");