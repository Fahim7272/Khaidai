<?php

class ItemRepository {
    private $conn;

    // The constructor expects the database connection
    public function __construct($db_conn) {
        $this->conn = $db_conn;
    }

    // This method fetches all food items to display on the foods.php page
    public function getItems() {
        $sql = "SELECT * FROM items";
        $result = $this->conn->query($sql);
        
        $items = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        
        return $items;
    }
}

?>