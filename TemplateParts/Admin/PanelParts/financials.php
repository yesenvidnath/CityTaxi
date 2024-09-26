<?php
include_once 'Functions/Common/Database.php';
include_once 'Functions/Common/Financial.php';

$financial = new Financial();

// Fetching all transactions
$transactions = $financial->fetchAllTransactions();

?>

<!-- Main Content -->
<div class="container mt-3">
    <div class="row">
        <h2>Financial Management</h2>
    </div>
</div>

<!-- Content here -->
<div class="container mt-3">
    <div class="row">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Amount</th>
                    <th>Driver Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?= htmlspecialchars($transaction['Payment_ID']) ?></td>
                    <td><?= htmlspecialchars($transaction['Payment_date']) ?></td>
                    <td><?= htmlspecialchars($transaction['Payment_time']) ?></td>
                    <td><?= htmlspecialchars($transaction['Amount']) ?></td>
                    <td><?= htmlspecialchars($transaction['First_name'] . " " . $transaction['Last_name']) ?></td>
                    <td>
                        <a href="generateInvoice.php?paymentId=<?= $transaction['Payment_ID'] ?>" class="btn btn-info">Generate Invoice</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
