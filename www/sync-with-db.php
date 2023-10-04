<?php
include_once('php-hooks.php');
include('connect.php');
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

    <div class="content-container">

        <table class='table table-striped'>
            <thead>
                <tr>
                    <th>Logs</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $_qGetDates = "SELECT DISTINCT `date` from sales_log";
                $_dates = mysqli_query($conn, $_qGetDates);
                $dates = mysqli_fetch_array($_dates, MYSQLI_ASSOC);
                foreach ($dates as $day) {
                ?>
                    <td><a href="sync_single.php?link=<?php echo $day ?>"><?php echo $day ?></a></td>
                <?php
                }

                ?>
            </tbody>
        </table>


    </div>
</body>

</html>
<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>