<?php
// Redirect to homepage if user tries to open this page directly
if (!isset($_SERVER['REQUEST_URI']) || str_contains($_SERVER['REQUEST_URI'], 'login')) header('location:../');

// Show the login page with custom error code
function getName($errorMessage)
{
    $regexUsername = substr($GLOBALS["regex"]["username"], 1, -1);
    $regexPassword = substr($GLOBALS["regex"]["password"], 1, -1);
?>
    <div class='col'>
        <div class="container pad">
            <form action='' method='POST'>
                <h2>Hvem er du?</h2>
                <?php
                $d = $errorMessage == "" ? "none" : "block";
                echo "<div class='error' style='display: $d;'>$errorMessage</div>";
                ?>
                <div class='input-container'>
                    <label for='loginUsername'>Brukernavn&nbsp;</label>
                    <input type='text' id='loginUsername' name='username' placeholder="Brukernavn" required autofocus pattern='<?= $regexUsername ?>'>
                </div>
                <div class='input-container'>
                    <div class="bar">
                        <label for='loginPassword'>Passord&nbsp;</label>
                        <a onclick="showPassword('loginPassword')">Vis/Skjul</a>
                    </div>
                    <input type='password' id='loginPassword' name='password' placeholder="Passord" required pattern='<?= $regexPassword ?>'>
                </div>
                <input type='submit' value="Logg inn">
            </form>
        </div>
    </div>
<?php
}

// Cookie varer i 1 år, men gjelder kun på denne siden
// The cookie cannot be changed client side because: httponly = true
$cookieOptions = ['expires' => time() + 3600 * 24 * 365, 'path' => $_SERVER['REQUEST_URI'], 'samesite' => 'Lax', 'httponly' => true];
if (
    isset($_POST['username']) &&
    isset($_POST['password'])
) {

    // Check if the inputted username matches the expected pattern
    if (
        preg_match($regex["username"], $_POST['username']) &&
        preg_match($regex["password"], $_POST['password'])
    ) {

        // Check if a user with matching credentials exists
        for ($i = 0; $i < count($db["users"]); $i++) {
            if (
                $db['users'][$i]['username'] == trim($_POST['username']) &&
                password_verify($_POST['password'], $db['users'][$i]['password'])
            ) {
                // Remember the user with a cookie
                // setcookie('userid', password_hash($_POST['username'] . $_POST['password'], PASSWORD_BCRYPT), $cookieOptions);
                setcookie('userid', $i, $cookieOptions);

                // Redirect to user to manage.php
                header('location:./');
            }
        }
        // Reopen the login page, with a custom error message 
        getName('Brukernavn eller passord er feil :(');
    } else getName('Et eller fler av feltene er formatert feil :(');
} else getName('');
