<?php
// Class for database connection

    class Database {
        private $db;
        private static $instance = null;

        // Single shared connection, using one source of truth for credentials
        // instead of every Model constructing its own `new Database(...)`.
        public static function connect() {
            if (self::$instance === null) {
                // Railway's MySQL plugin injects MYSQLHOST/MYSQLPORT/etc as env vars —
                // prefer those in production, fall back to Config/database.php for local dev.
                if (getenv('MYSQLHOST')) {
                    self::$instance = new self(
                        getenv('MYSQLHOST'),
                        getenv('MYSQLDATABASE'),
                        getenv('MYSQLUSER'),
                        getenv('MYSQLPASSWORD'),
                        getenv('MYSQLPORT') ?: 3306
                    );
                } else {
                    $config = require __DIR__ . "/../Config/database.php";
                    self::$instance = new self($config['host'], $config['dbname'], $config['username'], $config['password'], $config['port'] ?? 3306);
                }
            }
            return self::$instance;
        }

        // Constructor to connect to database
        public function __construct($hostname, $dbname, $username, $password, $port = 3306) {
            $dsn = "mysql:host=$hostname;port=$port;dbname=$dbname;charset=UTF8";

            try {
                $this->db = new PDO($dsn, $username, $password);
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }

        // Prepare SQL statement
        public function prepare($sql) {
            return $this->db->prepare($sql);
        }

        // Execute the query SQL statement with optional parameters
        public function query($sql, $params = []) {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        }

        // ✅ Fix: Add lastInsertId() method
        public function lastInsertId() {
            return $this->db->lastInsertId();
        }
    }

?>
