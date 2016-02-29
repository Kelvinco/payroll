<?php

/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright �2015
 * All Rights Reserved
 * Date: 22/12/2015
 * Time: 9:25 PM
 *
 * Package Name: Makmesh Payroll (Kenya)
 * Class Name: employee.php
 *
 */
class Employee extends OurDB
{

    protected
        $id,
        $fname,
        $mname,
        $lname,
        $pin,
        $national_id,
        $email,
        $nssf_no,
        $nhif_no,
        $account_no,
        $basic_pay,
        $bank,
        $branch,
        $code;

    public function __construct($post = null)
    {
        if (is_array($post))
            foreach ($post as $key => $val)
                $this->$key = $val;
    }

    public function maint_emp()
    {
        $input = array('fname' => $this->fname, 'mname' => $this->mname, 'lname' => $this->lname, 'pin' => $this->pin,
            'national_id' => $this->national_id, 'email' => $this->email, 'nssf_no' => $this->nssf_no,
            'nhif_no' => $this->nhif_no, 'account_no' => $this->account_no, 'basic_pay' => $this->basic_pay,
            'bank' => $this->bank, 'branch' => $this->branch, 'code' => $this->code);

        if ($this->id)
            if (self::edit('employee', $input, $this->id))
                return array('home', 'Employee Notification', 'The changes were saved.');
            else return array('home', 'Employee Notification', 'You made no changes.');


        if ($this->check_employee())
            return array('register', 'Employee Registration',
                'Bank Account No, National ID, Email, and KRA PIN should be unique for every employee.');


        if (self::insert($input, 'employee')) {
            if (isset($_SESSION['post']))
                unset($_SESSION['post']);
            return array('register', 'Employee Registration', "$this->fname $this->mname $this->lname was registered.");
        } else return array('register', 'Employee Registration', 'Something went wrong. Please try again later.');
    }

