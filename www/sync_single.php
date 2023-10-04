<?php
include_once('php-hooks.php');
include_once('functions.php');
include_once('connect.php');
include_once('connect_external_DB.php');
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
    <link rel="stylesheet" href="css/reports.css">
    <title>Reports</title>
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

    <div class="content-report-sheet">
        <button class="back" onclick="history.go(-1);">Back</button>
        <div class="log-table">
            <table class='table table-striped'>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Total cashback earnings</th>
                        <th>Total Referal Earnings</th>
                        <th>Synced</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q_getLog = "select userid from cashback_referal";
                    $customers = mysqli_query($conn, $q_getLog);
                    var_dump($customers);
                    $temp_id_storage = array();

                    foreach ($customers as $customer) {
                        if ($temp_id_storage == null) {
                            array_push($temp_id_storage, $customer);
                        }

                        //add cashbacks
                        $q_getTotalCashback = "Select `cashback_earned` from cashback_referal where userid = $customer";
                        $c_amount = mysqli_query($conn, $q_getTotalCashback);
                        $c_amount = mysqli_fetch_array($c_amount, MYSQLI_ASSOC);
                        //add referal
                        $q_getTotalReferal = "Select referal_earned from cashback_referal where userid = $customer";
                        $r_amount = mysqli_query($conn, $q_getTotalReferal);
                        $r_amount = mysqli_fetch_array($r_amount, MYSQLI_ASSOC);
                    }
                    var_dump($temp_id_storage);
                    ?>
                </tbody>
            </table>
        </div>
        <div class="btn-container">
            <form action="" method="POST">
                <input type="submit" name='submit' class='sync-btn' value="Sync">
            </form>
        </div>
    </div>
</body>

</html>
<?php
if (isset($_POST['submit'])) {
    startSync();
}
function startSync()
{
    $failed_records = null;
    include('connect_external_DB.php');
    syncUsers_first($conn_ex);
    $path = 'reports/' . $_GET['link'];
    if (file_exists($path)) {
        $file = file_get_contents($path);
        $contents = json_decode($file);
        if ($contents == null) {
            $contents = array();
        }
        foreach ($contents as $customers) {
            $totalCashback = 0;
            $totalReferal = 0;
            $userid = null;
            foreach ($customers as $transactions) {
                if ($userid == null) {
                    $userid = $transactions->userid;
                }
                if ($transactions->isCustomer == true) {
                    $totalCashback += $transactions->cashback_payed;
                    //$totalPurchaseAmount += $transactions->purchase_amount;
                } else {
                    $totalReferal += $transactions->referal_payed;
                }
            }
            if (!syncWithDB($conn_ex, $userid, $totalCashback, $totalReferal)) {
                $failed_records[] = $customers;
                $final_data = json_encode($failed_records);
                file_put_contents($path, $final_data);
            }
        }
    }
}
function syncWithDB($conn_ex, $userid, $totalCashback, $totalReferal)
{
    $confirmUserisIntheDB = "SELECT userid from customers where userid = $userid";
    $check = mysqli_query($conn_ex, $confirmUserisIntheDB);
    if (mysqli_num_rows($check) > 0) {

        $getCurrentEarnings = "Select `referal_earnings`, `cashback_earnings` from customers where userid=$userid";
        if ($result = mysqli_query($conn_ex, $getCurrentEarnings)) {
            $result_row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $current_cashback = $result_row['cashback_earnings'] + $totalCashback;
            $current_referal = $result_row['referal_earnings'] + $totalReferal;
            $q_SyncWithDB = "UPDATE `customers` SET `referal_earnings`=$current_referal,`cashback_earnings`=$current_cashback WHERE userid=$userid";
            if (mysqli_query($conn_ex, $q_SyncWithDB)) {

?>
                <script>
                    if (typeof(container) === undefined) {
                        let container = document.getElementById('<?php echo $userid ?>');
                    } else {
                        container = document.getElementById('<?php echo $userid ?>');
                    }
                    container.style.color = '#39ff32';
                </script>
        <?php
                return true;
            }
        }
    } else {
        ?>
        <script>
            if (typeof(container) === undefined) {
                let container = document.getElementById('<?php echo $userid ?>');
            } else {
                container = document.getElementById('<?php echo $userid ?>');
            }
            container.style.color = '#fd2c25';
        </script>
<?php
        return false;
    }
}


function syncUsers_first($conn_ex)
{
    $new_file_name = strval(date("d-m-Y")) . ".json";
    $path = "pending/" . $new_file_name;
    fopen($path, 'a');
    if (file_exists($path)) {
        $file_content = file_get_contents($path);
        $userGroup = json_decode($file_content, true);
        if ($userGroup == null) return;
        foreach ($userGroup['WithReferrer'] as $user) {
            $insertQuery = "INSERT INTO `customers`(`userid`, `name`, `phone`, `address`, `referrer`) VALUES ('$user[userid]','$user[name]','$user[phone]','$user[address]','$user[referrer]')";
            mysqli_query($conn_ex, $insertQuery);
        }
        foreach ($userGroup['WithoutReferrer'] as $user) {
            $_insertQuery = "INSERT INTO `customers`(`userid`, `name`, `phone`, `address`, `referrer`) VALUES ('$user[userid]','$user[name]','$user[phone]','$user[address]',default)";
            mysqli_query($conn_ex, $_insertQuery);
        }
        $nullData = null;
        file_put_contents($path, $nullData);
    }
}





function pop_up($output, $with_script_tags = true)
{
    $js_code = 'alert(' . json_encode($output, JSON_HEX_TAG) .
        ');';
    if ($with_script_tags) {
        $js_code = '<script> ' . $js_code . '</script>';
    }
    echo $js_code;
}
?>
<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>