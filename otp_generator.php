<?php

function generateOTP($length = 6) {
    $characters = '0123456789';
    $otp = '';
    $max = strlen($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $otp .= $characters[random_int(0, $max)];
    }
    return $otp;
}

// Example usage:
$otp_code = generateOTP();
echo "Generated OTP: " . $otp_code;

?>