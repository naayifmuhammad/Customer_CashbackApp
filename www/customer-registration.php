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
    <link rel="stylesheet" href="css/customer-registration.css">
    <title>Registration</title>
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
    <script>
        function getXMLHttp() {
            var xmlHttp;
            try {
                xmlHttp = new XMLHttpRequest();
            } catch (e) {
                try {
                    xmlHttp = new ActiveXObject("Masxml2.XMLHTTP");
                } catch (e) {
                    try {
                        xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
                    } catch (e) {
                        alert("your browser doesnot support AJAX!");
                        return false;
                    }
                }
            }
            return xmlHttp;

        }

        function getemail(e) {
            var xmlHttp = getXMLHttp();

            var ev = e.value;

            xmlHttp.onreadystatechange = function() {
                if (xmlHttp.readyState == 4) {
                    document.getElementById('span').innerHTML = xmlHttp.responseText;
                } else if (xmlHttp.readyState == 1) {
                    document.getElementById('span').innerHTML = '<span>Loading...</span>';
                }
            }
            xmlHttp.open("GET", "ajax.php?e=" + ev, true);
            xmlHttp.send();
        }

        function getID(e) {
            var xmlHttp = getXMLHttp();

            var ev = e.value;
            xmlHttp.onreadystatechange = function() {
                if (xmlHttp.readyState == 4) {
                    document.getElementById('uid').innerHTML = xmlHttp.responseText;
                } else if (xmlHttp.readyState == 1) {
                    document.getElementById('uid').innerHTML = '<span>Loading...</span>';
                }
            }
            xmlHttp.open("GET", "ajax.php?u=" + ev, true);
            xmlHttp.send();
        }
    </script>


    <div class="content-container">

        <div class="container-fluid">

            <div class="container">
                <h1>New Customer</h1>
                <form action="" method="POST">
                    <div class="field-input">
                        <input type="number" name='userid' required='true' onchange='getID(this)' placeholder="User-ID" class="field"><br><span id='uid'></span>
                    </div>
                    <div class="field-input">
                        <input type="text" name='name' required='true' placeholder="Name" class="field">
                    </div>
                    <div class="field-input">
                        <input type="number" name='phone' required='true' placeholder="Phone No" required onchange='getemail(this)' maxlength="10" class="field"><br><span id="span"></span>
                    </div>
                    <div class="field-input">
                        <input type="Address" name='address' placeholder="Address" class="field">
                    </div>
                    <div class="field-input">
                        <input type="text" name='referrer' placeholder="Referrer(Phone/ID)" class="field">
                    </div>
                    <input type="submit" name="submit" value="Add Customer" class="btn">
                </form>
            </div>

        </div>
    </div>
</body>

</html>
<!-- backend -->
<?php
if (isset($_POST['submit'])) {
    include("connect.php");
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // email and password sent from form 

        $userid = $_POST['userid'];
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $referrer = $_POST['referrer'];

        $proceed = 0;

        //check if they are referred by anyone in the first place
        if ($referrer != null) {
            //check if referrer is in the customer database;
            if (strlen($referrer) < 9) {
                $sql_check_for_userid = "SELECT * FROM  customers WHERE userid = '$referrer'";
                $results = mysqli_query($conn, $sql_check_for_userid);
                if (mysqli_num_rows($results) > 0) {
                    $proceed = 1;
?>
                    <script>
                        alert("Referrer found");
                    </script>
                <?php
                } else {
                ?>
                    <script>
                        alert("Referrer doesn't exist in the database");
                    </script>
                <?php
                }
            } else {
                $sql_check_for_phone = "SELECT `userid` FROM `customers` WHERE phone='$referrer'";
                $results = mysqli_query($conn, $sql_check_for_phone);
                if (mysqli_num_rows($results) > 0) {
                    $ref_ = mysqli_fetch_array($results, MYSQLI_ASSOC);
                    $ref_userid = $ref_['userid'];
                    $referrer = $ref_userid;
                ?>
                    <script>
                        alert("Referrer's phone found");
                    </script>
                <?php
                    $proceed = 1;
                } else {
                ?>
                    <script>
                        alert("Referrer's phone not found");
                    </script>
                <?php
                }
            }

            if ($proceed > 0) {
                $sql_add_customer = "INSERT INTO `customers`(`userid`, `name`, `phone`, `address`, `referrer`) VALUES ('$userid','$name','$phone','$address','$referrer')";
                if ($myq = mysqli_query($conn, $sql_add_customer)) {
                    $save_temp = "INSERT INTO `temp_sync`(`userid`, `name`, `phone`, `address`, `referrer`) VALUES ('$userid','$name','$phone','$address','$referrer')";
                    mysqli_query($conn, $save_temp);
                ?>
                    <script>
                        alert("Added to database");
                    </script>
                <?php
                } else {
                ?>
                    <script>
                        alert("Something went wrong with adding customer to database");
                    </script>
                <?php
                }
            }
        } else {
            $sql_add_customer = "INSERT INTO `customers`(`userid`, `name`, `phone`, `address`, `referrer`) VALUES ('$userid','$name','$phone','$address',default)";
            if ($myq = mysqli_query($conn, $sql_add_customer)) {
                $save_temp = "INSERT INTO `temp_sync`(`userid`, `name`, `phone`, `address`, `referrer`) VALUES ('$userid','$name','$phone','$address',default)";
                mysqli_query($conn, $save_temp);
                ?>
                <script>
                    alert("Added to database");
                </script>
            <?php
            } else {
            ?>
                <script>
                    alert("Something went wrong with adding customer to database");
                </script>
<?php
            }
        }
    }
}


?>
<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>

<!-- class User
 {
     function __construct($userid, $name, $phone, $address, $referrer)
     {
         $this->status = 'pending';
         $this->userid = $_POST['userid'];
         $this->name = $_POST['name'];
         $this->phone = $_POST['phone'];
         $this->address = $_POST['address'];
         $this->referrer = $_POST['referrer'];
     }
 } -->