<?php if (!isset($page_title)) { $page_title = 'Finance Console'; } ?>
<?php if (!isset($page_section)) { $page_section = 'Home'; } ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($page_title); ?> - Finance Console</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-[#f3f4f6] text-[#111827] text-[13px]">
<div class="min-h-screen flex flex-col">

    <header class="bg-[#003366] text-white">
        <div class="max-w-6xl mx-auto px-3 py-2 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 bg-white rounded-sm flex items-center justify-center text-[#003366] text-xs font-bold">
                    JF
                </div>
                <div class="leading-tight">
                    <p class="text-[20px] font-semibold tracking-wide uppercase">JFinance</p>
                    <p class="text-[11px]">Jeremia Xavier Softwares</p>
                </div>
            </div>
            <div class="text-right leading-tight">
                <p class="text-[11px]">
                    Signed in as:
                    <span class="font-semibold">
                        <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
                    </span>
                </p>
            </div>
        </div>
    </header>

    <nav class="bg-[#e5f0fb] border-b border-[#cbd5e1]">
        <div class="max-w-6xl mx-auto px-3 flex flex-wrap items-center text-[12px]">
            <?php $current = basename($_SERVER['PHP_SELF']); ?>
            <?php
                function fm_menu_item($file, $label, $current) {
                    $active = ($current === $file);
                    $classes = $active
                        ? 'border-b-2 border-[#003366] text-[#003366] font-semibold'
                        : 'text-[#1f2937] hover:text-[#003366]';
                    echo '<a href="'.$file.'" class="px-3 py-2 '.$classes.'">'.$label.'</a>';
                }
            ?>
            <?php fm_menu_item('dashboard.php', 'Dashboard', $current); ?>
            <?php fm_menu_item('income.php', 'Income', $current); ?>
            <?php fm_menu_item('expense.php', 'Expense', $current); ?>
            <?php fm_menu_item('debt.php', 'Debt', $current); ?>
            <?php fm_menu_item('budget.php', 'Budget', $current); ?>
            <?php fm_menu_item('category.php', 'Categories', $current); ?>
            <?php fm_side_item('letter_generator.php', 'Letters', $side); ?>

            <div class="flex-1"></div>
            <a href="logout.php" class="px-3 py-2 text-red-700 hover:text-red-900">Logout</a>
        </div>
    </nav>

    <div class="bg-white border-b border-[#e5e7eb]">
        <div class="max-w-6xl mx-auto px-3 py-2 text-[11px] text-[#4b5563]">
            Home &gt; <?php echo htmlspecialchars($page_section); ?> &gt; <?php echo htmlspecialchars($page_title); ?>
        </div>
    </div>

    <div class="flex flex-1">
        <aside class="w-56 bg-[#f9fafb] border-r border-[#d1d5db]">
            <div class="px-3 py-2 border-b border-[#e5e7eb] bg-[#eef2ff]">
                <p class="text-[11px] font-semibold text-[#374151] uppercase">Navigation</p>
            </div>
            <div class="py-2 text-[12px]">
                <?php
                    $side = basename($_SERVER['PHP_SELF']);
                    function fm_side_item($file, $label, $side) {
                        $active = ($side === $file);
                        $classes = $active
                            ? 'bg-[#dbeafe] text-[#1d4ed8] font-semibold border-l-4 border-[#1d4ed8]'
                            : 'text-[#374151] hover:bg-[#e5e7eb] border-l-4 border-transparent';
                        echo '<a href="'.$file.'" class="block px-3 py-2 '.$classes.'">'.$label.'</a>';
                    }
                ?>
                <div class="px-2 py-1 text-[11px] text-[#6b7280] uppercase">Overview</div>
                <?php fm_side_item('dashboard.php', 'Dashboard', $side); ?>

                <div class="mt-2 px-2 py-1 text-[11px] text-[#6b7280] uppercase">Transactions</div>
                <?php fm_side_item('income.php', 'Income Register', $side); ?>
                <?php fm_side_item('expense.php', 'Expense Register', $side); ?>
                <?php fm_side_item('debt.php', 'Debt Register', $side); ?>

                <div class="mt-2 px-2 py-1 text-[11px] text-[#6b7280] uppercase">Controls</div>
                <?php fm_side_item('budget.php', 'Budget Control', $side); ?>
                <?php fm_side_item('category.php', 'Category Master', $side); ?>
                <?php fm_side_item('letter_generator.php', 'Letters', $side); ?>
            </div>
        </aside>

        <main class="flex-1">
            <div class="max-w-6xl mx-auto px-3 py-4">
                <?php echo $page_content ?? ''; ?>
            </div>
        </main>
    </div>

    <footer class="bg-[#e5e7eb] border-t border-[#d1d5db]">
        <div class="max-w-6xl mx-auto px-3 py-2 text-[11px] text-[#4b5563] flex justify-between">
            <span>© <?php echo date('Y'); ?> JFinance. Jeremia Xavier Softwares</span>
            <span>Private use only</span>
        </div>
    </footer>
</div>
</body>
</html>
