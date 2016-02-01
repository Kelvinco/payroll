<?php

/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright ©2015
 * All Rights Reserved
 * Date: 6/20/2015
 * Time: 7:18 AM
 *
 * Package Name: skeleton
 * Class Name: ourdb.php
 *
 */
class OurDB extends Db
{
    public static function get_val($s)
    {
        $mysqli = self::Connection();
        $result = $mysqli->query($s);
        if ($mysqli->error) {
            Errors::log_messages($mysqli->error);
            Errors::log_messages($s);
        }
        $row = $result->fetch_row();
        if (empty($row[0])) {
            return false;
        }
        return $row[0];
    }

    public static function get_rows($s)
    {
        $mysqli = self::Connection();
        $result = $mysqli->query($s);
        if ($mysqli->error) {
            Errors::log_messages($mysqli->error);
            Errors::log_messages($s);
        }
        if ($result->num_rows == 0) {
            return false;
        }
        $rows = array();
        while ($row = $result->fetch_object()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public static function execute_query($s)
    {
        $mysqli = self::Connection();
        $result = $mysqli->query($s);
        if ($mysqli->error) {
            Errors::log_messages($mysqli->error);
            Errors::log_messages($s);
        }
        if ($mysqli->affected_rows > 0) {
            return true;
        }
        return false;
    }

    public static function  insert($values, $table)
    {
        $q = $o = array();
        foreach ($values as $key => $vals) {
            $q[] = "`$key`";
            $o[] = "'$vals'";
        }
        return self::execute_query("INSERT INTO `$table` (" . implode(", ", $q) . ") VALUES (" . implode(", ", $o) . ")");
    }

    public static function edit($table, $set, $where, $id = "id")
    {
        $values = array();
        foreach ($set as $key => $vals) {
            $values[] = "`$key` = '$vals'";
        }
        $query = "UPDATE `$table` SET " . implode(", ", $values) . " WHERE `$id` = $where";
        return self::execute_query($query);
    }

    public static function last_id()
    {
        $mysqli = self::Connection();
        return $mysqli->insert_id;
    }
}