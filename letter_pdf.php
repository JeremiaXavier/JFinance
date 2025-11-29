<?php
require_once 'config.php';
if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = get_user_id();
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

// Path to TCPDF
$tcpdf_path = 'includes/tcpdf/tcpdf.php';

if (file_exists($tcpdf_path)) {
    // ================= PDF VERSION (TCPDF) =================
    require_once $tcpdf_path;

    class MYPDF extends TCPDF
    {
        public function Header()
        {
            // Top black bar
            $this->SetFillColor(0, 0, 0);
            $this->Rect(0, 0, $this->getPageWidth(), 20, 'F');

            // Online logo URL (TCPDF can fetch this directly)
            $logoUrl = 'https://jeremiaxavier.github.io/jstatic/logowhite.png';

            try {
                // TCPDF Image() method supports HTTP/HTTPS URLs directly
                $this->Image(
                    $logoUrl,
                    10,      // x position
                    8,       // y position
                    50,      // width (mm)
                    '',      // auto height
                    'PNG',   // image type
                    '',      // link
                    '',      // align
                    false,   // isAbsoluteUrl
                    300,     // dpi
                    '',      // pallete
                    false,
                    false,
                    0,
                    false,
                    false,
                    false
                );
            } catch (Exception $e) {
                // Logo failed to load, continue anyway
            }

            // Company name + subtitle
            /* $this->SetTextColor(255, 255, 255);
            $this->SetFont('helvetica', 'B', 11);
            $this->SetXY(30, 4);
            $this->Cell(0, 5, 'Jeremia Xavier Softwares', 0, 1, 'L');

            $this->SetFont('helvetica', '', 8);
            $this->SetX(30);
            $this->Cell(0, 4, 'Corporate Communications', 0, 1, 'L');

            // Thin line under bar
            $this->SetDrawColor(0, 0, 0);
            $this->Line(10, 20, $this->getPageWidth() - 10, 20);
 */
            // Move cursor below header
            $this->SetY(26);
        }


        public function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('helvetica', 'I', 8);
            $this->SetTextColor(120, 120, 120);
            $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
        }
    }

    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(15, 30, 15);     // top 30mm (header uses first 20mm)
    $pdf->SetAutoPageBreak(true, 20);
    $pdf->AddPage();

    // Date
    $pdf->SetFont('helvetica', '', 11);
    $pdf->SetTextColor(17, 24, 39);
    $date_obj = new DateTime($letter['letter_date']);
    $pdf->MultiCell(0, 5, 'Date: ' . $date_obj->format('d F Y'), 0, 'L');
    $pdf->Ln(5);

    // Reference
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(107, 114, 128);
    $pdf->MultiCell(
        0,
        4,
        'Ref: ' . date('Y') . '/' . str_pad($letter['user_id'], 3, '0', STR_PAD_LEFT) . '/' . $letter['id'],
        0,
        'L'
    );
    $pdf->Ln(5);

    // Recipient
    $pdf->SetFont('helvetica', '', 11);
    $pdf->SetTextColor(17, 24, 39);
    $pdf->MultiCell(0, 5, htmlspecialchars($letter['recipient_name']), 0, 'L');

    if (!empty($letter['recipient_address'])) {
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(0, 4, htmlspecialchars($letter['recipient_address']), 0, 'L');
    }

    $pdf->Ln(5);

    // Salutation
    $pdf->SetFont('helvetica', '', 11);
    $pdf->MultiCell(0, 5, 'Dear Sir/Madam,', 0, 'L');
    $pdf->Ln(3);

    // Subject
    if (!empty($letter['subject'])) {
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetTextColor(0, 51, 102);
        $pdf->MultiCell(0, 5, 'Subject: ' . htmlspecialchars($letter['subject']), 0, 'L');
        $pdf->Ln(3);
        $pdf->SetTextColor(17, 24, 39);
        $pdf->SetFont('helvetica', '', 11);
    }

    // Body
    $pdf->MultiCell(0, 5, htmlspecialchars($letter['body']), 0, 'L');

    $pdf->Ln(8);

    // Signature
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(0, 4, 'Yours faithfully,', 0, 'L');
    $pdf->Ln(15);
    $pdf->SetTextColor(50, 50, 50);
    $pdf->MultiCell(0, 4, '______________________', 0, 'L');
    $pdf->MultiCell(0, 4, htmlspecialchars($_SESSION['username']), 0, 'L');
    $pdf->Ln(3);
    $pdf->SetTextColor(120, 120, 120);
    $pdf->SetFont('helvetica', 'I', 9);
    $pdf->MultiCell(0, 4, 'For, Jeremia Xavier Softwares.', 0, 'L');

    $filename = 'Letter_' . $letter_id . '_' . date('Y-m-d') . '.pdf';
    // 'D' = download, 'I' = inline preview in browser
    $pdf->Output($filename, 'D');

} else {
    // ================ HTML FALLBACK (PREVIEW / PRINT) ================
    ?>
    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="UTF-8">
        <title>Letter</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: "Times New Roman", serif;
                line-height: 1.6;
                color: #111827;
                background: #f3f4f6;
                padding: 20px;
            }

            .letter-container {
                background: white;
                max-width: 8.5in;
                min-height: 11in;
                margin: 20px auto;
                padding: 1in;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            }

            .letterhead {
                display: flex;
                align-items: center;
                border-bottom: 3px solid #000;
                padding-bottom: 10px;
                margin-bottom: 15px;
            }

            .letterhead-logo {
                width: 60px;
                height: 60px;
                background: #000;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 12px;
            }

            .letterhead-logo img {
                max-width: 100%;
                max-height: 100%;
            }

            .letterhead-text {
                font-size: 13px;
            }

            .letterhead-title {
                font-weight: bold;
                font-size: 16px;
            }

            .letterhead-subtitle {
                font-size: 11px;
                color: #4b5563;
            }

            .date-ref {
                font-size: 11px;
                color: #4b5563;
                margin-bottom: 20px;
            }

            .recipient {
                margin-bottom: 20px;
                font-size: 12px;
            }

            .salutation {
                margin-top: 10px;
                margin-bottom: 15px;
            }

            .subject {
                color: #003366;
                font-weight: bold;
                margin-bottom: 10px;
                font-size: 12px;
            }

            .body {
                text-align: justify;
                margin-bottom: 30px;
                font-size: 12px;
            }

            .signature {
                margin-top: 40px;
                font-size: 11px;
            }

            .signature-line {
                width: 150px;
                border-top: 1px solid #111827;
                margin-top: 40px;
                margin-bottom: 5px;
            }

            @media print {
                body {
                    background: white;
                    padding: 0;
                }

                .letter-container {
                    box-shadow: none;
                    margin: 0;
                }

                .print-button {
                    display: none;
                }
            }

            .print-button {
                display: block;
                margin: 20px auto;
                padding: 10px 20px;
                background: #2563eb;
                color: white;
                border: none;
                cursor: pointer;
                border-radius: 4px;
                font-size: 14px;
            }
        </style>
    </head>

    <body>
        <button class="print-button" onclick="window.print()">🖨️ Print Letter</button>

        <div class="letter-container">
            <div class="letterhead">
                <div class="letterhead-logo">
                    <?php if (file_exists('assets/logo.png')): ?>
                        <img src="assets/logo.png" alt="Logo">
                    <?php else: ?>
                        <span style="color:#fff;font-size:11px;">LOGO</span>
                    <?php endif; ?>
                </div>
                <div class="letterhead-text">
                    <div class="letterhead-title">Jeremia Xavier Softwares</div>
                    <div class="letterhead-subtitle">Corporate Communications</div>
                </div>
            </div>

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
    </body>

    </html>
    <?php
}
?>