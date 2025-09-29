<?php

function check_login() {
    session_start();
    if (!isset($_SESSION["user_id"])) {
        header("Location: ../login.php");
        exit();
    }
}

function get_user_name($conn, $user_id) {
    $sql = "SELECT nama FROM users WHERE id = $user_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return $result->fetch_assoc()["nama"];
    } else {
        return "Unknown User";
    }
}

function is_admin() {
    return isset($_SESSION["role"]) && $_SESSION["role"] === 'admin';
}

?>