<?php

class User {

    private $conn;
    private $table = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    // REGISTER USER
    public function register($fullname, $email, $password) {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO " . $this->table . "
                  (fullname, email, password)
                  VALUES (:fullname, :email, :password)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":fullname", $fullname);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashedPassword);

        return $stmt->execute();
    }

    // CHECK EMAIL
    public function emailExists($email) {

        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":email", $email);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // LOGIN
    public function login($email, $password) {

        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":email", $email);

        $stmt->execute();

        if($stmt->rowCount() > 0) {

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if(password_verify($password, $user['password'])) {
                return $user;
            }
        }

        return false;
    }
}
?>