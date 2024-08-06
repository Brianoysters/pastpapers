<?php
if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {
    $new_username = $_POST[ 'username' ];
    $new_password = password_hash( $_POST[ 'password' ], PASSWORD_DEFAULT );
    $role = $_POST[ 'role' ];

    $mysqli = new mysqli( 'localhost', 'root', '', 'campus_papers' );
    if ( $mysqli->connect_error ) {
        die( 'Connection Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error );
    }

    $query = 'INSERT INTO users (username, password, role) VALUES (?, ?, ?)';
    $stmt = $mysqli->prepare( $query );
    $stmt->bind_param( 'sss', $new_username, $new_password, $role );
    if ( $stmt->execute() ) {
        echo 'Registration successful.';
    } else {
        echo 'Error: Could not register user.';
    }
    $stmt->close();
    $mysqli->close();
}
?>
