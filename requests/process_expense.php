<?php
// requests/process_expense.php
require_once '../config/db.php';
require_once '../includes/Autoloader.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        die("Unauthorized");
    }

    try {
        $expenseRepo = new ExpenseRepository($pdo);
        $depositRepo = new DepositRepository($pdo);
        $staffRepo = new StaffRepository($pdo);
        $service = new ExpenseService($expenseRepo, $depositRepo, $staffRepo);

        $data = [
            'expense_date' => !empty($_POST['expense_date']) ? $_POST['expense_date'] : date('Y-m-d'),
            'description' => $_POST['description'],
            'amount' => $_POST['amount'],
            'category' => $_POST['category'],
            'staff_id' => !empty($_POST['staff_id']) ? $_POST['staff_id'] : null,
            'created_by' => $_SESSION['user_id']
        ];

        $service->addExpense($data);

        header("Location: ../expenses.php?success=1");
        exit;
    } catch (Exception $e) {
        $error = urlencode("خطأ: " . $e->getMessage());
        header("Location: ../expenses.php?error=$error");
        exit;
    }
}
