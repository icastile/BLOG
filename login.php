<?php

require_once "rest.php";
require_once "dblib.php";

$request = new RestRequest();

$reqVars = $request->getRequestVariables();


$db = connect();

if ($request->isPost())
{
    $key = "username";

    $value = $reqVars[$key];
    // first - check user exists in the DB
    $sql = 'select * from blog_user where ' . $key . ' = :value';
    $stmt = $db->prepare($sql);
    $stmt->execute([$reqVars[$key]]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $verified = false;


    if (count($result))
    {

        $hashed = $result['password'];
        $password = $reqVars['password'];
        $verified = password_verify($password, $hashed);
    }
    if ($verified)
    {
        //if verified - start the session and redirect to home page
        $results = ['message' => 'Success'];
        session_start();
        $_SESSION['username'] = $reqVars[$key];

    }
    else{

        // if not redirect back to login page with error message
        $results = ['error' => true];
    }
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode($results);



    // if its good - start the session

    // if not - redirect back to the login page

}
else{
    echo password_hash("Testing!", PASSWORD_DEFAULT);
}