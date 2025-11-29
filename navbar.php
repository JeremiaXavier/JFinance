<nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center gap-2">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-2 rounded-lg">
                    <i class="fas fa-chart-line text-white text-xl"></i>
                </div>
                <span class="text-xl font-bold text-gray-900">JFinance</span>
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center gap-1">
                <a href="dashboard.php" class="px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'bg-gray-100 text-blue-600' : ''; ?>">
                    <i class="fas fa-chart-pie mr-2"></i>Dashboard
                </a>
                <a href="income.php" class="px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition <?php echo basename($_SERVER['PHP_SELF']) === 'income.php' ? 'bg-gray-100 text-green-600' : ''; ?>">
                    <i class="fas fa-arrow-trend-up mr-2"></i>Income
                </a>
                <a href="expense.php" class="px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition <?php echo basename($_SERVER['PHP_SELF']) === 'expense.php' ? 'bg-gray-100 text-red-600' : ''; ?>">
                    <i class="fas fa-arrow-trend-down mr-2"></i>Expense
                </a>
                <a href="debt.php" class="px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition <?php echo basename($_SERVER['PHP_SELF']) === 'debt.php' ? 'bg-gray-100 text-orange-600' : ''; ?>">
                    <i class="fas fa-credit-card mr-2"></i>Debt
                </a>
                <a href="budget.php" class="px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition <?php echo basename($_SERVER['PHP_SELF']) === 'budget.php' ? 'bg-gray-100 text-purple-600' : ''; ?>">
                    <i class="fas fa-bullseye mr-2"></i>Budget
                </a>
                <a href="category.php" class="px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition <?php echo basename($_SERVER['PHP_SELF']) === 'category.php' ? 'bg-gray-100 text-indigo-600' : ''; ?>">
                    <i class="fas fa-tags mr-2"></i>Category
                </a>
            </div>

            <!-- User Menu -->
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600">Welcome, <span class="font-semibold"><?php echo htmlspecialchars($_SESSION['username']); ?></span></span>
                <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                    <i class="fas fa-sign-out-alt"></i>Logout
                </a>
            </div>
        </div>
    </div>
</nav>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
