<?php
require_once 'config.php';
if (!is_logged_in()) { redirect('login.php'); }

$user_id = get_user_id();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $name = $_POST['name'] ?? '';
        $type = $_POST['type'] ?? 'expense';
        if (!empty($name) && in_array($type, ['income', 'expense'])) {
            $stmt = $conn->prepare("INSERT INTO category (user_id, name, type) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user_id, $name, $type);
            if ($stmt->execute()) {
                $message = 'Category added successfully';
            }
            $stmt->close();
        }
    } elseif (isset($_POST['delete'])) {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM category WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $stmt->close();
        $message = 'Category deleted';
    }
}

$categories = $conn->query("SELECT id, name, type FROM category WHERE user_id = $user_id ORDER BY name");

$page_title = 'Category Directory';
$page_section = 'Controls / Categories';

ob_start();
?>
<div class="space-y-6">
    <?php if ($message): ?>
        <div class="p-3 rounded-md bg-purple-50 border border-purple-200 text-xs text-purple-700 flex items-start gap-2">
            <i class="fas fa-circle-check mt-0.5"></i>
            <span><?php echo htmlspecialchars($message); ?></span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white border border-slate-200 rounded-lg">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Master Data</p>
                    <h2 class="text-sm font-semibold text-slate-900">Create Category</h2>
                </div>
                <div class="p-4 text-sm">
                    <form method="POST" class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Name</label>
                            <input type="text" name="name" class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-purple-500" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Type</label>
                            <select name="type" class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-purple-500">
                                <option value="income">Income</option>
                                <option value="expense" selected>Expense</option>
                            </select>
                        </div>
                        <button type="submit" name="add" class="w-full bg-purple-600 hover:bg-purple-700 text-white text-xs font-medium py-2 rounded-md flex items-center justify-center gap-2">
                            <i class="fas fa-plus text-xs"></i><span>Add Category</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white border border-slate-200 rounded-lg">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <div>
                        <p class="text-[11px] uppercase tracking-wide text-slate-500">Chart of Accounts</p>
                        <h2 class="text-sm font-semibold text-slate-900">Configured Categories</h2>
                    </div>
                </div>
                <div class="divide-y divide-slate-100">
                    <?php while ($row = $categories->fetch_assoc()): ?>
                        <div class="px-4 py-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-900"><?php echo htmlspecialchars($row['name']); ?></p>
                                <p class="text-[11px] text-slate-500">
                                    Type:
                                    <span class="font-medium <?php echo $row['type'] === 'income' ? 'text-emerald-600' : 'text-rose-600'; ?>">
                                        <?php echo htmlspecialchars($row['type']); ?>
                                    </span>
                                </p>
                            </div>
                            <form method="POST" onsubmit="return confirm('Delete this category?');">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="delete" class="text-rose-600 hover:text-rose-800 text-xs font-medium">
                                    <i class="fas fa-trash-alt mr-1"></i>Delete
                                </button>
                            </form>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$page_content = ob_get_clean();
include 'layout.php';
