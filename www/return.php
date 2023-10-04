<?php
session_start();
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
    <title>Purchase Return</title>
</head>

<body>

    <div class="sidebar-container">
        <div class="sidebar-logo">
        </div>

        <div class="sidebar-content-container">
            <ul class="sidebar-navigation">
                <li>
                    <a href="hanna-home.php">
                        <i class="fa fa-chart-line"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="customer.php">
                        <i class="fa fa-users" aria-hidden="true"></i> Customer
                    </a>
                </li>
                <li>
                    <a href="reports.php">
                        <i class="fa fa-clipboard-list" aria-hidden="true"></i> Reports
                    </a>
                </li>
                <li>
                    <a href="sync-with-db.php">
                        <i class="fa fa-server" aria-hidden="true"></i> Sync
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="content-container">

        <div class="container-fluid">

            <div class="container">
                <h1>Return</h1>
                <form action="" method="POST">
                    <div class="field-input">
                        <input type="number" name='userid' required='true' placeholder="User-ID" class="field">
                    </div>
                    <input type="submit" name="submit" value="Check" class="btn">
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
            //since user exists in the database. redeem the amount from purchase. 
            fetch_user_data($userid);
        }
    }
}

//some useful functions

function console_log($output, $with_script_tags = true)
{
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
        ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
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
function fetch_user_data($userid)
{
    include('connect.php');
    $q_get_db_data = "SELECT `userid`, `name`, `referal_earnings`,`cashback_earnings`,`referrer`, `phone` from customers where userid=$userid";
    if ($user_data = mysqli_query($conn, $q_get_db_data)); {
        $user = mysqli_fetch_array($user_data, MYSQLI_ASSOC);
        $_SESSION['user'] = $user;

        ?>
        <div class="customer-table">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">User-ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Cashback</th>
                        <th scope="col">Referal Earnings</th>
                        <th scope="col">Total Available Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row"><?php echo $user['userid'] ?></th>
                        <td><?php echo $user["name"]  ?></td>
                        <td><?php echo "₹​" . $user['cashback_earnings'] ?></td>
                        <td><?php echo "₹​" . $user['referal_earnings']  ?></td>
                        <td><?php
                            $total_earnings = $user['cashback_earnings'] + $user['referal_earnings'];
                            echo "₹​" . $total_earnings; ?></td>
                    </tr>
                    <?php
                    ?>
                </tbody>
            </table>
            <form action="" method="POST">
                <div class="field-input">
                    <input type="number" name='amount' on required='true' placeholder="Return" class="field">
                </div>
                <input type="submit" name="return-btn" value="Redeem from purchase" class="btn">
            </form>
        </div>
<?php
    }
}
if (isset($_POST['return-btn'])) {
    checkReturn($_POST['amount'], false);
}

function checkReturn($amount, $isReferrer)
{
    if (!$isReferrer) {
        $user = $_SESSION['user'];
        $userid = $user["userid"];
        $c_earnings = $user['cashback_earnings'];
        $r_earnings = $user['referal_earnings'];
        $ref_userid = $user['referrer'];
        include('connect.php');
        $_return_amount = $amount;
        $cashback_percentage = 10;
        //$referal_percentage = 5;
        $cashbackAmountToDeduct = ($cashback_percentage / 100) * $_return_amount;
        //$referalAmountToDeduct = ($referal_percentage / 100) * $_return_amount;
        if ($ref_userid != null) {
            getRefDetails($ref_userid);
            checkReturn($amount, true);
        }
        if ($cashbackAmountToDeduct <= ($c_earnings + $r_earnings)) {

            $c_earnings = $c_earnings - $cashbackAmountToDeduct;
            if ($c_earnings >= 0) {
                $updated_balance = "UPDATE `customers` SET `cashback_earnings`='$c_earnings' WHERE userid = '$userid'";

                if (mysqli_query($conn, $updated_balance)) {

                    $msg = "Amount " . "₹​" . $cashbackAmountToDeduct . ' deducted from userid: ' . $userid;
                    status($msg);
                } else {
                    $msg = "Something went wrong try again later";
                }
            } else {
                $r_earnings = $r_earnings + $c_earnings;
                $c_earnings = 0;
                if ($r_earnings >= 0) {
                    $updated_balance = "UPDATE `customers` SET `cashback_earnings`= $c_earnings, `referal_earnings` = $r_earnings WHERE userid = '$userid'";

                    if (mysqli_query($conn, $updated_balance)) {

                        $msg = "Amount " . "₹​" . $cashbackAmountToDeduct . ' deducted from userid: ' . $userid;
                        status($msg);
                    } else {
                        $msg = "Something went wrong try again later";
                        status($msg);
                    }
                }
            }
        } else {
            $msg = "You don't have sufficient balance!";
            status($msg);
        }
        session_destroy();
        //Deduct the cashback made earlier from the referrer too
    } else {
        $user = $_SESSION['ref_user'];
        //$name = $user['name'];
        $userid = $user['userid'];
        $c_earnings = $user['cashback_earnings'];
        $r_earnings = $user['referal_earnings'];
        include('connect.php');
        $_return_amount = $amount;
        //$cashback_percentage = 10;

        $referal_percentage = 5;
        //$cashbackAmountToDeduct = ($cashback_percentage / 100) * $_return_amount;

        $referalAmountToDeduct = ($referal_percentage / 100) * $_return_amount;


        if ($referalAmountToDeduct <= ($c_earnings + $r_earnings)) {

            $c_earnings = $c_earnings - $referalAmountToDeduct;
            if ($c_earnings >= 0) {
                $updated_balance = "UPDATE `customers` SET `cashback_earnings`='$c_earnings' WHERE userid = '$userid'";

                if (mysqli_query($conn, $updated_balance)) {

                    $msg = "Amount " . "₹​" . $referalAmountToDeduct . ' deducted from userid: ' . $userid;
                    status($msg);
                } else {
                    $msg = "Something went wrong try again later";
                }
            } else {
                $r_earnings = $r_earnings + $c_earnings;
                $c_earnings = 0;
                if ($r_earnings >= 0) {
                    $updated_balance = "UPDATE `customers` SET `cashback_earnings`= $c_earnings, `referal_earnings` = $r_earnings WHERE userid = '$userid'";

                    if (mysqli_query($conn, $updated_balance)) {

                        $msg = "Amount " . "₹​" . $referalAmountToDeduct . ' deducted from userid: ' . $userid;
                        status($msg);
                    } else {
                        $msg = "Something went wrong try again later";
                        status($msg);
                    }
                }
            }
        } else {
            $msg = "You don't have sufficient balance!";
            status($msg);
        }
    }
}

function status($output, $with_script_tags = true)
{
    $js_code = 'alert(' . json_encode($output, JSON_HEX_TAG) .
        ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}

//function to get the details of the referrer to deduct the money from
function getRefDetails($userid)
{
    include('connect.php');
    $q_get_db_data = "SELECT `userid`, `referal_earnings`,`cashback_earnings` from customers where userid=$userid";
    if ($user_data = mysqli_query($conn, $q_get_db_data)); {
        $user = mysqli_fetch_array($user_data, MYSQLI_ASSOC);
        $_SESSION['ref_user'] = $user;
    }
}
?>

<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>