    public function check_employee()
    {
        if (self::get_val("SELECT id FROM employee WHERE account_no = $this->account_no OR
                            national_id = $this->national_id OR pin = '$this->pin' OR email = '$this->email'")
        )
            return true;
        return false;
    }

    public function add_overtime($item)
    {
        $iko = $_POST['iko'];
        $input = array('employee_id' => $item['employee_id'], 'amount' => $item['amount'], 'month' => $item['month'],
            'year' => $item['year']);
        if (self::insert($input, 'overtime')) {
            if (isset($_SESSION['post']))
                unset($_SESSION['post']);
            return array("overtime$iko", 'Overtime Notification!', 'The record was saved.');
        }
        return array("overtime$iko", 'Overtime Notification!', 'An error occurred. Please try again later.');
    }

    public function get_overtime($id, $year, $month)
    {
        return self::get_val("SELECT amount FROM overtime WHERE employee_id = $id AND year = $year
                                AND month = $month");
    }

    public function update_nhif($item)
    {
        $e = false;
        for ($i = 1; $i <= 17; ++$i) {
            $input = array('min' => $item["min$i"], 'max' => $item["max$i"], 'deduction' => $item["deduction$i"]);
            if (self::edit('nhif', $input, $item["row$i"]))
                $e = true;
        }

        if ($e)
            return array('nhif', 'NHIF Notification.', 'Your changes were saved.');
        else return array('nhif', 'NHIF Notification.', 'You made no changes.');
    }

    public function update_paye($item)
    {
        $e = false;
        for ($i = 1; $i <= 5; ++$i) {
            $input = array("min" => $item["min$i"], "max" => $item["max$i"], "rate" => $item["rate$i"] / 100);
            if (self::edit('paye', $input, $item["row$i"]))
                $e = true;
        }
        if ($e)
            return array('paye', 'P.A.Y.E Notification.', 'Your changes were saved.');
        else return array('paye', 'P.A.Y.E Notification.', 'You made no changes.');
    }

    public function maint_deduction($item)
    {
        $e = false;
        for ($i = 1; $i < 4; ++$i) {
            if ($i <= 2) {
                $input = array('value' => $item["value$i"]);
                if (self::edit("deduction", $input, $i))
                    $e = true;
            }
            if ($i == 3)
                if (self::execute_query("UPDATE deduction SET value = " . $item["value6"] . " WHERE id = 6"))
                    $e = true;
        }

        if ($e)
            return array('deduction', 'Deduction Notification.', 'Your changes were saved.');
        else return array('deduction', 'Deduction Notification.', 'You made no changes.');

//        if (self::insert($input, 'deduction')) {
//            if (isset($_SESSION['post']))
//                unset($_SESSION['post']);
//            return array('deduction', 'Deduction Notification.', 'The item was added.');
//        } else return array('deduction', 'Deduction Notification.', 'Something went wrong. Please try again later.');
    }

    public function get_employees($home = null)
    {
        $s = ' WHERE exclude = 0';
        if ($home)
            $s = '';
        return self::get_rows("SELECT `id`, `fname`, `mname`, `lname`, `pin`, `national_id`, `email`, `nssf_no`,
                                  `nhif_no`, `account_no`, `basic_pay`, `bank`, `branch`, `code` FROM employee $s");
    }

    public function get_employee($id)
    {
        $obj = self::get_rows("SELECT `fname`, `mname`, `lname`, `pin`, `national_id`, `email`, `nssf_no`, `nhif_no`,
                                `account_no`, `basic_pay`, `bank`, `branch`, `code` FROM employee WHERE  id = $id");
        if ($obj)
            return array_shift($obj);
        return false;
    }

    public function get_nhif()
    {
        return self::get_rows("SELECT min, max, deduction FROM nhif");
    }

    public function get_paye()
    {
        return self::get_rows("SELECT min, max, rate FROM paye");
    }

    public function get_deductions()
    {
        return self::get_rows("SELECT name, value, auto FROM deduction");
    }

    public function filter_deductions()
    {
        return self::get_rows("SELECT name FROM deduction WHERE auto = 0 AND name != 'Monthly Relief'
                              AND name != 'N.S.S.F' AND name != 'Pension Plan'");
    }

    public function get_name($id)
    {
        return self::get_val("SELECT CONCAT(fname,' ', mname, ' ', lname) FROM employee WHERE id = $id");
    }

    public function get_tax($amount)
    {
        $cat = self::get_rows("SELECT min, max, rate FROM paye");
        $tax = 0;
        if ($amount >= $cat[0]->min && $amount > $cat[0]->max)
            $tax += ($cat[0]->max - ($cat[0]->min - 1)) * $cat[0]->rate;
        else if ($amount <= $cat[0]->max)
            $tax += (($amount + 1) - ($cat[0]->min)) * $cat[0]->rate;

        if ($amount >= $cat[1]->min && $amount > $cat[1]->max)
            $tax += ($cat[1]->max - ($cat[1]->min - 1)) * $cat[1]->rate;
        else if ($amount <= $cat[1]->max)
            $tax += (($amount + 1) - ($cat[1]->min)) * $cat[1]->rate;

        if ($amount >= $cat[2]->min && $amount > $cat[2]->max)
            $tax += ($cat[2]->max - ($cat[2]->min - 1)) * $cat[2]->rate;
        else if ($amount <= $cat[2]->max)
            $tax += (($amount + 1) - ($cat[2]->min)) * $cat[2]->rate;

        if ($amount >= $cat[3]->min && $amount > $cat[3]->max)
            $tax += ($cat[3]->max - ($cat[3]->min - 1)) * $cat[3]->rate;
        else if ($amount <= $cat[3]->max)
            $tax += (($amount + 1) - ($cat[3]->min)) * $cat[3]->rate;

        if ($amount >= $cat[4]->min)
            $tax += (($amount + 1) - ($cat[4]->min)) * $cat[4]->rate;

        return floor($tax);
    }

    public function nhif_value($amount)
    {
        return self::get_val("SELECT deduction FROM nhif WHERE $amount BETWEEN min AND max");
    }

    public function saved_deductions($y, $m)
    {
        return self::get_rows("SELECT payroll.id, fname, mname, lname, item, value FROM payroll, employee WHERE month = $m AND
                                year = $y AND employee_id = employee.id");
    }

    public function save_deduction($item)
    {
        $input = array('employee_id' => $item['employee_id'], 'item' => $item['item'], 'value' => $item['value'],
            'month' => $item['month'], 'year' => $item['year']);
        if (self::insert($input, 'payroll')) {
            if (isset($_SESSION['post']))
                unset($_SESSION['post']);
            return array($item['url'], 'Deduction Notification', "The record was saved.");
        } else
            return array($item['url'], 'Deduction Notification', "Something wrong happened. Please try again later.");
    }

    public function remove_payroll($id)
    {
        return self::execute_query("DELETE FROM payroll WHERE id = $id");
    }

    public function emp_deduction($cat, $id, $m, $y)
    {
        return self::get_val("SELECT value FROM payroll WHERE employee_id = $id AND item = '$cat' AND month = $m AND year = $y");
    }

    public function total_deduction($id, $m, $y)
    {
        return self::get_val("SELECT SUM(value) FROM payroll WHERE employee_id = $id AND month = $m AND year = $y");
    }

    public function get_netpay($id, $m, $y)
    {
        return self::get_rows("SELECT value FROM pay WHERE employee_id = $id AND year = $y AND month = $m");
    }

    public function save_pay($id, $m, $y, $v)
    {
        $tokens = explode("|", $v);
        $input = array("employee_id" => $id, "month" => $m, "year" => $y, "value" => $tokens[0],
            "gross_pay" => $tokens[2], "net_tax" => $tokens[1], 'actual_tax' => $tokens[3]);
        if (!self::get_val("SELECT id FROM pay WHERE employee_id = $id AND month = $m AND year = $y")) {
            if (isset($_SESSION['post']))
                unset($_SESSION['post']);
            return self::insert($input, "pay");
        } else {
            if (isset($_SESSION['post']))
                unset($_SESSION['post']);
            return self::edit('pay', $input, $id, 'employee_id');
        }

        return false;
    }

    public function get_pay($id, $m, $y)
    {
        return self::get_val("SELECT value FROM pay WHERE employee_id = $id AND month = $m AND year = $y");
    }

    public function sum_basicpay()
    {
        return self::get_val("SELECT SUM(basic_pay) FROM employee");
    }

    public function other_deductions($y, $m)
    {
        return self::get_rows("SELECT employee_id, value, item FROM payroll WHERE (item = 'Insurance Relief' OR
                                item = 'Standing Order' OR item = 'Misc Ded/Ref' OR item = 'Pension Refund') AND
                                year = $y AND month = $m");
    }

    public function monthly_payroll($item, $id, $m, $y)
    {
        return self::get_val("SELECT value FROM payroll WHERE item = '$item' AND employee_id = $id AND month = $m AND
                              year = $y");
    }

    public function sum_payroll($id, $m, $y)
    {
        $sum = self::get_val("SELECT SUM(value) FROM payroll WHERE employee_id = $id AND year = $y AND month = $m");
        if ($sum) return $sum;
        return 0;
    }

    public function get_emoluments($year, $id = null)
    {
        $q = "SELECT SUM(gross_pay) AS gross, SUM(net_tax) AS tax FROM pay WHERE year = $year";
        if ($id) $q .= " AND employee_id = $id";
        $ge = self::get_rows($q);
        if ($ge) return array_shift($ge);
        return false;
    }

    public function sum_tax($m, $y, $t = null)
    {
        if ($t)
            return self::get_val("SELECT SUM(net_tax) FROM pay WHERE year = $y");
        return self::get_val("SELECT SUM(net_tax) FROM pay WHERE month = $m AND year = $y");
    }

    public function overtime($y, $m)
    {
        return self::get_rows("SELECT overtime.id, fname, mname, amount FROM overtime, employee WHERE year = $y AND month = $m AND employee_id = employee.id");
    }

    public function rem_overtime($id)
    {
        return self::execute_query("DELETE FROM overtime WHERE  id = $id");
    }

    public function rem_employee($id)
    {
        self::execute_query("DELETE FROM overtime WHERE employee_id = $id");
        self::execute_query("DELETE FROM pay WHERE employee_id = $id");
        self::execute_query("DELETE FROM payroll WHERE employee_id = $id");
        return self::execute_query("DELETE FROM employee WHERE id = $id");
    }

    public function get_actual($m, $y, $id)
    {
        return self::get_val("SELECT actual_tax FROM pay WHERE employee_id = $id AND year = $y AND month = $m");
    }

    public function get_multiplier($y, $id)
    {
        return self::get_val("SELECT COUNT(month) FROM pay WHERE year = $y AND employee_id = $id");
    }

    public function check_payroll($m, $y)
    {
        return self::get_val("SELECT id FROM pay WHERE month = $m AND year = $y");
    }

    public function allowance_list()
    {
        return self::get_rows('SELECT name, id FROM allowance');
    }

    public function new_allowance($name)
    {
        if (self::get_val("SELECT id FROM allowance WHERE name = '$name'"))
            return array('allowance', 'Allowance Notification!', 'The Record already exists.');

        if (self::execute_query("INSERT INTO allowance (name) VALUE ('$name')"))
            return array('allowance', 'Allowance Notification!', 'The Record was saved.');
        return array('allowance', 'Allowance Notification!',
            'An error occurred while saving your changes. Please try again later.');
    }

    public function remove_allowance($id)
    {
        if (self::execute_query("DELETE FROM allowance WHERE id = $id"))
            return 'The Record was deleted.';
        else
            return 'The Record ID could not be found.';
    }

    public function saved_allowances($m, $y)
    {
        return self::get_rows("SELECT employee_allowance.id, allowance, amount, fname, lname, mname FROM
                                employee_allowance, employee WHERE month = $m AND year = $y AND
                                employee_allowance.employee_id = employee.id");
    }

    public function save_allowance($item, $url)
    {
        $input = array('employee_id' => $item['employee_id'], 'amount' => $item['amount'], 'month' => $item['month'],
            'year' => $item['year'], 'allowance' => $item['allowance']);

        if (self::insert($input, 'employee_allowance'))
            return array($url, 'Allowance Notification!', 'The record was saved.');
        return array($url, 'Allowance Notification!', 'An internal error occurred. Please try again later.');
    }

    public function del_allowance($id)
    {
        if (self::execute_query("DELETE FROM employee_allowance WHERE id = $id"))
            return 'The record was deleted.';
        return 'The Record ID was not found.';
    }

    public function get_allowance($employee_id, $m, $y)
    {
        return self::get_val("SELECT SUM(amount) FROM employee_allowance WHERE employee_id = $employee_id AND month = $m AND
                              employee_allowance.year = $y");
    }

    public function list_allowances($employee_id, $m, $y)
    {
        return self::get_rows("SELECT amount, allowance FROM employee_allowance WHERE employee_id = $employee_id AND month = $m AND
                              employee_allowance.year = $y");
    }

    public function sum_taxes($employee_id, $year)
    {
        $taxes = self::get_rows("SELECT SUM(actual_tax) AS actual_tax, SUM(net_tax) AS net_tax FROM pay WHERE
                                  employee_id = $employee_id AND year = $year");
        if ($taxes)
            return array_shift($taxes);
        return false;
    }

    public function sum_allowance($y, $m)
    {
        return self::get_val("SELECT SUM(amount) FROM employee_allowance WHERE year = $y AND month = $m");
    }

    public function get_company()
    {
        $company = self::get_rows('SELECT name, id, box_address, kra_pin FROM company');
        if ($company)
            return array_shift($company);
        return false;
    }

    public function edit_company($item)
    {
        $input = array('name' => $item['name'], 'box_address' => $item['box_address'], 'kra_pin' => $item['kra_pin']);
        if (self::edit('company', $input, $item['id']))
            return array('profile', 'Profile Notification!', 'Your changes were saved.');

        return array('profile', 'Profile Notification!', 'You made no changes.');
    }

    public function exclude($id)
    {
        if (self::execute_query("UPDATE employee SET exclude = 1 WHERE id = $id"))
            return 'The employee will not appear in the payroll.';
        return 'Invalid Employee ID supplied.';
    }
}