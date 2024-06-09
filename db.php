<?php
class Database {
    private $db;

    public function __construct($db_file) {
        $this->db = new SQLite3($db_file);
    }

    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Unable to prepare statement: " . $this->db->lastErrorMsg());
        }
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        return $stmt->execute();
    }

    public function fetchAll($sql, $params = []) {
        $result = $this->query($sql, $params);
        $rows = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function fetch($sql, $params = []) {
        $result = $this->query($sql, $params);
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    public function lastInsertId() {
        return $this->db->lastInsertRowID();
    }
}

$db = new Database('/home/pedroribeiro/public_html/openmarket.db');
?>


