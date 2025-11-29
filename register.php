<?php
require_once 'config.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password_hash'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $username;
                redirect('dashboard.php');
            } else {
                $error = 'Invalid credentials';
            }
        } else {
            $error = 'Invalid credentials';
        }
        $stmt->close();
    } else {
        $error = 'Please fill all fields';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Finance Manager</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center">
<div class="w-full max-w-md">
    <div class="bg-white shadow-xl rounded-xl border border-slate-200 overflow-hidden">
        <div class="bg-slate-900 px-6 py-4 flex items-center gap-3">
            <div class="bg-blue-600 p-2 rounded-md">
                <i class="fas fa-building text-white text-xl"></i>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wider text-slate-400">Institution Finance Suite</p>
                <h1 class="text-white font-semibold text-lg">Login Console</h1>
            </div>
        </div>
        <div class="px-6 py-6">
            <p class="text-xs uppercase tracking-wide text-slate-500 mb-1">Authentication</p>
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Sign in to continue</h2>

            <?php if ($error): ?>
                <div class="mb-4 p-3 rounded-md bg-rose-50 border border-rose-200 text-xs text-rose-700 flex items-start gap-2">
                    <i class="fas fa-circle-exclamation mt-0.5"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4 text-sm">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Username</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                        <input type="text" name="username" class="w-full pl-8 pr-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                        <input type="password" name="password" class="w-full pl-8 pr-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 rounded-md flex items-center justify-center gap-2">
                    <i class="fas fa-right-to-bracket text-xs"></i>
                    <span>Sign In</span>
                </button>
            </form>

            <p class="mt-4 text-[11px] text-slate-500 text-center">
                New user?
                <a href="register.php" class="text-blue-600 hover:text-blue-700 font-medium">Create an account</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>
