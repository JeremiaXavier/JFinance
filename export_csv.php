<?php
require_once 'config.php';
if (!is_logged_in()) { redirect('login.php'); }

$user_id = get_user_id();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=transactions_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');

fputcsv($output, ['Date', 'Type', 'Category', 'Amount', 'Note']);

$result = $conn->query("
    SELECT i.date, 'Income' AS type, c.name AS category, i.amount, i.note FROM income i
    LEFT JOIN category c ON i.category_id = c.id
    WHERE i.user_id = $user_id
    UNION ALL
    SELECT e.date, 'Expense' AS type, c.name AS category, e.amount, e.note FROM expense e
    LEFT JOIN category c ON e.category_id = c.id
    WHERE e.user_id = $user_id
    ORDER BY date DESC
");

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit;
