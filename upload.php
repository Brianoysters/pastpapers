<?php
session_start();
if ( !isset( $_SESSION[ 'username' ] ) || $_SESSION[ 'role' ] != 'owner' ) {
    header( 'Location: login.html' );
    exit();
}

if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {
    if ( isset( $_FILES[ 'file' ] ) && $_FILES[ 'file' ][ 'error' ] == 0 ) {
        $allowed = [ 'pdf' => 'application/pdf' ];
        $filename = $_FILES[ 'file' ][ 'name' ];
        $filetype = $_FILES[ 'file' ][ 'type' ];
        $filesize = $_FILES[ 'file' ][ 'size' ];
        $unit_id = $_POST[ 'unit' ];

        $ext = pathinfo( $filename, PATHINFO_EXTENSION );
        if ( !array_key_exists( $ext, $allowed ) || $filesize > 5000000 ) {
            die( 'Error: Invalid file format or file too large.' );
        }

        if ( in_array( $filetype, $allowed ) ) {
            if ( move_uploaded_file( $_FILES[ 'file' ][ 'tmp_name' ], 'uploads/' . $filename ) ) {
                $mysqli = new mysqli( 'localhost', 'root', '', 'campus_papers' );
                if ( $mysqli->connect_error ) {
                    die( 'Connection Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error );
                }

                $query = 'INSERT INTO past_papers (unit_id, filename) VALUES (?, ?)';
                $stmt = $mysqli->prepare( $query );
                $stmt->bind_param( 'is', $unit_id, $filename );
                $stmt->execute();
                $stmt->close();
                $mysqli->close();

                echo 'Your file was uploaded successfully.';
            } else {
                echo 'File is not uploaded.';
            }
        } else {
            echo 'Error: Invalid file format.';
        }
    } else {
        echo 'Error: ' . $_FILES[ 'file' ][ 'error' ];
    }
}
?>
