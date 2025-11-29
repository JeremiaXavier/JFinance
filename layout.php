<?php if (!isset($page_title)) { $page_title = 'Finance Manager'; } ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($page_title); ?> - Finance Manager</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 text-slate-900">
<div class="min-h-screen flex">
    <aside class="w-64 bg-slate-900 text-slate-100 flex flex-col">
        <div class="h-16 flex items-center px-4 border-b border-slate-800">
            <div class="flex items-center gap-2">
                <div class="bg-blue-600 p-2 rounded-md">
                    <i class="fas fa-building text-white text-lg"></i>
                </div>
                <div>
                    <p class="text-sm uppercase tracking-wide text-slate-400">Finance Suite</p>
                    <p class="font-semibold text-sm">Institution Edition</p>
                </div>
            </div>
        </div>
        <nav class="flex-1 overflow-y-auto py-4">
            <?php $current = basename($_SERVER['PHP_SELF']); ?>
            <p class="px-4 text-[11px] uppercase tracking-wide text-slate-500 mb-2">Main</p>
            <a href="dashboard.php" class="flex items-center px-4 py-2 text-sm <?php echo $current === 'dashboard.php' ? 'bg-slate-800 text-blue-300' : 'text-slate-200 hover:bg-slate-800'; ?>">
                <i class="fas fa-gauge-high mr-3 w-4 text-blue-400"></i>Dashboard
            </a>
            <p class="px-4 text-[11px] uppercase tracking-wide text-slate-500 mt-4 mb-2">Transactions</p>
            <a href="income.php" class="flex items-center px-4 py-2 text-sm <?php echo $current === 'income.php' ? 'bg-slate-800 text-blue-300' : 'text-slate-200 hover:bg-slate-800'; ?>">
                <i class="fas fa-arrow-trend-up mr-3 w-4 text-emerald-400"></i>Income
            </a>
            <a href="expense.php" class="flex items-center px-4 py-2 text-sm <?php echo $current === 'expense.php' ? 'bg-slate-800 text-blue-300' : 'text-slate-200 hover:bg-slate-800'; ?>">
                <i class="fas fa-arrow-trend-down mr-3 w-4 text-rose-400"></i>Expense
            </a>
            <a href="debt.php" class="flex items-center px-4 py-2 text-sm <?php echo $current === 'debt.php' ? 'bg-slate-800 text-blue-300' : 'text-slate-200 hover:bg-slate-800'; ?>">
                <i class="fas fa-credit-card mr-3 w-4 text-amber-400"></i>Debt
            </a>
            <p class="px-4 text-[11px] uppercase tracking-wide text-slate-500 mt-4 mb-2">Controls</p>
            <a href="budget.php" class="flex items-center px-4 py-2 text-sm <?php echo $current === 'budget.php' ? 'bg-slate-800 text-blue-300' : 'text-slate-200 hover:bg-slate-800'; ?>">
                <i class="fas fa-bullseye mr-3 w-4 text-purple-400"></i>Budget
            </a>
            <a href="category.php" class="flex items-center px-4 py-2 text-sm <?php echo $current === 'category.php' ? 'bg-slate-800 text-blue-300' : 'text-slate-200 hover:bg-slate-800'; ?>">
                <i class="fas fa-tags mr-3 w-4 text-indigo-400"></i>Categories
            </a>
        </nav>
        <div class="border-t border-slate-800 px-4 py-3 text-xs text-slate-400">
            <p class="mb-1">Signed in as</p>
            <p class="font-semibold text-slate-100 text-sm"><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></p>
            <a href="logout.php" class="mt-3 inline-flex items-center text-rose-400 hover:text-rose-300 text-xs">
                <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6">
            <div>
                <p class="text-[11px] uppercase tracking-wide text-slate-500">Finance / <?php echo htmlspecialchars($page_section ?? 'Dashboard'); ?></p>
                <h1 class="text-lg font-semibold text-slate-900"><?php echo htmlspecialchars($page_title); ?></h1>
            </div>
            <div class="flex items-center gap-4">
                <div class="hidden md:flex items-center bg-slate-100 px-3 py-1.5 rounded-md text-xs text-slate-500">
                    <i class="fas fa-search mr-2 text-slate-400"></i>
                    <input type="text" placeholder="Search in module..." class="bg-transparent focus:outline-none text-xs w-40">
                </div>
                <div class="flex items-center gap-2">
                    <div class="text-right">
                        <p class="text-xs font-medium text-slate-700"><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></p>
                        <p class="text-[11px] text-slate-400">Finance Officer</p>
                    </div>
                    <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center text-xs font-semibold text-white">
                        <?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6">
            <?php echo $page_content ?? ''; ?>
        </main>
    </div>
</div>
</body>
</html>
