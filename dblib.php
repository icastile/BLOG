<?php

function connect()
{
    try {
        $db = new PDO("pgsql:dbname=blog host=localhost password=314dev user=dev");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $err) {
        $db = null;
        exit("Database Error");
    }

    return $db;
}