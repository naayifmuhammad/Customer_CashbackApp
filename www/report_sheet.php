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
                        <th>Time</th>
                        <th>Userid</th>
                        <th>Purchase Amount</th>
                        <th>Cashback Payed</th>
                        <th>Referrer</th>
                        <th>Referal Payed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $path = 'reports/' . $_GET['link'];
                    if (file_exists($path)) {
                        $file = file_get_contents($path);
                        $contents = json_decode($file);
                        if ($contents == null) {

                            foreach ($contents as $content) {
                    ?>
                                <tr>
                                    <td><?php echo $content->time ?></td>
                                    <td><?php echo $content->userid ?></td>
                                    <td><?php echo '₹ ' . $content->purchase_amount ?></td>
                                    <td><?php echo '₹ ' . $content->cashback_payed ?></td>
                                    <td><?php echo $content->referrer ?></td>
                                    <td><?php echo '₹ ' . $content->referal_payed ?></td>
                                </tr>
                    <?php
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
<?php
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