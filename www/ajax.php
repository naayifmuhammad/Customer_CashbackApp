<?php
include('connect.php');
if (isset($_GET['e'])) {
    $e = $_GET['e'];
    $q = "select phone from customers where phone='$e'";
    $m = mysqli_query($conn, $q);
    if (mysqli_num_rows($m) > 0) {
        echo  "Number is already used";
    } else {
        echo "";
    }
}

if (isset($_GET['u'])) {
    $u = $_GET['u'];
    $q_userid = "select userid from customers where userid='$u'";
    $_q_userid = mysqli_query($conn, $q_userid);

    if (mysqli_num_rows($_q_userid) > 0) {

        echo "User ID is already in use.";
    } else {
        echo "";
    }
}
