<?php include 'views/layouts/header.php'; ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-calendar-check"></i></div>
        <div class="stat-info">
            <h3><?= count($appointments) ?></h3>
            <p>Today's Patients</p>
        </div>
    </div>
    <!-- Add more stats -->
</div>

<div class="table-container">
    <div class="table-header">
        <h3>Today's Interface</h3>
        <a href="?route=receptionist/add_patient" class="btn btn-primary"><i class="fas fa-plus"></i> New Patient</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Token</th>
                <th>Patient Name</th>
                <th>Assigned Doctor</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($appointments as $appt): ?>
            <tr>
                <td><span style="font-weight: 800; font-size: 18px; color: var(--primary);">#<?= $appt['token_number'] ?></span></td>
                <td><?= $appt['patient_name'] ?></td>
                <td><?= $appt['doctor_name'] ?></td>
                <td>
                    <?php if($appt['status'] == 'waiting'): ?>
                        <span class="badge waiting">Waiting</span>
                    <?php elseif($appt['status'] == 'completed'): ?>
                        <span class="badge completed">Completed</span>
                    <?php endif; ?>
                </td>
                <td>
                    <!-- If completed, show Print Prescription -->
                    <?php if($appt['status'] == 'completed'): ?>
                        <a href="?route=doctor/print_prescription&id=<?= $appt['id'] ?>" target="_blank" class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;"><i class="fas fa-print"></i> Print</a>
                    <?php else: ?>
                        <!-- Maybe Print Token? -->
                        <!-- Print Token -->
                        <a href="?route=receptionist/print_token&id=<?= $appt['id'] ?>" target="_blank" class="btn btn-secondary" style="padding: 5px 10px; font-size: 12px; background: #6c757d; color: white;"><i class="fas fa-ticket-alt"></i> Print Token</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'views/layouts/footer.php'; ?>
