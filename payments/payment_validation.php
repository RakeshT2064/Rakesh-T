<?php
function validateUPIPayment($upi_id, $transaction_id) {
    $errors = [];
    
    // Validate UPI ID format
    if (!preg_match('/^[\w\.\-]+@[\w\.\-]+$/', $upi_id)) {
        $errors[] = "Invalid UPI ID format";
    }
    
    // Validate transaction ID
    if (!preg_match('/^[A-Za-z0-9]{12}$/', $transaction_id)) {
        $errors[] = "Invalid transaction ID format";
    }
    
    return $errors;
}

function validateCardPayment($card_number, $expiry, $cvv, $name) {
    $errors = [];
    
    // Remove spaces from card number
    $card_number = str_replace(' ', '', $card_number);
    
    // Validate card number using Luhn algorithm
    if (!validateLuhn($card_number)) {
        $errors[] = "Invalid card number";
    }
    
    // Validate expiry date
    if (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $expiry)) {
        $errors[] = "Invalid expiry date format";
    } else {
        list($month, $year) = explode('/', $expiry);
        $expiry_date = DateTime::createFromFormat('y-m', $year . '-' . $month);
        $now = new DateTime();
        
        if ($expiry_date < $now) {
            $errors[] = "Card has expired";
        }
    }
    
    // Validate CVV
    if (!preg_match('/^[0-9]{3,4}$/', $cvv)) {
        $errors[] = "Invalid CVV";
    }
    
    // Validate name
    if (empty($name) || strlen($name) < 3) {
        $errors[] = "Invalid card holder name";
    }
    
    return $errors;
}

function validateLuhn($number) {
    $sum = 0;
    $numDigits = strlen($number);
    $parity = $numDigits % 2;
    
    for ($i = $numDigits - 1; $i >= 0; $i--) {
        $digit = intval($number[$i]);
        
        if ($i % 2 != $parity) {
            $digit *= 2;
            if ($digit > 9) {
                $digit -= 9;
            }
        }
        
        $sum += $digit;
    }
    
    return ($sum % 10) == 0;
}

function validateNetBanking($bank_code) {
    $valid_banks = ['sbi', 'hdfc', 'icici', 'axis', 'pnb', 'bob'];
    return in_array($bank_code, $valid_banks);
}