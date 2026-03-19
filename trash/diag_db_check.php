<?php
header('Content-Type: text/plain; charset=utf-8');

function check_db($name, $id)
{
    echo "--- Checking Database: $name ---\n";
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=$name;charset=utf8", 'root', '');
        $stmt = $pdo->prepare("SELECT id, name FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($res) {
            echo "ID $id FOUND: " . $res['name'] . "\n";
        } else {
            echo "ID $id NOT FOUND.\n";
        }
    } catch (Exception $e) {
        echo "Error connecting to $name: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

check_db('qat_erp', 50);
check_db('qat', 50);
