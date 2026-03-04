<?php
session_start();
require_once '../utils/db.php';
require_once 'fpdf186/fpdf.php';
require_once 'utils/mailer.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transaction_id'], $_POST['email'])) {
    $transaction_id = intval($_POST['transaction_id']);
    $email = trim($_POST['email']);
    // Fetch transaction
    $stmt = $conn->prepare('SELECT * FROM transactions WHERE id = ?');
    $stmt->bind_param('i', $transaction_id);
    $stmt->execute();
    $transaction = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    // Fetch sales for this transaction
    $sales = $conn->query('SELECT s.*, p.name AS product_name FROM sales s JOIN products p ON s.product_id = p.id WHERE s.transaction_id = ' . $transaction_id);
    // Generate PDF in memory
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Sales Receipt', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 8, 'Date: ' . $transaction['transaction_date'], 0, 1);
    $pdf->Cell(0, 8, 'Buyer: ' . $transaction['buyer_name'] . ' (' . $transaction['buyer_contact'] . ')', 0, 1);
    $pdf->Ln(4);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(40, 8, 'Product', 1);
    $pdf->Cell(20, 8, 'Boxes', 1);
    $pdf->Cell(25, 8, 'Items/Box', 1);
    $pdf->Cell(20, 8, 'Loose', 1);
    $pdf->Cell(25, 8, 'Quantity', 1);
    $pdf->Cell(30, 8, 'Price/Unit', 1);
    $pdf->Cell(30, 8, 'Total', 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 11);
    $grand_total = 0;
    $sales->data_seek(0);
    while ($row = $sales->fetch_assoc()) {
        $pdf->Cell(40, 8, $row['product_name'], 1);
        $pdf->Cell(20, 8, $row['boxes_sold'], 1);
        $pdf->Cell(25, 8, $row['items_per_box_sold'], 1);
        $pdf->Cell(20, 8, $row['loose_items_sold'], 1);
        $pdf->Cell(25, 8, $row['quantity_sold'], 1);
        $pdf->Cell(30, 8, number_format($row['price_per_unit'], 0) . ' Frw', 1);
        $pdf->Cell(30, 8, number_format($row['total'], 0) . ' Frw', 1);
        $pdf->Ln();
        $grand_total += $row['total'];
    }
    $pdf->Ln(4);
    $pdf->SetFont('Arial', 'B', 12);
    $total_amount = $grand_total;
    $amount_paid = intval($transaction['amount_paid']);
    $outstanding = $total_amount - $amount_paid;
    $pdf->Cell(0, 8, 'Total Amount: ' . number_format($total_amount, 0) . ' Frw', 0, 1);
    $pdf->Cell(0, 8, 'Amount Paid: ' . number_format($amount_paid, 0) . ' Frw', 0, 1);
    $pdf->Cell(0, 8, 'Outstanding: ' . number_format($outstanding, 0) . ' Frw', 0, 1);
    $pdf->Cell(0, 8, 'Payment Status: ' . ucfirst($transaction['payment_status']), 0, 1);
    $pdfdoc = $pdf->Output('S');
    // Send email with PDF attachment
    $subject = 'Your Sales Receipt from Quincaillerie Manager';
    $body = "Dear " . htmlspecialchars($transaction['buyer_name']) . ",\n\nPlease find attached your sales receipt.\n\n";
    $body .= "Total Amount: " . number_format($total_amount, 0) . " Frw\n";
    $body .= "Amount Paid: " . number_format($amount_paid, 0) . " Frw\n";
    $body .= "Outstanding: " . number_format($outstanding, 0) . " Frw\n";
    $body .= "Payment Status: " . ucfirst($transaction['payment_status']) . "\n\nThank you for your business!";
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        // SMTP config
        $mail->isSMTP();
        $mail->Host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = getenv('SMTP_USER') ?: 'josephmanizabayo7@gmail.com'; 
        $mail->Password = getenv('SMTP_PASS') ?: 'xibc jqdf xapm ausl';         
        $mail->SMTPSecure = getenv('SMTP_SECURE') ?: 'ssl';                       
        $mail->Port = getenv('SMTP_PORT') ?: 465;                               

        $mail->setFrom(getenv('SMTP_FROM_EMAIL') ?: 'josephmanizabayo7@gmail.com', getenv('SMTP_FROM_NAME') ?: 'Quincaillerie Manager');
        $mail->addAddress($email);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->addStringAttachment($pdfdoc, 'receipt_' . $transaction_id . '.pdf');
        $mail->send();
        echo '<div style="max-width:600px;margin:40px auto;padding:32px;background:#fff;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,0.08);text-align:center;">Receipt sent successfully to ' . htmlspecialchars($email) . '.<br><a href="javascript:history.back()" class="btn" style="margin-top:18px;">⬅ Back</a></div>';
    } catch (Exception $e) {
        echo '<div style="max-width:600px;margin:40px auto;padding:32px;background:#fff;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,0.08);text-align:center;">Failed to send email: ' . htmlspecialchars($mail->ErrorInfo) . '<br><a href="javascript:history.back()" class="btn" style="margin-top:18px;">⬅ Back</a></div>';
    }
    exit();
} else {
    header('Location: transactions.php');
    exit();
} 
