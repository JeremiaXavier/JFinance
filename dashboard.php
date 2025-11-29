<?php
require_once 'config.php';
if (!is_logged_in()) { redirect('login.php'); }

$user_id = get_user_id();

$income_result = $conn->query("SELECT SUM(amount) AS total FROM income WHERE user_id = $user_id");
$total_income = $income_result->fetch_assoc()['total'] ?? 0;

$expense_result = $conn->query("SELECT SUM(amount) AS total FROM expense WHERE user_id = $user_id");
$total_expense = $expense_result->fetch_assoc()['total'] ?? 0;

$debt_result = $conn->query("SELECT SUM(amount) AS total FROM debt WHERE user_id = $user_id");
$total_debt = $debt_result->fetch_assoc()['total'] ?? 0;

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

$page_title = 'Financial Dashboard';
$page_section = 'Dashboard';

ob_start();
?>
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 rounded-lg p-4">
            <p class="text-[11px] uppercase tracking-wide text-slate-500">Current Balance</p>
            <p class="text-2xl font-semibold text-slate-900 mt-1">$<?php echo number_format($balance, 2); ?></p>
            <p class="text-[11px] text-emerald-600 mt-1"><i class="fas fa-circle-up mr-1"></i>Net after expenses and debt</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-lg p-4">
            <p class="text-[11px] uppercase tracking-wide text-slate-500">Total Income</p>
            <p class="text-2xl font-semibold text-emerald-600 mt-1">$<?php echo number_format($total_income, 2); ?></p>
            <p class="text-[11px] text-slate-500 mt-1">All recorded income</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-lg p-4">
            <p class="text-[11px] uppercase tracking-wide text-slate-500">Total Expense</p>
            <p class="text-2xl font-semibold text-rose-600 mt-1">$<?php echo number_format($total_expense, 2); ?></p>
            <p class="text-[11px] text-slate-500 mt-1">All recorded expenses</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-lg p-4">
            <p class="text-[11px] uppercase tracking-wide text-slate-500">Outstanding Debt</p>
            <p class="text-2xl font-semibold text-amber-600 mt-1">$<?php echo number_format($total_debt, 2); ?></p>
            <p class="text-[11px] text-slate-500 mt-1">Total pending liabilities</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-lg">
            <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Ledger</p>
                    <h2 class="text-sm font-semibold text-slate-900">Recent Transactions</h2>
                </div>
                <a href="export_csv.php" class="inline-flex items-center px-3 py-1.5 rounded-md bg-blue-600 text-white text-xs font-medium hover:bg-blue-700">
                    <i class="fas fa-download mr-2"></i>Export CSV
                </a>
            </div>
            <div class="px-4 py-3 border-b border-slate-200 flex flex-wrap gap-2 items-center text-xs">
                <span class="text-slate-500 mr-2">Quick filters:</span>
                <button class="px-2 py-1 rounded-md border border-slate-200 text-slate-600 hover:bg-slate-50">All</button>
                <button class="px-2 py-1 rounded-md border border-slate-200 text-emerald-600 hover:bg-slate-50">Income</button>
                <button class="px-2 py-1 rounded-md border border-slate-200 text-rose-600 hover:bg-slate-50">Expense</button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-slate-600">Date</th>
                            <th class="px-4 py-2 text-left font-semibold text-slate-600">Type</th>
                            <th class="px-4 py-2 text-left font-semibold text-slate-600">Category</th>
                            <th class="px-4 py-2 text-left font-semibold text-slate-600">Amount</th>
                            <th class="px-4 py-2 text-left font-semibold text-slate-600">Note</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php while ($row = $transactions->fetch_assoc()): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-2 text-slate-800"><?php echo htmlspecialchars($row['date']); ?></td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium <?php echo $row['type'] === 'Income' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'; ?>">
                                        <?php echo htmlspecialchars($row['type']); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-slate-700"><?php echo htmlspecialchars($row['category'] ?? '-'); ?></td>
                                <td class="px-4 py-2 font-semibold <?php echo $row['type'] === 'Income' ? 'text-emerald-600' : 'text-rose-600'; ?>">
                                    <?php echo $row['type'] === 'Income' ? '+' : '-'; ?>$<?php echo number_format($row['amount'], 2); ?>
                                </td>
                                <td class="px-4 py-2 text-slate-600"><?php echo htmlspecialchars($row['note'] ?? '-'); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white border border-slate-200 rounded-lg p-4">
                <p class="text-[11px] uppercase tracking-wide text-slate-500 mb-1">Alerts & Tasks</p>
                <ul class="text-xs text-slate-700 space-y-2">
                    <li class="flex gap-2">
                        <i class="fas fa-circle-exclamation text-amber-500 mt-0.5"></i>
                        <span>Review upcoming debt due dates and adjust budgets if required.</span>
                    </li>
                    <li class="flex gap-2">
                        <i class="fas fa-circle-info text-blue-500 mt-0.5"></i>
                        <span>Ensure categories are aligned with institutional chart of accounts.</span>
                    </li>
                </ul>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-4">
                <p class="text-[11px] uppercase tracking-wide text-slate-500 mb-1">Module Overview</p>
                <p class="text-xs text-slate-600">This console consolidates income, expenses, debts and budget controls in a single operational view, similar to internal banking or university finance systems.</p>
            </div>
        </div>
    </div>
</div>
<?php
$page_content = ob_get_clean();
include 'layout.php';
