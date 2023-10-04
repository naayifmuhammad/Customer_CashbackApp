<?php
include_once('php-hooks.php');
include_once('connect.php');
include_once('functions.php');
global $hooks;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css\css\bootstrap.min.css">
    <link href="css\all.css" rel="stylesheet">
    <link rel="stylesheet" href="css/cashback.css">
    <title>Cashback</title>
</head>

<body>

    <div class="sidebar-container">
        <div class="sidebar-logo">
        </div>

        <div class="sidebar-content-container">
            <ul class="sidebar-navigation">
                <?php
                $hooks->do_action('loggedin_user_menu');
                ?>
            </ul>
        </div>
    </div>

    <div class="content-container">

        <div class="container-fluid">

            <div class="container">
                <h1>Cashback</h1>
                <form action="" method="POST">
                    <div class="field-input">
                        <input type="number" name='userid' required='true' placeholder="User-ID" class="field">
                    </div>
                    <div class="field-input">
                        <input type="number" name='amount' required='true' placeholder="Purchase Amount" class="field">
                    </div>
                    <input type="submit" name="submit" value="Submit" class="btn">
                </form>
            </div>
        </div>
    </div>
</body>

</html>
<?php
if (isset($_POST['submit'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $userid = $_POST['userid'];

        $purchase_amount = $_POST['amount'];
        $cashback_percentage = 10;
        $referal_percentage = 5;
        $cashback_earned = round(($cashback_percentage / 100) * $purchase_amount);
        $referal_earned = round(($referal_percentage / 100) * $purchase_amount);



        include("connect.php");

        //check if the user exists in the system before giving cashback.

        $check_for_user = "SELECT userid FROM  customers WHERE userid = '$userid'";
        $results = mysqli_query($conn, $check_for_user);
        //if user is in the system.
        if (mysqli_num_rows($results) < 1) {
?>
            <script>
                alert("User doesn't exist in the system. Please add the new user first");
            </script>
<?php
        } else {
            //give the user cashback and compensate the referrer if one exist.

            //Get the user's current cashback amount and add the latest purchase cashback to it.
            $q_customer_cashback = "SELECT `cashback_earnings` FROM `customers` WHERE userid='$userid'";
            $customer_cashback = mysqli_query($conn, $q_customer_cashback);
            $_cashback = mysqli_fetch_array($customer_cashback, MYSQLI_ASSOC);
            $current_cashback = $_cashback['cashback_earnings'];

            //calculate the added cashback
            $added_cashback = $current_cashback + $cashback_earned;

            $q_add_cashback = "UPDATE customers SET cashback_earnings='$added_cashback' where userid=$userid";
            if (mysqli_query($conn, $q_add_cashback)) {
                add_toSalesLog($purchase_amount, $userid, $conn);
                add_cashback_toLog($cashback_earned, $userid, $conn);
                $success_msg = "Amount " . strval($cashback_earned) . " added to user " . strval($userid);
                pay_status($success_msg);
            } else {
                $msg = "Something went wrong. Cashback not added";
                pay_status($msg);
            }



            //referal payment
            $check_for_referrer = "SELECT `referrer` FROM `customers` WHERE userid='$userid'";
            $referrers = mysqli_query($conn, $check_for_referrer);
            if (mysqli_num_rows($referrers) > 0) {
                $ref_ = mysqli_fetch_array($referrers, MYSQLI_ASSOC);
                $ref_userid = $ref_['referrer'];
                if ($ref_userid != null and $ref_userid != 0) {

                    //grab the current referal fee the customer has
                    $q_referal_fee = "SELECT `referal_earnings` FROM `customers` WHERE userid='$ref_userid'";
                    $_referal_fee = mysqli_query($conn, $q_referal_fee);
                    $_current_referal_fee = mysqli_fetch_array($_referal_fee, MYSQLI_ASSOC);
                    $current_referal_fee = $_current_referal_fee['referal_earnings'];

                    //calculate the added referal earnings
                    $added_referal_fee = $current_referal_fee + $referal_earned;

                    //Calculate and insert the added referal payment to their current referal balance.
                    $updated_referal = "UPDATE `customers` SET `referal_earnings`='$added_referal_fee' WHERE userid = '$ref_userid'";
                    if (mysqli_query($conn, $updated_referal)) {
                        add_referal_log($referal_earned, $ref_userid, $conn);
                        $msg = "Amount " . $referal_earned . ' payed as referal to userid ' . $ref_userid;
                        pay_status($msg);
                    }
                }
            }
        }
    }
}

//some useful functions


?>
<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
<!-- Log transactions -->
<?php
function add_toSalesLog($_purchaseAmount, $_userid, $conn)
{
    $qAddToSalesLog = "INSERT INTO `sales_log`(`userid`, `purchase_amount`) VALUES ('$_userid','$_purchaseAmount')";
    mysqli_query($conn, $qAddToSalesLog);
}
function add_cashback_toLog($cashback_earned, $userid, $conn)
{
    $q_Log_cashback = "INSERT INTO `cashback_referal`(`userid`, `cashback_earned`) VALUES ('$userid','$cashback_earned')";
    mysqli_query($conn, $q_Log_cashback);
}
function add_referal_log($referalAMount, $ref_userid, $conn)
{
    $q_Log_cashback = "INSERT INTO `cashback_referal`(`userid`, `cashback_earned`) VALUES ('$ref_userid','$referalAMount')";
    mysqli_query($conn, $q_Log_cashback);
}

function pay_status($output, $with_script_tags = true)
{
    $js_code = 'alert(' . json_encode($output, JSON_HEX_TAG) .
        ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}
?>