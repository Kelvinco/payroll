<?php

/**
 * Created by PhpStorm
 * Designed by Makmesh iKiev
 * <ikiev@makmesh.com>
 * Copyright ©2015
 * All Rights Reserved
 * Date: 6/20/2015
 * Time: 7:21 AM
 *
 * Package Name: skeleton
 * Class Name: errors.php
 *
 */
class Errors
{
    public static function log_messages($log)
    {
        $fp = fopen("logs/errors", "a");

        fwrite($fp, date("H:i:s d/m/Y") . " : " . $log . "\n\n");
        fclose($fp);
    }
}