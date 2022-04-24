<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
require_once "dblib.php";
require_once "rest.php";
require_once "validate-blog-comment.php";

$request = new RestRequest();


// returns PHP array of everything thats sent(could be nested array)
$reqVars = $request->getRequestVariables();


// connect to the DB
$db = connect();

if ($request->isGet()) {

    //$key = "username";
    //$value = $reqVars['username'];

    $key = implode(array_keys($reqVars)); // "post_id"
    $value = implode(array_values($reqVars)); // the post_id

    $sql = 'select bc.id, bc.user_id, bc.post_id, bc.comment_text, bc.comment_date,
            bu.username from blog_comment as bc join blog_user as bu on bc.user_id = bu.id
            where bc.user_id = bu.id and post_id = ' . $value .'order by bc.id';

    //$sql = 'select username, * from blog_comment where post_id = ' . $value;
    // run the query
    $query = $db->prepare($sql);
    $query->execute();
    // get the data from the DB
    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    $response = $results;
    // return to user as JSON string
    http_response_code(200);
    // send error message
    echo json_encode($response) . "\n";


} // if the request is POST, create a blog_comment
else if ($request->isPost()) {

    $username = $reqVars["username"];

    // get the id of user
    $getUserIdQ = "select id from blog_user where username = '$username'";
    $getUserId = $db->prepare($getUserIdQ);
    $getUserId->execute();

    $user_id = $getUserId->fetch(PDO::FETCH_ASSOC);
    $id = $user_id["id"];
    // get the highest post id
    $getHighID = "select max(id) from blog_comment";
    $highQuery = $db->prepare($getHighID);
    $highQuery->execute();
    $highNum = $highQuery->fetch(PDO::FETCH_ASSOC);

    // increment the highest id number
    if ($highNum === null) {
        $highNum = 1;
    } else {
        $highNum["max"]++;
    }


    $sql = 'insert into blog_comment (id, user_id, post_id, comment_text, comment_date) 
            values (:id, :user_id, :post_id, :comment_text, :comment_date)';

    $success = getSuccess($highNum["max"], $id, $reqVars, $db, $sql);

    $results = ['message' => $success? 'Success' : 'Did not add blog post'];
    $results += ['comment_id' => $highNum["max"]];
    http_response_code(202);

    header('Content-Type: application/json; charset=utf-8');

    echo json_encode($results) . "\n";
}
else if ($request->isDelete()) {

    $key = "id";
    $value = $reqVars["comment_id"];
    var_dump($value);


    // build the sql
    $sql = 'delete from blog_comment where id = :value';
    // run the query
    $query = $db->prepare($sql);
    $query->execute([$reqVars['comment_id']]);
    // get the data from the DB
    $results = $query->fetch(PDO::FETCH_ASSOC);

    $response = ["message" => "successfully deleted ".$key.":".$value];
    http_response_code(200);

    echo json_encode($response)."\n";
}

