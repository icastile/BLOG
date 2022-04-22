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
 * @param $user_id - the posting user
 * @param $reqVars - array of request variables
 * @param PDO $db - the DB
 * @param string $sql - sql statement to execute
 * @return bool - true is statement success
 */
function getSuccess($id, $user_id, $reqVars, PDO $db, string $sql): bool
{

    var_dump($reqVars['extra']);

    $queryVars = [
        ':id' => $id,
        ':user_id' => $user_id,
        ':post_date' => date("Y/m/d"),
        ':post_text' => "Technically not Null",
        ':extra' => json_encode($reqVars["extra"]),
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
    $key = "username";
    $value = $reqVars["username"];
    $resource = "blog_user";
    if ($value == NULL)
    {
        $key = "id";
        $value = $reqVars["id"];
        $resource = "post";
    }
    $checkSql = 'select count(*) from ' . $resource . ' where ' . $key . ' = :value';
    $checkStmt = $db->prepare($checkSql);
    $checkStmt->execute([$reqVars[$key]]);

    return $checkStmt->fetchColumn() == 1;

}
