<?php
require_once 'config.php';
if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = get_user_id();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate'])) {
    $recipient_name = $_POST['recipient_name'] ?? '';
    $recipient_address = $_POST['recipient_address'] ?? '';
    $letter_subject = $_POST['letter_subject'] ?? '';
    $letter_body = $_POST['letter_body'] ?? '';
    $letter_date = $_POST['letter_date'] ?? date('Y-m-d');

    if (!empty($recipient_name) && !empty($letter_body)) {
        // Store letter in database for reference
        $stmt = $conn->prepare("INSERT INTO letters (user_id, recipient_name, recipient_address, subject, body, letter_date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $user_id, $recipient_name, $recipient_address, $letter_subject, $letter_body, $letter_date);
        if ($stmt->execute()) {
            $letter_id = $conn->insert_id;
            // Redirect to PDF view
            redirect('letter_preview.php?id=' . $letter_id);
        }
        $stmt->close();
    } else {
        $message = 'Please fill all required fields.';
    }
}

$page_title = 'Letter Generator';
$page_section = 'Finance Module';

ob_start();
?>
<div class="space-y-4">

    <?php if ($message): ?>
        <div class="border border-red-300 bg-red-50 text-red-800 px-3 py-2 text-[12px]">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="bg-white border border-[#d1d5db]">
        <div class="bg-[#f9fafb] border-b border-[#e5e7eb] px-3 py-2">
            <span class="text-[12px] font-semibold text-[#111827]">Generate Official Letter</span>
        </div>
        <div class="p-4">
            <form method="POST" class="space-y-3">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#374151] mb-1">Letter Date</label>
                        <input type="date" name="letter_date" value="<?php echo date('Y-m-d'); ?>"
                            class="w-full border border-[#9ca3af] px-2 py-1.5 text-[13px] focus:outline-none focus:ring-1 focus:ring-[#2563eb]">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#374151] mb-1">Reference Number</label>
                        <input type="text" placeholder="Auto-generated" disabled
                            class="w-full border border-[#9ca3af] px-2 py-1.5 text-[13px] bg-[#f3f4f6]"
                            value="FM/<?php echo date('Y'); ?>/<?php echo str_pad($user_id, 3, '0', STR_PAD_LEFT); ?>">
                    </div>
                </div>

                <div>
                    <label class="block text-[12px] font-semibold text-[#374151] mb-1">Recipient Name *</label>
                    <input type="text" name="recipient_name"
                        class="w-full border border-[#9ca3af] px-2 py-1.5 text-[13px] focus:outline-none focus:ring-1 focus:ring-[#2563eb]"
                        required>
                </div>

                <div>
                    <label class="block text-[12px] font-semibold text-[#374151] mb-1">Recipient Address</label>
                    <textarea name="recipient_address" rows="3"
                        class="w-full border border-[#9ca3af] px-2 py-1.5 text-[13px] focus:outline-none focus:ring-1 focus:ring-[#2563eb]"></textarea>
                </div>

                <div>
                    <label class="block text-[12px] font-semibold text-[#374151] mb-1">Letter Subject</label>
                    <input type="text" name="letter_subject"
                        class="w-full border border-[#9ca3af] px-2 py-1.5 text-[13px] focus:outline-none focus:ring-1 focus:ring-[#2563eb]">
                </div>

                <div>
                    <label class="block text-[12px] font-semibold text-[#374151] mb-1">Letter Body *</label>
                    <textarea name="letter_body" rows="10"
                        class="w-full border border-[#9ca3af] px-2 py-1.5 text-[13px] focus:outline-none focus:ring-1 focus:ring-[#2563eb] font-serif"
                        placeholder="Dear Sir/Madam,

Please write your letter content here...

Yours faithfully,"></textarea>
                </div>

                <div class="border-t border-[#e5e7eb] pt-3 flex gap-2">
                    <button type="submit" name="generate"
                        class="bg-[#2563eb] hover:bg-[#1d4ed8] text-white px-4 py-1.5 text-[12px] font-semibold">
                        Generate PDF Letter
                    </button>
                    <button type="reset"
                        class="bg-[#e5e7eb] hover:bg-[#d1d5db] text-[#111827] px-4 py-1.5 text-[12px] font-semibold">
                        Clear Form
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
<?php
$page_content = ob_get_clean();
include 'layout.php';
?>