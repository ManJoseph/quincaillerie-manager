<?php
session_start();
require_once '../utils/db.php';
require_once 'fpdf186/fpdf.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}
$transaction_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$transaction_id) {
    echo 'Transaction not found.';
    exit();
}
// Fetch transaction
$stmt = $conn->prepare('SELECT * FROM transactions WHERE id = ?');
$stmt->bind_param('i', $transaction_id);
$stmt->execute();
$transaction = $stmt->get_result()->fetch_assoc();
$stmt->close();
// Fetch sales for this transaction
$sales = $conn->query('SELECT s.*, p.name AS product_name FROM sales s JOIN products p ON s.product_id = p.id WHERE s.transaction_id = ' . $transaction_id);
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
$pdf->Output('D', 'receipt_' . $transaction_id . '.pdf');
exit(); 
