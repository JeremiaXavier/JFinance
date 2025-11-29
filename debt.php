<?php
require_once 'config.php';
if (!is_logged_in()) { redirect('login.php'); }

$user_id = get_user_id();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $creditor = $_POST['creditor'] ?? '';
        $amount = floatval($_POST['amount']);
        $due_date = $_POST['due_date'] ?? null;
        if (!empty($creditor) && $amount > 0) {
            $stmt = $conn->prepare("INSERT INTO debt (user_id, creditor, amount, due_date) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isds", $user_id, $creditor, $amount, $due_date);
            if ($stmt->execute()) {
                $message = 'Debt added successfully';
            }
            $stmt->close();
        }
    } elseif (isset($_POST['delete'])) {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM debt WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $stmt->close();
        $message = 'Debt deleted';
    }
}

$debts = $conn->query("SELECT id, creditor, amount, due_date FROM debt WHERE user_id = $user_id ORDER BY due_date ASC");

$page_title = 'Debt Obligations';
$page_section = 'Controls / Debt';

ob_start();
?>
<div class="space-y-6">
    <?php if ($message): ?>
        <div class="p-3 rounded-md bg-amber-50 border border-amber-200 text-xs text-amber-700 flex items-start gap-2">
            <i class="fas fa-circle-check mt-0.5"></i>
            <span><?php echo htmlspecialchars($message); ?></span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white border border-slate-200 rounded-lg">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">New Obligation</p>
                    <h2 class="text-sm font-semibold text-slate-900">Register Debt</h2>
                </div>
                <div class="p-4 text-sm">
                    <form method="POST" class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Creditor</label>
                            <input type="text" name="creditor" class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-amber-500" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Amount ($)</label>
                            <input type="number" step="0.01" name="amount" class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-amber-500" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Due Date</label>
                            <input type="date" name="due_date" class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                        </div>
                        <button type="submit" name="add" class="w-full bg-amber-600 hover:bg-amber-700 text-white text-xs font-medium py-2 rounded-md flex items-center justify-center gap-2">
                            <i class="fas fa-plus text-xs"></i><span>Register Debt</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white border border-slate-200 rounded-lg">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <div>
                        <p class="text-[11px] uppercase tracking-wide text-slate-500">Obligations</p>
                        <h2 class="text-sm font-semibold text-slate-900">Debt Register</h2>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold text-slate-600">Creditor</th>
                                <th class="px-4 py-2 text-left font-semibold text-slate-600">Amount</th>
                                <th class="px-4 py-2 text-left font-semibold text-slate-600">Due Date</th>
                                <th class="px-4 py-2 text-left font-semibold text-slate-600">Status</th>
                                <th class="px-4 py-2 text-left font-semibold text-slate-600">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php while ($row = $debts->fetch_assoc()): 
                                $due = $row['due_date'];
                                $status = 'Open';
                                $statusClass = 'bg-amber-50 text-amber-700';
                                if ($due && $due < date('Y-m-d')) {
                                    $status = 'Overdue';
                                    $statusClass = 'bg-rose-50 text-rose-700';
                                } elseif ($due && $due === date('Y-m-d')) {
                                    $status = 'Due Today';
                                    $statusClass = 'bg-emerald-50 text-emerald-700';
                                }
                            ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2 text-slate-800"><?php echo htmlspecialchars($row['creditor']); ?></td>
                                    <td class="px-4 py-2 font-semibold text-slate-900">$<?php echo number_format($row['amount'], 2); ?></td>
                                    <td class="px-4 py-2 text-slate-700"><?php echo htmlspecialchars($row['due_date'] ?? '-'); ?></td>
                                    <td class="px-4 py-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium <?php echo $statusClass; ?>">
                                            <?php echo $status; ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <form method="POST" onsubmit="return confirm('Delete this debt?');">
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
