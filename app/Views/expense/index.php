<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-sm-12">
            <div class="home-tab">
                <div class="d-sm-flex align-items-center justify-content-between border-bottom">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active ps-0" id="home-tab" data-bs-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">Expense Overview</a>
                        </li>
                    </ul>
                    <div>
                        <div class="btn-wrapper">
                            <a href="<?= base_url('expenses/categories') ?>" class="btn hr-btnbg me-2"><i class="mdi mdi-format-list-bulleted-type"></i> Manage Categories</a>
                            <a href="<?= base_url('expenses/create') ?>" class="btn hr-btnbg me-0"><i class="mdi mdi-plus-circle"></i> Add Expense</a>
                        </div>
                    </div>
                </div>

                <div class="tab-content tab-content-basic">
                    <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview">
                        
                        <!-- Stats Cards -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="statistics-details d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="statistics-title">Total Expense (Month)</p>
                                        <h3 class="rate-percentage text-primary">₹<?= number_format($stats['total_month'], 2) ?></h3>
                                    </div>
                                    <div>
                                        <p class="statistics-title">Total Expense (Year)</p>
                                        <h3 class="rate-percentage text-success">₹<?= number_format($stats['total_year'], 2) ?></h3>
                                    </div>
                                    <div>
                                        <p class="statistics-title">Pending Approvals</p>
                                        <h3 class="rate-percentage text-warning"><?= $stats['pending_count'] ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body py-3">
                                        <form action="<?= base_url('expenses') ?>" method="GET" class="row g-3">
                                            <div class="col-md-2">
                                                <select name="month" class="form-select form-select-sm">
                                                    <option value="">All Months</option>
                                                    <?php for($m=1; $m<=12; $m++): ?>
                                                        <option value="<?= $m ?>" <?= ($filters['month'] ?? '') == $m ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <select name="year" class="form-select form-select-sm">
                                                    <option value="">All Years</option>
                                                    <?php for($y=date('Y'); $y>=2020; $y--): ?>
                                                        <option value="<?= $y ?>" <?= ($filters['year'] ?? '') == $y ? 'selected' : '' ?>><?= $y ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <select name="category_id" class="form-select form-select-sm">
                                                    <option value="">All Categories</option>
                                                    <?php foreach($categories as $cat): ?>
                                                        <option value="<?= $cat['id'] ?>" <?= ($filters['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= $cat['name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <select name="status" class="form-select form-select-sm">
                                                    <option value="">All Status</option>
                                                    <option value="Pending" <?= ($filters['status'] ?? '') == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="Approved" <?= ($filters['status'] ?? '') == 'Approved' ? 'selected' : '' ?>>Approved</option>
                                                    <option value="Rejected" <?= ($filters['status'] ?? '') == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search title or employee..." value="<?= $filters['search'] ?? '' ?>">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="submit" class="btn hr-btnbg btn-sm w-100">Filter</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-8 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title">Recent Expenses</h4>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Title</th>
                                                        <th>Category</th>
                                                        <th>Amount</th>
                                                        <th>Paid By</th>
                                                        <th>Date</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($expenses as $expense): ?>
                                                        <tr>
                                                            <td class="fw-bold"><?= $expense['title'] ?></td>
                                                            <td><label class="badge badge-outline-secondary"><?= $expense['category_name'] ?></label></td>
                                                            <td>₹<?= number_format($expense['amount'], 2) ?></td>
                                                            <td><?= $expense['paid_by'] ? $expense['paid_by_name'] : '<span class="text-info">Company</span>' ?></td>
                                                            <td><?= date('d M, Y', strtotime($expense['expense_date'])) ?></td>
                                                            <td>
                                                                <?php if($expense['status'] == 'Pending'): ?>
                                                                    <label class="badge badge-warning">Pending</label>
                                                                <?php elseif($expense['status'] == 'Approved'): ?>
                                                                    <label class="badge badge-success">Approved</label>
                                                                <?php else: ?>
                                                                    <label class="badge badge-danger">Rejected</label>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <div class="btn-group">
                                                                    <a href="<?= base_url('expenses/edit/'.$expense['id']) ?>" class="btn btn-outline-primary btn-sm"><i class="mdi mdi-pencil"></i></a>
                                                                    <?php if($user->role !== 'employee'): ?>
                                                                        <?php if($expense['status'] == 'Pending'): ?>
                                                                            <a href="<?= base_url('expenses/approve/'.$expense['id']) ?>" class="btn btn-outline-success btn-sm" onclick="return confirm('Approve this expense?')"><i class="mdi mdi-check"></i></a>
                                                                            <a href="<?= base_url('expenses/reject/'.$expense['id']) ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Reject this expense?')"><i class="mdi mdi-close"></i></a>
                                                                        <?php endif; ?>
                                                                        <a href="<?= base_url('expenses/delete/'.$expense['id']) ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure?')"><i class="mdi mdi-delete"></i></a>
                                                                    <?php endif; ?>
                                                                    <?php if(!empty($expense['attachment'])): ?>
                                                                        <a href="<?= base_url('uploads/expenses/'.$expense['attachment']) ?>" target="_blank" class="btn btn-outline-info btn-sm"><i class="mdi mdi-attachment"></i></a>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                    <?php if(empty($expenses)): ?>
                                                        <tr>
                                                            <td colspan="7" class="text-center">No expenses found.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title">Category-wise Expenses</h4>
                                        <canvas id="expenseChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('expenseChart').getContext('2d');
    const chartData = <?= json_encode($chartData) ?>;
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.map(item => item.name),
            datasets: [{
                label: 'Total Expense (₹)',
                data: chartData.map(item => item.total),
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
<?= $this->endSection() ?>
