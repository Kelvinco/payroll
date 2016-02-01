<?php
/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright �2015
 * All Rights Reserved
 * Date: 24/12/2015
 * Time: 12:59 PM
 *
 * Package Name: Makmesh Payroll (Kenya)
 * File Name: p10a.php
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
$pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 10);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


$pdf->setFontSubsetting(true);
$pdf->SetFont('', 'BI', 14, '', true);

$pdf->SetTitle("Makmesh Payroll (Kenya) - P10A");

$pdf->AddPage();
$year = (int)$_GET['y'];
$pdf->Image("../assets/img/kra.png", 70);
$pdf->Ln(30);

$pdf->Cell(0, 10, "P.10A", 0, 1);

$pdf->SetFont('', 'B');
$pdf->Cell(0, 5, "P.A.Y.E SUPPORTING LIST FOR END OF YEAR CERTIFICATE: YEAR $year", 0, 1);

$pdf->Cell(150, 10, "PIN", 0, 1, "R");

$pdf->Cell(120, 5, "");
$pdf->SetFont('');
$pin = "A000000000Z";
for ($i = 0; $i < 11; ++$i)
    $pdf->Cell(5, 5, $pin[$i], 1, 0, "C");

$pdf->SetFont('', 'B');
$pdf->Ln();

$pdf->Cell(0, 10, "EMPLOYER'S NAME:     Makmesh Payroll (Kenya)", 0, 1);

$headers = array("EMPLOYEE'S PIN", 'EMPLOYEE\'S NAME', 'TOTAL EMOLUMENTS KSHS.', "PAYE DEDUCTED  KSHS.");

$width = array(35, 55, 50, 35);

$pdf->SetFont("", 'B', 10);
$i = 0;
foreach ($headers as $head) {
    $pdf->MultiCell($width[$i], 10, $head, 1, "L", false, 0);
    ++$i;
}
$pdf->Ln();
$pdf->SetFont('');

$obj = new Employee();
$items = $obj->get_employees();

$pdf->setColor('fill', 220, 220, 220);
$h = 6;
$te = $tt = 0;
foreach ($items as $item) {
    $ge = $obj->get_emoluments($year, $item->id);
    $emo = $tax = "";
    if ($ge) {
        $emo = number_format($ge->gross, 2);
        $tax = number_format($ge->tax, 2);
    }
    $pdf->MultiCell($width[0], $h, $item->pin, 1, "L", false, 0);
    $pdf->MultiCell($width[1], $h, "$item->fname $item->mname $item->lname", 1, "L", false, 0);
    $pdf->MultiCell($width[2], $h, $emo, 1, "R", false, 0);
    $pdf->MultiCell($width[3], $h, $tax, 1, "R");
}

$pdf->SetFont('', 'B');
$ge = $obj->get_emoluments($year);
$pdf->MultiCell($width[0], $h, "", 1, "L", false, 0);
$pdf->MultiCell($width[1], $h, "TOTAL EMOLUMENTS", 1, "L", false, 0);
$pdf->MultiCell($width[2], $h, number_format($ge->gross, 2), 1, "R", false, 0);
$pdf->MultiCell($width[3], $h, "", 1, "R", true);


$pdf->MultiCell($width[0], $h, "", 1, "L", false, 0);
$pdf->MultiCell($width[1], $h, "TOTAL PAYE TAX", 1, "L", false, 0);
$pdf->MultiCell($width[2], $h, "", 1, "R", true, 0);
$pdf->MultiCell($width[3], $h, number_format($ge->tax, 2), 1, "R");

$pdf->MultiCell($width[0], $h, "", 1, "L", false, 0);
$pdf->MultiCell($width[1], $h, "TOTAL WCPS", 1, "L", false, 0);
$pdf->MultiCell($width[2], $h, "", 1, "R", true, 0);
$pdf->MultiCell($width[3], $h, "", 1, "R");

$pdf->MultiCell($width[0], $h, "", 1, "L", false, 0);
$pdf->SetFont('', 'B', 9);
$pdf->MultiCell($width[1] + $width[2], $h, "*TOTAL TAX ON LUMP SUM/AUDIT TAX/INTEREST/PENALTY", 1, "L", false, 0);
$pdf->MultiCell($width[3], $h, "", 1, "R");

$pdf->SetFont("", 'B', 10);
$pdf->MultiCell($width[0], $h, "", 1, "L", false, 0);
$pdf->MultiCell($width[1] + $width[2], $h, "TOTAL TAX C/F TO NEXT LIST", 1, "L", false, 0);
$pdf->MultiCell($width[3], $h, '', 1, "R");

$pdf->SetFont('');

$pdf->Cell($width[0], $h, "");
$pdf->Cell($width[1], $h, "*DELETE AS APPROPRIATE");

$pdf->Ln(10);

$pdf->Cell(0, 0, "NOTE TO EMPLOYER: ATTACH TWO COPIES OF THIS LIST TO END OF YEAR CERTIFICATE (P10)");

$pdf->Output("P10A.pdf");