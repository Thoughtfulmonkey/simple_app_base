<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> 
        <style>
            .container{
                max-width: 720px;
            }
            .success{
                color: #11cb11;
            }
        </style>
    </head>

    <body>

        <div class="container">
        
<?php
            require '../api/utils.php';

            // Check for form submission
            if ($_SERVER['REQUEST_METHOD'] == 'POST'){

                echo "<h2>Setting up...</h2>";

                // Check for all requirements
                $servername = $_POST['dbAddress'];
                $username = $_POST['dbUser'];
                $password = $_POST['dbPassword'];
                $dbname = $_POST['dbName'];
                $prefix = $_POST['dbPrefix'];

                $adminUsername = $_POST['setAdmin'];
                $adminPassword = $_POST['setPassword'];

                try {

                    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                
                    // set the PDO error mode to exception
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                    $stmt = $conn->prepare('CREATE TABLE `'.$prefix.'invite_table` (
                        `uid` int UNSIGNED NOT NULL AUTO_INCREMENT,
                        `invite_string` varchar(12) NOT NULL,
                        `expires` date NOT NULL,
                        PRIMARY KEY (uid)
                    );');
                    $stmt->execute();

                    echo "<p>Created invite table. <span class='success'>✔</span></p>";

                    $stmt = $conn->prepare('CREATE TABLE `'.$prefix.'roles` (
                        `roleid` int UNSIGNED NOT NULL AUTO_INCREMENT,
                        `rolename` varchar(10) NOT NULL,
                        PRIMARY KEY (roleid)
                    );');
                    $stmt->execute();

                    echo "<p>Created roles table. <span class='success'>✔</span></p>";

                    $stmt = $conn->prepare('INSERT INTO `'.$prefix.'roles` (`roleid`, `rolename`) VALUES
                        (1, "admin"),
                        (2, "limited"),
                        (3, "guest");
                    ');
                    $stmt->execute();

                    echo "<p>Added default roles. <span class='success'>✔</span></p>";

                    $stmt = $conn->prepare('CREATE TABLE `'.$prefix.'users` (
                        `uid` int UNSIGNED NOT NULL AUTO_INCREMENT,
                        `public_id` varchar(10) NOT NULL,
                        `username` varchar(30) NOT NULL,
                        `password` varchar(255) NOT NULL,
                        `role` tinyint UNSIGNED NOT NULL,
                        PRIMARY KEY (uid)
                    );');
                    $stmt->execute();

                    echo "<p>Created users table. <span class='success'>✔</span></p>";

                    // Create user
                    $publicID = gen_pubic_id();
                    $password_hash = password_hash($adminPassword, PASSWORD_BCRYPT);
                    $stmt = $conn->prepare('INSERT INTO `'.$prefix.'users` (public_id, username, password, role) VALUES(:pubid, :username, :password, 2)');
                    $stmt->bindParam(':pubid', $publicID, PDO::PARAM_STR);
                    $stmt->bindParam(':username', $adminUsername, PDO::PARAM_STR);
                    $stmt->bindParam(':password', $password_hash, PDO::PARAM_STR);
                    $stmt->execute();

                    echo "<p>Added admin user. <span class='success'>✔</span></p>";

                    $stmt = $conn->prepare('CREATE TABLE `'.$prefix.'settings` (
                        `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
                        `name` varchar(20) NOT NULL,
                        `value` varchar(20) NOT NULL,
                        PRIMARY KEY (id)
                    );');
                    $stmt->execute();

                    echo "<p>Created settings table. <span class='success'>✔</span></p>";

                    $stmt = $conn->prepare("INSERT INTO `".$prefix."settings` (`id`, `name`, `value`) VALUES (1, 'allow_guests', 'no')");
                    $stmt->execute();

                    echo "<p>Set allow_guests to no <span class='success'>✔</span></p>";
                    

                    $connfile = fopen("../api/connection.php", "w");

                    fwrite($connfile, '<?php'.PHP_EOL);
                    fwrite($connfile, '$servername = "'.$servername.'";'.PHP_EOL);
                    fwrite($connfile, '$dbuser = "'.$username.'";'.PHP_EOL);
                    fwrite($connfile, '$dbpassword = "'.$password.'";'.PHP_EOL);
                    fwrite($connfile, '$dbname = "'.$dbname.'";'.PHP_EOL);
                    fwrite($connfile, '$prefix = "'.$prefix.'";'.PHP_EOL);
                    fwrite($connfile, '?>'.PHP_EOL);

                    fclose($connfile);

                    echo "<p>Wrote connection file. <span class='success'>✔</span></p>";

                    echo "<p>Setup is complete. Remove this file from your server.</p>";
                
                } catch(PDOException $e) {
                    echo '<p>'. $e->getMessage().'</p>';
                }

            }
            else{

?>
            <form class="row g-3 mt-5" method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">

                <h2>Database</h2>

                <div class="form-group">
                    <label for="dbAddressLabel">Server address</label>
                    <input type="text" class="form-control" id="dbAddress" name="dbAddress" aria-describedby="dbAddressLabel" placeholder="localhost">
                </div>
                <div class="form-group">
                    <label for="dbNameLabel">Database name</label>
                    <input type="text" class="form-control" id="dbName" name="dbName" aria-describedby="dbNameLabel" placeholder="Database name">
                </div>
                <div class="form-group">
                    <label for="dbPrefixLabel">Table prefix</label>
                    <input type="text" class="form-control" id="dbPrefix" name="dbPrefix" aria-describedby="dbPrefixLabel" placeholder="Leave blank if none">
                </div>
                <div class="form-group">
                    <label for="dbUserLabel">User</label>
                    <input type="text" class="form-control" id="dbUser" name="dbUser" aria-describedby="dbUserLabel" placeholder="User name">
                </div>
                <div class="form-group">
                    <label for="dbPasswordLabel">Password</label>
                    <input type="text" class="form-control" id="dbPassword" name="dbPassword" aria-describedby="dbPasswordLabel" placeholder="Password">
                </div>
                
                <h2>Admin user</h2>

                <div class="form-group">
                    <label for="setAdminLabel">Admin username</label>
                    <input type="text" class="form-control" id="setAdmin" name="setAdmin" aria-describedby="setAdminLabel" placeholder="Admin">
                </div>

                <div class="form-group">
                    <label for="setPasswordLabel">Admin password</label>
                    <input type="text" class="form-control" id="setPassword" name="setPassword" aria-describedby="setPasswordLabel" placeholder="Record this somewhere">
                </div>
                
                <button type="submit" class="btn btn-primary mt-5">Submit</button>

            </form>
<?php
            }
?>
        
        </div>
    </body>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</html>