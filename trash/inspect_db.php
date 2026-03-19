<?php
require 'config/db.php';

echo "<h3>Purchases Analysis (Last 20)</h3>";
$purchases = $pdo->query("SELECT id, purchase_date, qat_type_id, quantity_kg, received_units, status FROM purchases ORDER BY id DESC LIMIT 20")->fetchAll();
if (empty($purchases)) {
    echo "No purchases found.<br>";
} else {
    echo "<table border='1'><tr><th>ID</th><th>Date</th><th>Type</th><th>KG</th><th>Units</th><th>Status</th></tr>";
    foreach ($purchases as $p) {
        echo "<tr><td>{$p['id']}</td><td>{$p['purchase_date']}</td><td>{$p['qat_type_id']}</td><td>{$p['quantity_kg']}</td><td>{$p['received_units']}</td><td>{$p['status']}</td></tr>";
    }
    echo "</table>";
}

echo "<h3>Leftovers Analysis (Last 20)</h3>";
$leftovers = $pdo->query("SELECT id, source_date, sale_date, qat_type_id, weight_kg, quantity_units, status FROM leftovers ORDER BY id DESC LIMIT 20")->fetchAll();
if (empty($leftovers)) {
    echo "No leftovers found.<br>";
} else {
    echo "<table border='1'><tr><th>ID</th><th>Source Date</th><th>Sale Date</th><th>Type</th><th>KG</th><th>Units</th><th>Status</th></tr>";
    foreach ($leftovers as $l) {
        echo "<tr><td>{$l['id']}</td><td>{$l['source_date']}</td><td>{$l['sale_date']}</td><td>{$l['qat_type_id']}</td><td>{$l['weight_kg']}</td><td>{$l['quantity_units']}</td><td>{$l['status']}</td></tr>";
    }
    echo "</table>";
}

echo "<h3>Daily Debts Analysis</h3>";
$debts = $pdo->query("SELECT id, sale_date, due_date, customer_id, price, is_paid FROM sales WHERE payment_method = 'Debt' AND debt_type = 'Daily' AND is_paid = 0 ORDER BY due_date ASC")->fetchAll();
if (empty($debts)) {
    echo "No unpaid daily debts found.<br>";
} else {
    echo "<table border='1'><tr><th>ID</th><th>Sale Date</th><th>Due Date</th><th>Customer</th><th>Price</th></tr>";
    foreach ($debts as $d) {
        echo "<tr><td>{$d['id']}</td><td>{$d['sale_date']}</td><td>{$d['due_date']}</td><td>{$d['customer_id']}</td><td>{$d['price']}</td></tr>";
    }
    echo "</table>";
}

echo "<h3>Oldest Unclosed Logic Check</h3>";
$stmtOldest = $pdo->query("SELECT MIN(d) FROM (
    SELECT MIN(purchase_date) as d FROM purchases WHERE status IN ('Fresh', 'Momsi')
    UNION
    SELECT MIN(sale_date) as d FROM sales WHERE payment_method = 'Debt' AND debt_type = 'Daily' AND is_paid = 0
    UNION
    SELECT MIN(sale_date) as d FROM leftovers WHERE status = 'Transferred_Next_Day'
) as unclosed_dates");
$oldest = $stmtOldest->fetchColumn();
echo "Oldest Unclosed Date detected: <b>" . ($oldest ?: "NONE") . "</b><br>";

echo "<h3>Today's Date (PHP): " . date('Y-m-d') . "</h3>";
