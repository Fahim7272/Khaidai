<?php
// authentication.php
class Authentication {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function authenticateAdmin($email, $password) {
        // Admin authentication logic
        // Example: Check against admin credentials
        if ($email === 'admin@gmail.com' && $password === '1234') {
            return true;
        }
        return false;
    }

    public function authenticateDeliveryMan($email, $password) {
        // Delivery men authentication logic
        // Example: Check against deliverymen table
        $sql = "SELECT id, name, email, password FROM deliverymen WHERE email = ?";
        // Execute prepared statement and verify password
        // Return true/false based on authentication success
    }

    public function authenticateUser($email, $password) {
        // User authentication logic
        // Example: Check against users table
        $sql = "SELECT id, name, email, password FROM users WHERE email = ?";
        // Execute prepared statement and verify password
        // Return true/false based on authentication success
    }
}
?>
