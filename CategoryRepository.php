<?php
include_once 'ICategoryRepository.php';
include_once 'DBConnection.php';

class CategoryRepository implements ICategoryRepository {
    private $dbConnection;

    public function __construct(DBConnection $dbConnection) {
        $this->dbConnection = $dbConnection;
    }

    public function getAllCategories() {
        $conn = $this->dbConnection->getConnection();
        $sql = "SELECT * FROM category";
        $result = $conn->query($sql);
        return $result;
    }
}
?>
