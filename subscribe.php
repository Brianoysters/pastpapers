<?php
session_start();
if ( !isset( $_SESSION[ 'username' ] ) ) {
    header( 'Location: login.html' );
    exit();
}

if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {
    $plan = $_POST[ 'plan' ];
    $payment = $_POST[ 'payment' ];
    $username = $_SESSION[ 'username' ];

    $mysqli = new mysqli( 'localhost', 'root', '', 'campus_papers' );
    if ( $mysqli->connect_error ) {
        die( 'Connection Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error );
    }

    $expiry_date = $plan == 'weekly' ? date( 'Y-m-d', strtotime( '+1 week' ) ) : date( 'Y-m-d', strtotime( '+1 month' ) );
    $query = 'UPDATE users SET subscription_plan = ?, subscription_expiry = ?, downloads_this_week = 0 WHERE username = ?';
    $stmt = $mysqli->prepare( $query );
    $stmt->bind_param( 'sss', $plan, $expiry_date, $username );
    if ( $stmt->execute() ) {
        echo 'Subscription successful.';
    } else {
        echo 'Error: Could not update subscription.';
    }
    $stmt->close();
    $mysqli->close();
}
?>
