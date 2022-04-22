<?php
require_once "dblib.php";
require_once "rest.php";
require_once "validate-blog-post.php";

$request = new RestRequest();

// returns PHP array of everything thats sent(could be nested array)
$reqVars = $request->getRequestVariables();

// connect to the DB
$db = connect();

if ($request->isGet()) {

    //$key = "username";
    //$value = $reqVars['username'];

    $key = implode(array_keys($reqVars));
    $value = implode(array_values($reqVars));

    // check if vend_id is in the DB
    $valid = validateKeyExists($key, $db, $reqVars);
    if ($valid) {
        // if the request doesnt have an id
        if ($key == "username") {
            $getUserIdQ = "select id from blog_user where username = '$value'";
            $getUserId = $db->prepare($getUserIdQ);
            $getUserId->execute();

            $user_id = $getUserId->fetch(PDO::FETCH_ASSOC);
            $id = $user_id["id"];
            // build the sql
            $sql = "select * from post where user_id = '$id'";
            // run the query
            $query = $db->prepare($sql);
            $query->execute();
            // get the data from the DB
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            $response = $results;
        }
        else {
            $sql = 'select * from post where id = ' . $value;
            // run the query
            $query = $db->prepare($sql);
            $query->execute();
            // get the data from the DB
            $results = $query->fetch(PDO::FETCH_ASSOC);

            $response = $results;
        }

        // return to user as JSON string
        http_response_code(200);
    } // send error message
    else {
        $response = array("error_text" => "The id was missing or does not exist");
        http_response_code(400);
    }
    echo json_encode($response) . "\n";
}

// if the request is POST, create a blog_post
else if ($request->isPost()) {

    $username = $reqVars["username"];
    // check if request contains all required variables
    if (containsAllVars($reqVars)) {
        // get the id of user
        $getUserIdQ = "select id from blog_user where username = '$username'";
        $getUserId = $db->prepare($getUserIdQ);
        $getUserId->execute();

        $user_id = $getUserId->fetch(PDO::FETCH_ASSOC);
        $id = $user_id["id"];

        // get the highest post id
        $getHighID = "select max(id) from post";
        $highQuery = $db->prepare($getHighID);

        $highQuery->execute();

        $highNum = $highQuery->fetch(PDO::FETCH_ASSOC);

        // increment the highest id number
        if ($highNum === null)
        {
            $highNum = 1;
        }
        else
        {
            $highNum["max"]++;
        }


        $sql = 'insert into post (id, user_id, post_date, post_text, extra) 
                values (:id, :user_id, :post_date, :post_text, :extra)';
        var_dump($sql);
        $success = getSuccess($highNum["max"], $id, $reqVars, $db, $sql);
        var_dump($success);

        $results = ['message' => $success ? 'Success' : 'Did not add blog post'];
        http_response_code(202);

    }
    else
    {
        $results = ['error' => true];
        http_response_code(400);
    }
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode($results) . "\n";
}
