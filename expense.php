<?php
require_once 'config.php';
if (!is_logged_in()) { redirect('login.php'); }

$user_id = get_user_id();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $amount = floatval($_POST['amount']);
        $category = intval($_POST['category']);
        $note = $_POST['note'] ?? '';
        if ($amount > 0 && $category > 0) {
            $stmt = $conn->prepare("INSERT INTO expense (user_id, category_id, amount, note) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iids", $user_id, $category, $amount, $note);
            if ($stmt->execute()) {
                $message = 'Expense added successfully';
            }
            $stmt->close();
        }
    } elseif (isset($_POST['delete'])) {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM expense WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $stmt->close();
        $message = 'Expense deleted';
    }
}

$categories = $conn->query("SELECT id, name FROM category WHERE user_id = $user_id AND type = 'expense' ORDER BY name");
$expenses = $conn->query("
    SELECT e.id, e.amount, e.note, e.date, c.name AS category_name
    FROM expense e
    LEFT JOIN category c ON e.category_id = c.id
    WHERE e.user_id = $user_id
    ORDER BY e.date DESC
");

$page_title = 'Expense Register';
$page_section = 'Transactions / Expense';

ob_start();
?>
<div class="space-y-6">
    <?php if ($message): ?>
        <div class="p-3 rounded-md bg-rose-50 border border-rose-200 text-xs text-rose-700 flex items-start gap-2">
            <i class="fas fa-circle-check mt-0.5"></i>
            <span><?php echo htmlspecialchars($message); ?></span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white border border-slate-200 rounded-lg">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">New Entry</p>
                    <h2 class="text-sm font-semibold text-slate-900">Record Expense</h2>
                </div>
                <div class="p-4 text-sm">
                    <form method="POST" class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Amount ($)</label>
                            <input type="number" step="0.01" name="amount" class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-rose-500" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Category</label>
                            <select name="category" class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-rose-500" required>
                                <option value="">Select category...</option>
                                <?php while ($cat = $categories->fetch_assoc()): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Note</label>
                            <input type="text" name="note" class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-rose-500" placeholder="Optional remark">
                        </div>
                        <button type="submit" name="add" class="w-full bg-rose-600 hover:bg-rose-700 text-white text-xs font-medium py-2 rounded-md flex items-center justify-center gap-2">
                            <i class="fas fa-plus text-xs"></i><span>Submit Expense</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white border border-slate-200 rounded-lg">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <div>
                        <p class="text-[11px] uppercase tracking-wide text-slate-500">Ledger</p>
                        <h2 class="text-sm font-semibold text-slate-900">Expense Records</h2>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <input type="text" placeholder="Search note..." class="px-2 py-1 border border-slate-300 rounded-md focus:outline-none focus:ring-1 focus:ring-slate-400">
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold text-slate-600">Date</th>
                                <th class="px-4 py-2 text-left font-semibold text-slate-600">Amount</th>
                                <th class="px-4 py-2 text-left font-semibold text-slate-600">Category</th>
                                <th class="px-4 py-2 text-left font-semibold text-slate-600">Note</th>
                                <th class="px-4 py-2 text-left font-semibold text-slate-600">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php while ($row = $expenses->fetch_assoc()): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2 text-slate-800"><?php echo htmlspecialchars($row['date']); ?></td>
                                    <td class="px-4 py-2 font-semibold text-rose-600">-$<?php echo number_format($row['amount'], 2); ?></td>
                                    <td class="px-4 py-2 text-slate-700"><?php echo htmlspecialchars($row['category_name'] ?? '-'); ?></td>
                                    <td class="px-4 py-2 text-slate-600"><?php echo htmlspecialchars($row['note'] ?? '-'); ?></td>
                                    <td class="px-4 py-2">
                                        <form method="POST" onsubmit="return confirm('Delete this expense entry?');">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" name="delete" class="text-rose-600 hover:text-rose-800 text-xs font-medium">
                                                <i class="fas fa-trash-alt mr-1"></i>Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$page_content = ob_get_clean();
include 'layout.php';
