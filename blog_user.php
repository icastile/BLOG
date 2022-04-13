<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
//implements the signup resource from blog DB
// supports all CRUD operations
// CREATE
// READ
// UPDATE
// DELETE

require_once "dblib.php";
require_once "rest.php";
require_once "validateBlogUser.php";

$request = new RestRequest();

// returns PHP array of everything thats sent(could be nested array)
$reqVars = $request->getRequestVariables();

// connect to the DB
$db = connect();

if ($request->isGet()) {

    $key = implode(array_keys($reqVars));
    $value = implode(array_values($reqVars));

    // check if vend_id is in the DB
    $valid = validateKeyExists($key, $db, $reqVars);

    if ($valid) {
        // build the sql
        $sql = 'select * from blog_user where ' . $key . ' = :value';
        // run the query
        $query = $db->prepare($sql);
        $query->execute([$reqVars[$key]]);
        // get the data from the DB
        $results = $query->fetch(PDO::FETCH_ASSOC);
        $response = $results;

        // return to user as JSON string
        http_response_code(200);
    } // send error message
    else {
        $response = array("error_text" => "The id was missing or does not exist");
        http_response_code(400);
    }

    echo json_encode($response) . "\n";
}

// if the request is POST, create a blog_user
else if ($request->isPost()) {
    // check if request contains all required variables
    if (containsAllVars($reqVars)) {
        // check if id exists in DB
        if (keyInDB($db, $reqVars)) {
            $results = ['error' => true, 'message' => 'user already exists'];
        } else {
            // get the highest id value
            $getHighID = 'select max(id) from blog_user';
            $highQuery = $db->prepare($getHighID);
            $highQuery->execute();
            $highNum = $highQuery->fetch(PDO::FETCH_ASSOC);
            // increment the highest invoice number
            $highNum["max"]++;

            $password = password_hash($reqVars['password'], PASSWORD_DEFAULT);
            $reqVars['password'] = $password;

            $sql = 'insert into blog_user (id, username, password) 
                    values (:id, :username, :password)';
            $success = getSuccess($highNum["max"], $reqVars, $db, $sql);
            $results = ['message' => $success ? 'Success' : 'Did not add blog user'];
            http_response_code(202);
        }
    } else {
        $results = ['error' => true, 'message' => 'Insufficient data'];
        http_response_code(400);
    }
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode($results) . "\n";
}


