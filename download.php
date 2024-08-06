<?php
session_start();
if ( !isset( $_SESSION[ 'username' ] ) ) {
    header( 'Location: login.html' );
    exit();
}

if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {
    $username = $_SESSION[ 'username' ];
    $paper = $_POST[ 'paper' ];

    $mysqli = new mysqli( 'localhost', 'root', '', 'campus_papers' );
    if ( $mysqli->connect_error ) {
        die( 'Connection Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error );
    }

    $query = 'SELECT subscription_plan, subscription_expiry, downloads_this_week FROM users WHERE username = ?';
    $stmt = $mysqli->prepare( $query );
    $stmt->bind_param( 's', $username );
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ( $user && strtotime( $user[ 'subscription_expiry' ] ) > time() ) {
        $allow_download = false;

        if ( $user[ 'subscription_plan' ] == 'weekly' && $user[ 'downloads_this_week' ] < 1 ) {
            $allow_download = true;
            $query = 'UPDATE users SET downloads_this_week = downloads_this_week + 1 WHERE username = ?';
            $stmt = $mysqli->prepare( $query );
            $stmt->bind_param( 's', $username );
            $stmt->execute();
        } elseif ( $user[ 'subscription_plan' ] == 'monthly' ) {
            $allow_download = true;
        }

        if ( $allow_download ) {
            $file = 'uploads/' . $paper;
            if ( file_exists( $file ) ) {
                header( 'Content-Description: File Transfer' );
                header( 'Content-Type: application/octet-stream' );
                header( 'Content-Disposition: attachment; filename=' . basename( $file ) );
                header( 'Expires: 0' );
                header( 'Cache-Control: must-revalidate' );
                header( 'Pragma: public' );
                header( 'Content-Length: ' . filesize( $file ) );
                flush();
                readfile( $file );
                exit;
            } else {
                echo 'File does not exist.';
            }
        } else {
            echo 'Download limit reached.';
        }
    } else {
        echo 'No valid subscription.';
    }
    $stmt->close();
    $mysqli->close();
}
?>
