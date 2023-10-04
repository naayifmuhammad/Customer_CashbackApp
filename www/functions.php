<?php
global $hooks;
function Left_Sidebar()
{
?>
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
<?php
}
$hooks->add_action('loggedin_user_menu', 'Left_Sidebar');
