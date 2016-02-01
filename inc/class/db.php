<?php

/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright ©2015
 * All Rights Reserved
 * Date: 6/20/2015
 * Time: 7:16 AM
 *
 * Package Name: skeleton
 * Class Name: db.php
 *
 */
class Db
{
    protected static $_connection = NULL;
    private static $_mysqlUser = 'root';
    private static $_mysqlPass = '';
    private static $_mysqlDb = 'payroll';
    private static $_mysqlHost = 'localhost';

    public static function Connection()
    {
        if (!self::$_connection) {
            self::$_connection = @new mysqli(self::$_mysqlHost, self::$_mysqlUser, self::$_mysqlPass, self::$_mysqlDb);
            if (self::$_connection->connect_error) {
                die("Connection failed. Cannot connect to MySQL Server. ");
            }
            self::$_connection->set_charset('utf-8');
        }
        return self::$_connection;
    }
}