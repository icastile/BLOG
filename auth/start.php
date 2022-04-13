<?php


require_once "../rest.php";

$request = new RestRequest();

if ($request->isPost()) {
    $vars = $request->getRequestVariables();
    $username = $vars['username'];
    $password = $vars['password'];
    //check against the database
    session_start();
    $_SESSION['username'] = $username;
    echo json_encode(['username' => $username, 'message' => 'Logged In!']);
} else {
    http_response_code(351);
    echo json_encode(['error' => true, 'message' => 'Invalid request']);
}

/*if(array_key_exists("user", $_GET) && array_key_exists("password", $_GET))
{
    //check their password (normally you would use a database)
    if($_GET["password"] == "secrettime")
    {
        //create a php session
        session_start();

        //register a username
        $_SESSION["username"] = $_GET["user"];
    }
    else
    {
        echo "WRONG!\n";
    }
}
else
{
    echo "no username provided!\n";
}*/
