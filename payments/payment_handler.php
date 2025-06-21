<?php
require_once 'payment_validation.php';

class PaymentHandler {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function processPayment($booking_id, $payment_method, $payment_data) {
        try {
            $this->pdo->beginTransaction();
            
            // Validate payment data
            $errors = $this->validatePayment($payment_method, $payment_data);
            if (!empty($errors)) {
                throw new Exception(implode(", ", $errors));
            }
            
            // Process payment based on method
            switch($payment_method) {
                case 'upi':
                    $transaction_id = $this->processUPIPayment($payment_data);
                    break;
                case 'card':
                    $transaction_id = $this->processCardPayment($payment_data);
                    break;
                case 'netbanking':
                    $transaction_id = $this->processNetBankingPayment($payment_data);
                    break;
                default:
                    throw new Exception("Invalid payment method");
            }
            
            // Update booking status
            $stmt = $this->pdo->prepare("
                UPDATE bookings 
                SET payment_status = 'completed', 
                    transaction_id = ?, 
                    payment_method = ? 
                WHERE booking_id = ?
            ");
            $stmt->execute([$transaction_id, $payment_method, $booking_id]);
            
            $this->pdo->commit();
            return ['success' => true, 'transaction_id' => $transaction_id];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    private function validatePayment($method, $data) {
        switch($method) {
            case 'upi':
                return validateUPIPayment($data['upi_id'], $data['transaction_id']);
            case 'card':
                return validateCardPayment(
                    $data['card_number'],
                    $data['expiry'],
                    $data['cvv'],
                    $data['name']
                );
            case 'netbanking':
                return validateNetBanking($data['bank_code']) ? [] : ['Invalid bank selected'];
            default:
                return ['Invalid payment method'];
        }
    }
    
    // Implement actual payment gateway integration here
    private function processUPIPayment($data) {
        // Integrate with actual UPI payment gateway
        return uniqid('UPI');
    }
    
    private function processCardPayment($data) {
        // Integrate with actual card payment gateway
        return uniqid('CARD');
    }
    
    private function processNetBankingPayment($data) {
        // Integrate with actual net banking gateway
        return uniqid('NB');
    }
}