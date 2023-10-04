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

    <div class="content-container">

        <table class='table table-striped'>
            <thead>
                <tr>
                    <th>Date created</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $path = 'reports/';
                $files = preg_grep('/^([^.])/', scandir($path));
                foreach ($files as  $file) {
                    $link = 'report_sheet.php?link=' . $file;
                ?>
                    <tr>
                        <td><a class="links" href="<?php echo $link ?>">
                                <?php
                                $x = basename($file, '.json');
                                echo $x ?></td>
                        </a>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>


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