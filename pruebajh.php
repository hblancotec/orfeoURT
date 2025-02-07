<?php

require_once('include/recaptchalib.php');

// Get a key from https://www.google.com/recaptcha/admin/create
$publickey = "6Lfo0MYSAAAAAJUYPKcXC6XRA4WDuhZYsPSUOfZy";
$privatekey = "6Lfo0MYSAAAAAPnMC-z4uOOrbRlSgofER9XMcJaD";

# the response from reCAPTCHA
$resp = null;
# the error code from reCAPTCHA, if any
$error = null;

# was there a reCAPTCHA response?
if ($_POST["recaptcha_response_field"]) {
        $resp = recaptcha_check_answer ($privatekey,
                                        $_SERVER["REMOTE_ADDR"],
                                        $_POST["recaptcha_challenge_field"],
                                        $_POST["recaptcha_response_field"]);

        if ($resp->is_valid) {
                echo "You got it!";
        } else {
                # set the error code so that we can display it
                $error = $resp->error;
        }
}
?>
<html>
  <body>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<?php
echo recaptcha_get_html($publickey, $error);
?>
    <br/>
    <input type="submit" value="submit" />
    </form>
  </body>
</html>