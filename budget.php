<?php
require_once 'config.php';
if (!is_logged_in()) { redirect('login.php'); }

$user_id = get_user_id();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $category = intval($_POST['category']);
        $limit = floatval($_POST['limit_amount']);
        if ($category > 0 && $limit > 0) {
            $stmt = $conn->prepare("INSERT INTO budget (user_id, category_id, limit_amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE limit_amount = ?");
            $stmt->bind_param("iidd", $user_id, $category, $limit, $limit);
            if ($stmt->execute()) {
                $message = 'Budget set successfully';
            }
            $stmt->close();
        }
    } elseif (isset($_POST['delete'])) {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM budget WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $stmt->close();
        $message = 'Budget deleted';
    }
}

$categories = $conn->query("SELECT id, name FROM category WHERE user_id = $user_id AND type = 'expense' ORDER BY name");
$budgets = $conn->query("
    SELECT b.id, b.limit_amount, c.name AS category_name,
           COALESCE(SUM(e.amount), 0) AS spent
    FROM budget b
    LEFT JOIN category c ON b.category_id = c.id
    LEFT JOIN expense e ON b.category_id = e.category_id AND e.user_id = $user_id
    WHERE b.user_id = $user_id
    GROUP BY b.id, b.category_id, b.limit_amount, c.name
");

$page_title = 'Budget Control Center';
$page_section = 'Controls / Budget';

ob_start();
?>
<div class="space-y-6">
    <?php if ($message): ?>
        <div class="p-3 rounded-md bg-indigo-50 border border-indigo-200 text-xs text-indigo-700 flex items-start gap-2">
            <i class="fas fa-circle-check mt-0.5"></i>
            <span><?php echo htmlspecialchars($message); ?></span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white border border-slate-200 rounded-lg">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Budget Policy</p>
                    <h2 class="text-sm font-semibold text-slate-900">Set Category Limit</h2>
                </div>
                <div class="p-4 text-sm">
                    <form method="POST" class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Expense Category</label>
                            <select name="category" class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500" required>
                                <option value="">Select category...</option>
                                <?php while ($cat = $categories->fetch_assoc()): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Limit Amount ($)</label>
                            <input type="number" step="0.01" name="limit_amount" class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500" required>
                        </div>
                        <button type="submit" name="add" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium py-2 rounded-md flex items-center justify-center gap-2">
                            <i class="fas fa-bullseye text-xs"></i><span>Apply Budget</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-4">
            <?php while ($row = $budgets->fetch_assoc()):
                $spent = floatval($row['spent']);
                $limit = floatval($row['limit_amount']);
                $percentage = $limit > 0 ? ($spent / $limit) * 100 : 0;
                $status = 'Under Limit';
                $barClass = 'bg-emerald-500';
                if ($percentage >= 90 && $percentage < 100) {
                    $status = 'Near Limit';
                    $barClass = 'bg-amber-500';
                } elseif ($percentage >= 100) {
                    $status = 'Over Limit';
                    $barClass = 'bg-rose-500';
                }
            ?>
                <div class="bg-white border border-slate-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <p class="text-xs font-semibold text-slate-900"><?php echo htmlspecialchars($row['category_name']); ?></p>
                            <p class="text-[11px] text-slate-500">Configured budget for this category</p>
                        </div>
                        <form method="POST" onsubmit="return confirm('Delete this budget?');">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete" class="text-rose-600 hover:text-rose-800 text-[11px] font-medium">
                                <i class="fas fa-trash-alt mr-1"></i>Remove
                            </button>
                        </form>
                    </div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="text-slate-600">$<?php echo number_format($spent, 2); ?> spent</span>
                        <span class="text-slate-600">$<?php echo number_format($limit, 2); ?> limit</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 mb-1">
                        <div class="<?php echo $barClass; ?> h-2 rounded-full" style="width: <?php echo min($percentage, 100); ?>%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] mt-1">
                        <span class="text-slate-500">Utilization: <?php echo number_format($percentage, 0); ?>%</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full border border-slate-200 text-slate-600"><?php echo $status; ?></span>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>
<?php
$page_content = ob_get_clean();
include 'layout.php';
