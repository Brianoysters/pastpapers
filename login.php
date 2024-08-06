<?php
session_start();
if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {
    $username = $_POST[ 'username' ];
    $password = $_POST[ 'password' ];

    $mysqli = new mysqli( 'localhost', 'root', '', 'campus_papers' );
    if ( $mysqli->connect_error ) {
        die( 'Connection Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error );
    }

    $query = 'SELECT * FROM users WHERE username = ?';
    $stmt = $mysqli->prepare( $query );
    $stmt->bind_param( 's', $username );
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ( $user && password_verify( $password, $user[ 'password' ] ) ) {
        $_SESSION[ 'username' ] = $username;
        $_SESSION[ 'role' ] = $user[ 'role' ];
        header( 'Location: index.html' );
    } else {
        echo 'Invalid username or password.';
    }
    $stmt->close();
    $mysqli->close();
}
?>
