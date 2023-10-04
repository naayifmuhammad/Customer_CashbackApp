<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css\all.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <title>Hanna-Login</title>
</head>

<body>
    <div class="container">
        <h1>HANNA</h1>
        <form action="" method="POST">
            <div class="field-input">
                <input type="password" name='password' placeholder="password" class="field">
            </div>
            <input type="submit" name="submit" value="login" class="btn">
        </form>
    </div>
</body>

</html>
<?php
if (isset($_POST['submit'])) {
    include("connect.php");
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // email and password sent from form 
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $sql = "SELECT * FROM auth WHERE username = 'hanna' and password = '$password'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $count = mysqli_num_rows($result);
        if ($count > 0) {
            //header("location: hanna-home.php");
?>
            <script>
                window.location.href = "hanna-home.php";
            </script>
<?php
        }
    }
}
?>