<?php

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
 * @param $reqVars - array of request variables
 * @param PDO $db - the DB
 * @param string $sql - sql statement to execute
 * @return bool - true is statement success
 */
function getSuccess($id, $reqVars, PDO $db, string $sql): bool
{
    $queryVars = [
        ':id' => $id,
        ':username' => $reqVars['username'],
        ':password' => $reqVars['password'],
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
    $expectedCols = ["username", "password"];
    $valid = true;
    foreach ($expectedCols as $col) {
        if (!array_key_exists($col, $reqVars)) {
            $valid = false;
        }
    }

    return $valid;
}

/**
 * Returns true if given username is in DB
 *
 * @param $db - the DB
 * @param $reqVars - array of request variables
 * @return bool - true if DB contains given vend_id
 */
function keyInDB($db, $reqVars): bool
{

    $key = "username";
    $value = $reqVars["username"];
    $checkSql = 'select count(*) from blog_user where ' . $key . ' = :value';
    $checkStmt = $db->prepare($checkSql);
    $checkStmt->execute([$reqVars[$key]]);

    return $checkStmt->fetchColumn() == 1;

}
