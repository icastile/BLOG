<?php

function connect()
{
    try {
        $db = new PDO("pgsql:dbname=blog host=icastile19@web.cs.georgefox.edu password=icastile19 user=icastile19");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $err) {
        $db = null;
        exit("Database Error");
    }

    return $db;
}