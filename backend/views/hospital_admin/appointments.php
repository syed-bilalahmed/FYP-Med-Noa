<?php include 'views/layouts/header.php'; ?>

<?php if(isset($db_error)): ?>
<div style="background:#fff3cd;color:#856404;padding:10px 15px;border-radius:8px;margin-bottom:15px;font-size:13px;">⚠️ <?= $db_error ?></div>
<?php endif; ?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <h3>Appointments</h3>
    <span class="badge bg-primary fs-6"><?= $total_count ?? count($appointments ?? []) ?> Total</span>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Token</th>
                <th>Patient</th>
                <th>Phone</th>
                <th>Doctor</th>
                <th>Type</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($appointments)): ?>
                <tr>
                    <td colspan="8" class="text-center p-5">
                        <i class="fas fa-calendar-times fa-3x text-muted d-block mb-3" style="opacity:0.3;"></i>
                        <div class="text-muted">No appointments found.</div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($appointments as $a): ?>
                <?php
                    $st = strtolower(trim($a['status'] ?? 'pending'));
                    $styles = [
                        'pending'   => 'background:#fff3cd;color:#856404;',
                        'confirmed' => 'background:#d1e7dd;color:#0f5132;',
                        'completed' => 'background:#cff4fc;color:#055160;',
                        'cancelled' => 'background:#f8d7da;color:#842029;',
                    ];
                    $badgeStyle = $styles[$st] ?? 'background:#e2e3e5;color:#383d41;';
                ?>
                <tr>
                    <td><span class="badge bg-secondary">#<?= htmlspecialchars($a['token_number'] ?? '—') ?></span></td>
                    <td><strong><?= htmlspecialchars($a['patient_name'] ?? 'Unknown') ?></strong></td>
                    <td><small class="text-muted"><?= htmlspecialchars($a['patient_phone'] ?? '—') ?></small></td>
                    <td><small><?= htmlspecialchars($a['doctor_name'] ?? '—') ?></small></td>
                    <td>
                        <span class="badge" style="background:#cff4fc;color:#055160;">
                            <?= ucfirst($a['type'] ?? 'Consultation') ?>
                        </span>
                    </td>
                    <td><small><?= $a['date'] ? date('M d, Y', strtotime($a['date'])) : '—' ?></small></td>
                    <td>
                        <span class="badge" style="<?= $badgeStyle ?>padding:5px 10px;border-radius:6px;font-weight:600;">
                            <?= ucfirst($st) ?>
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:5px;flex-wrap:wrap;">
                            <?php if ($st === 'pending' || $st === ''): ?>
                                <a href="?route=hospital_admin/update_appointment&id=<?= $a['id'] ?>&status=confirmed"
                                   class="btn btn-sm btn-success" title="Confirm">
                                    <i class="fas fa-check"></i> Confirm
                                </a>
                                <a href="?route=hospital_admin/update_appointment&id=<?= $a['id'] ?>&status=cancelled"
                                   class="btn btn-sm btn-danger" title="Cancel"
                                   onclick="return confirm('Cancel this appointment?')">
                                    <i class="fas fa-times"></i>
                                </a>
                            <?php elseif ($st === 'confirmed'): ?>
                                <a href="?route=hospital_admin/update_appointment&id=<?= $a['id'] ?>&status=completed"
                                   class="btn btn-sm btn-primary" title="Mark Done">
                                    <i class="fas fa-check-double"></i> Done
                                </a>
                                <a href="?route=hospital_admin/update_appointment&id=<?= $a['id'] ?>&status=cancelled"
                                   class="btn btn-sm btn-outline-danger" title="Cancel"
                                   onclick="return confirm('Cancel this appointment?')">
                                    <i class="fas fa-times"></i>
                                </a>
                            <?php else: ?>
                                <div class="d-flex align-items-center gap-1">
                                    <span class="text-muted small">—</span>
                                    <?php if ($st !== 'completed' && $st !== 'cancelled'): ?>
                                        <a href="?route=hospital_admin/update_appointment&id=<?= $a['id'] ?>&status=cancelled" 
                                           class="btn btn-sm btn-link text-danger p-0" title="Force Cancel">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$totalPages  = $total_pages ?? 1;
$currentPage = $current_page ?? 1;
if ($totalPages > 1):
?>
<nav class="mt-4 d-flex justify-content-center">
    <ul class="pagination">
        <?php if ($currentPage > 1): ?>
        <li class="page-item">
            <a class="page-link" href="?route=hospital_admin/appointments&page=<?= $currentPage - 1 ?>">‹ Prev</a>
        </li>
        <?php endif; ?>

        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
        <li class="page-item <?= $p === $currentPage ? 'active' : '' ?>">
            <a class="page-link" href="?route=hospital_admin/appointments&page=<?= $p ?>"><?= $p ?></a>
        </li>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
        <li class="page-item">
            <a class="page-link" href="?route=hospital_admin/appointments&page=<?= $currentPage + 1 ?>">Next ›</a>
        </li>
        <?php endif; ?>
    </ul>
</nav>
<?php endif; ?>

<?php include 'views/layouts/footer.php'; ?>
