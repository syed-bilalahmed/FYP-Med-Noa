<?php include 'views/layouts/header.php'; ?>

<!-- Welcome & Search Section -->
<div class="top-bar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h2 style="font-size: 24px; font-weight: 700;">Welcome to Mednoa!</h2>
        <p style="color: grey; font-size: 14px;">Hospital Admin Dashboard Template</p>
    </div>
    <div style="display: flex; gap: 15px;">
        <input type="text" placeholder="Search here..." class="form-control" style="width: 250px; background: white;">
        <button class="btn btn-primary" style="border-radius: 12px; width: 45px;"><i class="fas fa-cog"></i></button>
    </div>
</div>

<!-- 4 Stats Cards -->
<div class="stats-grid" style="grid-template-columns: repeat(4, 1fr);">
    <div class="stat-card colored red">
        <div class="icon-box"><i class="fas fa-hospital"></i></div>
        <div>
            <p>Total Hospitals</p>
            <h3><?= $total_hospitals ?? 0 ?></h3>
        </div>
    </div>
    <div class="stat-card colored blue">
        <div class="icon-box"><i class="fas fa-clinic-medical"></i></div>
        <div>
            <p>Total Clinics</p>
            <h3><?= $total_clinics ?? 0 ?></h3>
        </div>
    </div>
    <div class="stat-card colored green">
        <div class="icon-box"><i class="fas fa-check-circle"></i></div>
        <div>
            <p>Active Subscriptions</p>
            <h3><?= $active_subscriptions ?? 0 ?></h3>
        </div>
    </div>
    <div class="stat-card colored purple">
        <div class="icon-box"><i class="fas fa-user-md"></i></div>
        <div>
            <p>Total Doctors</p>
            <h3><?= $total_doctors ?? 0 ?></h3>
        </div>
    </div>
</div>

<!-- Recent Activity Section -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
    
    <!-- Recent Hospitals -->
    <div class="card p-20">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h5 style="margin:0; font-weight: 700;">Recent Hospitals / Clinics</h5>
            <a href="?route=admin/hospitals" class="btn btn-sm btn-light">View All</a>
        </div>
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($recent_hospitals) && count($recent_hospitals) > 0): ?>
                    <?php foreach($recent_hospitals as $h): ?>
                    <tr>
                        <td><?= $h['name'] ?></td>
                        <td><small><?= $h['type'] ?? 'Hospital' ?></small></td>
                        <td>
                            <?php if($h['subscription_status'] == 'active'): ?>
                                <span class="badge" style="background: #e1f7e3; color: #34c759;">Active</span>
                            <?php else: ?>
                                <span class="badge" style="background: #fff0f1; color: #ff3b30;"><?= $h['subscription_status'] ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3" class="text-center text-muted">No hospitals yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Recent Doctors -->
    <div class="card p-20">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h5 style="margin:0; font-weight: 700;">New Doctors</h5>
            <a href="?route=admin/doctors" class="btn btn-sm btn-light">View All</a>
        </div>
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Specialization</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($recent_doctors) && count($recent_doctors) > 0): ?>
                    <?php foreach($recent_doctors as $d): ?>
                    <tr>
                        <td>
                            <div style="font-weight: 600;"><?= $d['name'] ?></div>
                            <small class="text-muted"><?= $d['phone'] ?? 'No Phone' ?></small>
                        </td>
                        <td><?= $d['specialization'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="2" class="text-center text-muted">No doctors found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include 'views/layouts/footer.php'; ?>
