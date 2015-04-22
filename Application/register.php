<?php

include 'headers.php';
include 'sessions.php';
include 'config.php';
include 'password.php';

// get POST information from login form
$email=$_POST["email"];
$username=$_POST["username"];
$password=$_POST["password"];

// open connection to the database
include 'opendb.php';

try{
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        //header('Location: /registration.php?message=' . urlencode('Email provided was invalid!'));
        exit();
    }else if(!ctype_alnum($username)){
        //header('Location: /registration.php?message=' . urlencode('Username may only contain alphanumeric characters!'));
        exit();
    }else if(ctype_alnum($password)){
        //header('Location: /registration.php?message=' . urlencode('Password must contain atleast one non-alphanumeric characters!'));
        exit();
    }else if(strlen($password) < 8){
        //header('Location: /registration.php?message=' . urlencode('Password too short!'));
        exit();
    }

    $query = $db->prepare("SELECT username FROM users WHERE username=':username'");
    $query->bindParam(':username', $username, strlen($username));
    $query->execute();

    if($query->rowCount() > 0) {
        //header('Location: /registration.php?message=' . urlencode('Username taken!'));
        exit();
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $insert = $db->prepare("INSERT INTO users (email, username, password) VALUES ('$email', '$username', '$hash')");
    $insert->execute();
	
    // register user
    if ($insert->rowCount()) {
    authenticate($email, $password);
    } else {
        //header('Location: /registration.php?message=' . urlencode(mysql_error($conn)));
    } 
} catch(Exception $e) {
    header("Location: /registration.php?message=" . urlencode("Error: " . $e));
}

// close connection to the database
include 'closedb.php';

?>
