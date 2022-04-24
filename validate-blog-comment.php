<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

/**
 * returns true if the key exists in both reqVars and the DB
 *
 * @param $key - the key of the resource
 * @param $db - the DB
 * @param $reqVars - array of request variables
 * @return bool - true if key exists
 */
function validateKeyExists($key, $db, $reqVars): bool
{
    return array_key_exists($key, $reqVars) && keyInDB($db, $reqVars);

}

/**
 * Returns true if the sql statement was successful
 *
 * @param $id - the key of the resource
 * @param $user_id - the posting user
 * @param $reqVars - array of request variables
 * @param PDO $db - the DB
 * @param string $sql - sql statement to execute
 * @return bool - true is statement success
 */
function getSuccess($id, $user_id, $reqVars, PDO $db, string $sql): bool
{

    $queryVars = [
        ':id' => $id,
        ':user_id' => $user_id,
        ':post_id' => $reqVars["post_id"],
        ':comment_text' => $reqVars["comment_text"],
        ':comment_date' => date("Y/m/d"),
    ];

    $stmt = $db->prepare($sql);


    return $stmt->execute($queryVars);
}

/**
 * Returns true if contains expected vars to execute request
 *
 * @param $reqVars - array of request variables
 * @return bool - true if reqVars contains all required columns
 */
function containsAllVars($reqVars): bool
{
    $expectedCols = ["username", "extra"];
    $valid = true;
    foreach ($expectedCols as $col) {
        if (!array_key_exists($col, $reqVars)) {
            $valid = false;
        }
    }

    return $valid;
}

/**
 * Returns true if given username/id exists as a user
 *
 * @param $db - the DB
 * @param $reqVars - array of request variables
 * @return bool - true if DB contains given vend_id
 */
function keyInDB($db, $reqVars): bool
{
    $key = "post_id";
    $value = $reqVars["post_id"];

    $checkSql = 'select count(*) from blog_comment where ' . $key . ' = :value';
    $checkStmt = $db->prepare($checkSql);
    $checkStmt->execute([$reqVars[$key]]);

    return $checkStmt->fetchColumn() == 1;

}

