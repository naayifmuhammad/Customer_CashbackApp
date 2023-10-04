<?php
include_once('php-hooks.php');
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
    <link rel="stylesheet" type="text/css" href="table\datatables.min.css" />
    <script type="text/javascript" src="table\datatables.min.js"></script>
    <link href="css\all.css" rel="stylesheet">
    <link rel="stylesheet" href="css/customer.css">
    <title>Home</title>
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

            <!-- The info cards go here -->


            <!-- <div class="cards-grid">
                <button class="button-card" onclick="window.location.href='customer-registration.php';">
                    placing the card inside a div tag to make it a link
            <div class="card">
                NEW CUSTOMER
            </div>
            </button>
            <button class="button-card" onclick="window.location.href='cashback.php';">
                placing the card inside a div tag to make it a link
                <div class="card">
                    CASH BACK
                </div>
            </button>

        </div> -->

            <ul>
                <li class="border"></li>
                <li><a href="customer-registration.php">New Customer</a></li>
                <li><a href="cashback.php">Cashback</a></li>
                <li><a href="redeem-cashback.php">Redeem Cashback</a></li>
                <li><a href="return.php">Return</a></li>
            </ul>

        </div>

        <!-- table and button -->

        <div class="customer-table">
            <form action="" method="POST">
                <input type="submit" class='s_table_btn' name='btn' value='Show customer data'>
            </form>

            <?php
            if (isset($_POST['btn'])) {
            ?>
                <div class="table-container">
                    <table class="table table-striped table-hover datatable">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">User-ID</th>
                                <th scope="col">Name</th>
                                <th scope="col">Referals Made</th>
                                <th scope="col">Cashback</th>
                                <th scope="col">Referal Earnings</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $userCount = 0;
                            foreach (get_user_data_all() as $user) {
                                $userCount += 1;
                            ?>
                                <tr>
                                    <th scope="row"><?php echo $userCount ?></th>
                                    <td><?php echo $user["userid"]  ?></td>
                                    <td><?php echo $user['name'] ?></td>
                                    <td><?php echo referrals_made($user['userid'])  ?></td>
                                    <td><?php echo "₹​" . $user["cashback_earnings"]  ?></td>
                                    <td><?php echo "₹​" . $user["referal_earnings"]  ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
        </div>
    <?php
            }

    ?>
    </div>

</body>

</html>
<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>


<?php
//some functions
function get_user_data_all()
{
    include('connect.php');
    $q_user_data = "SELECT * FROM customers";
    $result = mysqli_query($conn, $q_user_data);
    return $result;
}

function referrals_made($userid)
{
    include('connect.php');
    $checkforname = "SELECT `userid` FROM `customers` WHERE referrer='$userid'";
    $name = mysqli_query($conn, $checkforname);
    return (mysqli_num_rows($name));
}

?>
<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    jQuery(document).ready(function() {
        jQuery('.datatable').DataTable({
            select: true,
        });
    });
</script>