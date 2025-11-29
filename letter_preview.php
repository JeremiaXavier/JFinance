<?php
require_once 'config.php';
if (!is_logged_in()) { redirect('login.php'); }

$user_id   = get_user_id();
$letter_id = intval($_GET['id'] ?? 0);

if ($letter_id === 0) {
    die('Invalid letter ID');
}

// Fetch letter from database
$stmt = $conn->prepare("SELECT * FROM letters WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $letter_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$letter = $result->fetch_assoc();
$stmt->close();

if (!$letter) {
    die('Letter not found');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Letter Preview</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            height: 100%;
        }
        body {
            font-family: "Times New Roman", serif;
            line-height: 1.5;
            color: #111827;
            background: #808080;
        }
        .toolbar {
            max-width: 210mm;
            margin: 8px auto;
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }
        .toolbar button {
            padding: 6px 12px;
            font-size: 12px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-print {
            background: #2563eb;
            color: #ffffff;
        }
        .btn-print:hover {
            background: #1d4ed8;
        }
        .btn-download {
            background: #10b981;
            color: #ffffff;
        }
        .btn-download:hover {
            background: #059669;
        }
        .btn-back {
            background: #e5e7eb;
            color: #111827;
        }
        .btn-back:hover {
            background: #d1d5db;
        }

        /* A4 page */
        .page {
            width: 210mm;
            height: 297mm;
            margin: 8px auto 16px;
            background: #ffffff;
            box-shadow: 0 0 6px rgba(0,0,0,0.4);
            display: flex;
            flex-direction: column;
        }

        /* Top black bar with logo (matches TCPDF header) */
        .page-header {
            height: 20mm;
            background: #000000;
            color: #ffffff;
            display: flex;
            align-items: center;
            padding: 0 15mm;
        }
        .page-header-logo {
            display: flex;
        
            align-items: center;
            justify-content: center;
            margin-right: 6mm;
        }
        .page-header-logo img {
            height: 5mm;
            width: auto;
            display: block;
        }
        .page-header-text-main {
            font-size: 11pt;
            font-weight: bold;
        }
        .page-header-text-sub {
            font-size: 8pt;
            opacity: 0.9;
        }

        /* Bottom line under header */
        .page-header-line {
            height: 0;
            border-bottom: 1px solid #000000;
            margin: 0 10mm;
        }

        /* Content area (similar to TCPDF margins) */
        .page-body {
            flex: 1;
            padding: 10mm 15mm 20mm;
            font-size: 11pt;
        }
        .date-ref {
            font-size: 9pt;
            color: #4b5563;
            margin-bottom: 10pt;
        }
        .recipient {
            margin-bottom: 12pt;
            font-size: 10.5pt;
        }
        .salutation {
            margin: 8pt 0 10pt;
        }
        .subject {
            color: #003366;
            font-weight: bold;
            margin-bottom: 8pt;
            font-size: 10.5pt;
        }
        .body {
            text-align: justify;
            margin-bottom: 24pt;
            font-size: 10.5pt;
            white-space: pre-line;
        }
        .signature {
            margin-top: 32pt;
            font-size: 10pt;
        }
        .signature-line {
            width: 45mm;
            border-top: 1px solid #111827;
            margin: 24pt 0 4pt;
        }

        @media print {
            body {
                background: #ffffff;
            }
            .toolbar {
                display: none;
            }
            .page {
                margin: 0;
                box-shadow: none;
                width: 210mm;
                height: 297mm;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="btn-print" onclick="window.print()">🖨️ Print / Save as PDF</button>
        <button class="btn-download" onclick="downloadPDF()">⬇️ Download PDF</button>
        <button class="btn-back" onclick="history.back()">← Back</button>
    </div>

    <div class="page">
        <div class="page-header">
            <div class="page-header-logo">
                <img src="https://jeremiaxavier.github.io/jstatic/logowhite.png" alt="Logo">
            </div>
            
        </div>
        <div class="page-header-line"></div>

        <div class="page-body">
            <div class="date-ref">
                <strong>Date:</strong>
                <?php
                    $date_obj = new DateTime($letter['letter_date']);
                    echo $date_obj->format('d F Y');
                ?><br>
                <strong>Ref:</strong>
                <?php echo date('Y'); ?>/<?php echo str_pad($letter['user_id'], 3, '0', STR_PAD_LEFT); ?>/<?php echo $letter['id']; ?>
            </div>

            <div class="recipient">
                <strong><?php echo htmlspecialchars($letter['recipient_name']); ?></strong><br>
                <?php if (!empty($letter['recipient_address'])): ?>
                    <?php echo nl2br(htmlspecialchars($letter['recipient_address'])); ?><br>
                <?php endif; ?>
            </div>

            <div class="salutation">Dear Sir/Madam,</div>

            <?php if (!empty($letter['subject'])): ?>
                <div class="subject">Subject: <?php echo htmlspecialchars($letter['subject']); ?></div>
            <?php endif; ?>

            <div class="body">
                <?php echo nl2br(htmlspecialchars($letter['body'])); ?>
            </div>

            <div class="signature">
                <p>Yours faithfully,</p>
                <div class="signature-line"></div>
                <p><strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
                <p><em>For, Jeremia Xavier Softwares.</em></p>
            </div>
        </div>
    </div>

    <script>
        function downloadPDF() {
            window.location.href = 'letter_pdf.php?id=<?php echo $letter_id; ?>';
        }
    </script>
</body>
</html>
