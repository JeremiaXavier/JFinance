<?php
require_once 'config.php';
if (!is_logged_in()) { redirect('login.php'); }

$user_id = get_user_id();

$income_result  = $conn->query("SELECT SUM(amount) AS total FROM income WHERE user_id = $user_id");
$total_income   = $income_result->fetch_assoc()['total'] ?? 0;

$expense_result = $conn->query("SELECT SUM(amount) AS total FROM expense WHERE user_id = $user_id");
$total_expense  = $expense_result->fetch_assoc()['total'] ?? 0;

$debt_result    = $conn->query("SELECT SUM(amount) AS total FROM debt WHERE user_id = $user_id");
$total_debt     = $debt_result->fetch_assoc()['total'] ?? 0;

$balance = $total_income - $total_expense - $total_debt;

$transactions = $conn->query("
    SELECT date, 'Income' AS type, c.name AS category, amount, note FROM income i
    LEFT JOIN category c ON i.category_id = c.id
    WHERE i.user_id = $user_id
    UNION ALL
    SELECT date, 'Expense' AS type, c.name AS category, amount, note FROM expense e
    LEFT JOIN category c ON e.category_id = c.id
    WHERE e.user_id = $user_id
    ORDER BY date DESC
    LIMIT 10
");

$page_title   = 'Dashboard';
$page_section = 'Finance Module';

ob_start();
?>
<div class="space-y-4">

    <!-- Top summary row, more compact and modern -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div class="bg-white border border-[#d1d5db] px-3 py-3">
            <p class="text-[11px] text-[#6b7280] uppercase tracking-wide">Net Balance</p>
            <p class="mt-1 text-[20px] font-semibold text-[#111827]">
                ₹<?php echo number_format($balance, 2); ?>
            </p>
            <p class="mt-1 text-[11px] text-[#4b5563]">After income, expenses and debt</p>
        </div>
        <div class="bg-white border border-[#d1d5db] px-3 py-3">
            <p class="text-[11px] text-[#6b7280] uppercase tracking-wide">Total Income</p>
            <p class="mt-1 text-[20px] font-semibold text-green-700">
                ₹<?php echo number_format($total_income, 2); ?>
            </p>
            <p class="mt-1 text-[11px] text-[#4b5563]">All recorded credit entries</p>
        </div>
        <div class="bg-white border border-[#d1d5db] px-3 py-3">
            <p class="text-[11px] text-[#6b7280] uppercase tracking-wide">Total Expense</p>
            <p class="mt-1 text-[20px] font-semibold text-red-700">
                ₹<?php echo number_format($total_expense, 2); ?>
            </p>
            <p class="mt-1 text-[11px] text-[#4b5563]">All recorded debit entries</p>
        </div>
        <div class="bg-white border border-[#d1d5db] px-3 py-3">
            <p class="text-[11px] text-[#6b7280] uppercase tracking-wide">Outstanding Debt</p>
            <p class="mt-1 text-[20px] font-semibold text-orange-700">
                ₹<?php echo number_format($total_debt, 2); ?>
            </p>
            <p class="mt-1 text-[11px] text-[#4b5563]">Current unpaid liabilities</p>
        </div>
    </div>

    <!-- Summary table -->
    <div class="bg-white border border-[#d1d5db]">
        <div class="bg-[#f9fafb] border-b border-[#e5e7eb] px-3 py-2 flex items-center justify-between">
            <span class="text-[12px] font-semibold text-[#111827]">Summary (Detailed)</span>
            <span class="text-[11px] text-[#6b7280]">Snapshot of your current position</span>
        </div>
        <div class="p-3">
            <table class="w-full border border-[#d1d5db] text-[12px]">
                <thead class="bg-[#f3f4f6]">
                    <tr>
                        <th class="border border-[#d1d5db] px-2 py-1 text-left">Metric</th>
                        <th class="border border-[#d1d5db] px-2 py-1 text-left">Amount (₹)</th>
                        <th class="border border-[#d1d5db] px-2 py-1 text-left">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-[#d1d5db] px-2 py-1">Total Income</td>
                        <td class="border border-[#d1d5db] px-2 py-1 text-green-700 font-semibold">
                            ₹<?php echo number_format($total_income, 2); ?>
                        </td>
                        <td class="border border-[#d1d5db] px-2 py-1">All recorded income entries</td>
                    </tr>
                    <tr>
                        <td class="border border-[#d1d5db] px-2 py-1">Total Expense</td>
                        <td class="border border-[#d1d5db] px-2 py-1 text-red-700 font-semibold">
                            ₹<?php echo number_format($total_expense, 2); ?>
                        </td>
                        <td class="border border-[#d1d5db] px-2 py-1">All recorded expense entries</td>
                    </tr>
                    <tr>
                        <td class="border border-[#d1d5db] px-2 py-1">Outstanding Debt</td>
                        <td class="border border-[#d1d5db] px-2 py-1 text-orange-700 font-semibold">
                            ₹<?php echo number_format($total_debt, 2); ?>
                        </td>
                        <td class="border border-[#d1d5db] px-2 py-1">Total unpaid liabilities</td>
                    </tr>
                    <tr class="bg-[#f9fafb]">
                        <td class="border border-[#d1d5db] px-2 py-1 font-semibold">Net Balance</td>
                        <td class="border border-[#d1d5db] px-2 py-1 font-semibold">
                            ₹<?php echo number_format($balance, 2); ?>
                        </td>
                        <td class="border border-[#d1d5db] px-2 py-1">Income − Expense − Debt</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Transactions with subtle badges -->
    <div class="bg-white border border-[#d1d5db]">
        <div class="bg-[#f9fafb] border-b border-[#e5e7eb] px-3 py-2 flex justify-between items-center">
            <span class="text-[12px] font-semibold text-[#111827]">Recent Transactions</span>
            <div class="flex items-center gap-3 text-[11px]">
                <span class="text-[#6b7280] hidden sm:inline">Last 10 entries</span>
                <a href="export_csv.php" class="text-[#2563eb] underline">Export CSV</a>
            </div>
        </div>
        <div class="p-3 overflow-x-auto">
            <table class="min-w-full border border-[#d1d5db] text-[12px]">
                <thead class="bg-[#f3f4f6]">
                    <tr>
                        <th class="border border-[#d1d5db] px-2 py-1 text-left">Date</th>
                        <th class="border border-[#d1d5db] px-2 py-1 text-left">Type</th>
                        <th class="border border-[#d1d5db] px-2 py-1 text-left">Category</th>
                        <th class="border border-[#d1d5db] px-2 py-1 text-left">Amount (₹)</th>
                        <th class="border border-[#d1d5db] px-2 py-1 text-left">Note</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $transactions->fetch_assoc()): ?>
                        <?php
                            $isIncome = ($row['type'] === 'Income');
                            $typeClass = $isIncome
                                ? 'bg-green-50 text-green-700 border border-green-200'
                                : 'bg-red-50 text-red-700 border border-red-200';
                            $amountClass = $isIncome ? 'text-green-700' : 'text-red-700';
                            $prefix = $isIncome ? '+' : '-';
                        ?>
                        <tr class="hover:bg-[#f9fafb]">
                            <td class="border border-[#d1d5db] px-2 py-1">
                                <?php echo htmlspecialchars($row['date']); ?>
                            </td>
                            <td class="border border-[#d1d5db] px-2 py-1">
                                <span class="inline-block px-2 py-0.5 rounded-full text-[11px] <?php echo $typeClass; ?>">
                                    <?php echo htmlspecialchars($row['type']); ?>
                                </span>
                            </td>
                            <td class="border border-[#d1d5db] px-2 py-1">
                                <?php echo htmlspecialchars($row['category'] ?? '-'); ?>
                            </td>
                            <td class="border border-[#d1d5db] px-2 py-1 font-semibold <?php echo $amountClass; ?>">
                                <?php echo $prefix; ?>₹<?php echo number_format($row['amount'], 2); ?>
                            </td>
                            <td class="border border-[#d1d5db] px-2 py-1">
                                <?php echo htmlspecialchars($row['note'] ?? '-'); ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                    <?php if ($transactions->num_rows === 0): ?>
                        <tr>
                            <td colspan="5" class="border border-[#d1d5db] px-2 py-2 text-center text-[#6b7280]">
                                No transactions found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<?php
$page_content = ob_get_clean();
include 'layout.php';
