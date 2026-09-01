<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<style>
    .main-dec-div {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .filtermenu {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .form-select {
        height: 2.44rem;
    }

    /* Stat Cards Styling */
    .stat-card-widget {
        background: #ffffff;
        border-radius: 10px;
        border: 1px solid #eef2f6;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card-widget:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .stat-icon-primary {
        background: rgba(230, 97, 54, 0.12);
        color: #E66136;
    }
    .stat-icon-success {
        background: rgba(40, 167, 69, 0.12);
        color: #28a745;
    }
    .stat-icon-info {
        background: rgba(23, 162, 184, 0.12);
        color: #17a2b8;
    }
    .stat-label {
        font-size: 13px;
        color: #6c757d;
        font-weight: 500;
        margin-bottom: 2px;
    }
    .stat-value {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 0;
        color: #2b3344;
    }

    /* Table & Action Styling */
    #expense-table thead th {
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-size: 0.8rem !important;
        padding: 14px 10px !important;
    }

    .capitalize-text {
        text-transform: capitalize;
    }

    @media (max-width: 767.98px) {
        .filter-sm-res {
            flex-wrap: wrap !important;
        }

        .flex-direction-column {
            flex-direction: column;
        }

        .filter-sm-res h4 {
            flex: 1 1 100%;
            margin-bottom: 10px;
        }

        .filter-sm-res > div {
            flex: 1 1 100%;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-sm-res select, .filter-sm-res input, .filter-sm-res button {
            width: 100% !important;
            min-width: unset !important;
        }

        .btnpdingam {
            padding: 5px !important;
            font-size: 14px !important;
            margin: 7px !important;
        }

        .dataTables_length, .dataTables_filter {
            font-size: 12px !important;
            float: left !important;
        }
    }
</style>

<!-- Top Statistics Row -->
<div class="row g-3 mb-4">
    <div class="col-md-4 col-sm-6 col-12">
        <div class="stat-card-widget">
            <div class="stat-icon-wrapper stat-icon-primary">
                <i class="mdi mdi-calendar-month"></i>
            </div>
            <div>
                <div class="stat-label">Total Expense (This Month)</div>
                <h4 class="stat-value" style="color: #E66136;">&#8377;<?= number_format($stats['total_month'], 2) ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 col-12">
        <div class="stat-card-widget">
            <div class="stat-icon-wrapper stat-icon-success">
                <i class="mdi mdi-chart-line"></i>
            </div>
            <div>
                <div class="stat-label">Total Expense (This Year)</div>
                <h4 class="stat-value" style="color: #28a745;">&#8377;<?= number_format($stats['total_year'], 2) ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-12 col-12">
        <div class="stat-card-widget">
            <div class="stat-icon-wrapper stat-icon-info">
                <i class="mdi mdi-receipt"></i>
            </div>
            <div>
                <div class="stat-label">Filtered Expenses</div>
                <h4 class="stat-value" style="color: #17a2b8;"><?= count($expenses) ?> <span style="font-size: 14px; font-weight: normal; color: #6c757d;">Entries</span></h4>
            </div>
        </div>
    </div>
</div>

<!-- Main Expenses Card -->
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                
                <!-- Card Header with Title & Main Buttons -->
                <div class="d-md-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Manage Expenses</h4>
                    <div class="d-md-flex gap-2 align-items-center mt-2 mt-md-0">
                        <button type="button" id="btnExportExpenses" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-file-excel iconfontsize"></i> Export Excel
                        </button>
                        
                        <?php if ($user->role !== 'employee'): ?>
                            <a href="<?= base_url('expenses/categories') ?>" class="btn btn-secondary attendenceall text-nowrap">
                                <i class="mdi mdi-format-list-bulleted-type iconfontsize"></i> Manage Categories
                            </a>
                        <?php endif; ?>

                        <a href="<?= base_url('expenses/create') ?>" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-plus iconfontsize"></i> Add Expense
                        </a>
                    </div>
                </div>

                <!-- Filters Toolbar Row -->
                <form action="<?= base_url('expenses') ?>" method="GET" class="row g-2 align-items-center mb-4 p-3 bg-light rounded-2">
                    <div class="col-12 col-sm-6 col-md-2">
                        <select name="month" class="form-select shadow-none">
                            <option value="">All Months</option>
                            <?php 
                            for ($m = 1; $m <= 12; $m++) { 
                                $monthVal = str_pad($m, 2, '0', STR_PAD_LEFT);
                                $monthName = date('F', mktime(0, 0, 0, $m, 1));
                                $isSelected = (isset($filters['month']) && ($filters['month'] == $m || $filters['month'] == $monthVal)) ? 'selected' : '';
                            ?>
                                <option value="<?= $m ?>" <?= $isSelected ?>><?= $monthName ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-2">
                        <select name="year" class="form-select shadow-none">
                            <option value="">All Years</option>
                            <?php 
                            for ($y = (int)date('Y'); $y >= 2020; $y--) { 
                                $isSelected = (isset($filters['year']) && $filters['year'] == $y) ? 'selected' : '';
                            ?>
                                <option value="<?= $y ?>" <?= $isSelected ?>><?= $y ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-2">
                        <select name="category_id" class="form-select shadow-none">
                            <option value="">All Categories</option>
                            <?php 
                            if (!empty($categories)) {
                                foreach ($categories as $cat) { 
                                    $isSelected = (isset($filters['category_id']) && $filters['category_id'] == $cat['id']) ? 'selected' : '';
                            ?>
                                <option value="<?= $cat['id'] ?>" <?= $isSelected ?>><?= esc($cat['name']) ?></option>
                            <?php 
                                } 
                            } 
                            ?>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Search title or paid by..." value="<?= $filters['search'] ?? '' ?>" style="height: 2.44rem;">
                    </div>

                    <div class="col-12 col-md-3 d-flex gap-2">
                        <button type="submit" class="btn hr-btnbg w-100">
                            <i class="mdi mdi-filter-outline me-1"></i> Filter
                        </button>
                        <?php if (!empty($filters['month']) || !empty($filters['year']) || !empty($filters['category_id']) || !empty($filters['search'])): ?>
                            <a href="<?= base_url('expenses') ?>" class="btn btn-secondary text-nowrap" title="Reset Filters">
                                <i class="mdi mdi-refresh"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>

                <!-- Data Table & Chart Row -->
                <div class="row">
                    <div class="col-lg-8 mb-4 mb-lg-0">
                        <div class="table-responsive">
                            <table class="table table-striped w-100" id="expense-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Title</th>
                                        <th class="desktop-only-col">Category</th>
                                        <th class="desktop-only-col">Amount</th>
                                        <th class="desktop-only-col">Paid By</th>
                                        <th class="desktop-only-col">Date</th>
                                        <th class="desktop-only-col">Receipt</th>
                                        <th class="desktop-only-col action-column" style="width: 110px;">Action</th>
                                        <th class="mobile-expand-col" style="width: 50px;">Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($expenses as $expense): ?>
                                        <?php 
                                            $formattedDate = date('d M Y', strtotime($expense['expense_date']));
                                            $paidByName = $expense['paid_by'] ? ($expense['paid_by_name'] ?? 'Employee') : 'Company';
                                            $hasAttachment = !empty($expense['attachment']);
                                        ?>
                                        <tr>
                                            <td class="capitalize-text">
                                                <div style="flex: 1;">
                                                    <span class="fw-semibold text-dark"><?= esc($expense['title']) ?></span>
                                                    <div class="expanded-details" id="expense-details-<?= $expense['id'] ?>" onclick="event.stopPropagation();">
                                                        <div class="detail-row">
                                                            <span class="detail-label">Category:</span>
                                                            <span class="detail-value"><?= esc($expense['category_name'] ?? 'General') ?></span>
                                                        </div>
                                                        <div class="detail-row">
                                                            <span class="detail-label">Amount:</span>
                                                            <span class="detail-value fw-bold text-primary">&#8377;<?= number_format($expense['amount'], 2) ?></span>
                                                        </div>
                                                        <div class="detail-row">
                                                            <span class="detail-label">Paid By:</span>
                                                            <span class="detail-value"><?= esc($paidByName) ?></span>
                                                        </div>
                                                        <div class="detail-row">
                                                            <span class="detail-label">Date:</span>
                                                            <span class="detail-value"><?= $formattedDate ?></span>
                                                        </div>
                                                        <?php if (!empty($expense['description'])): ?>
                                                            <div class="detail-row">
                                                                <span class="detail-label">Description:</span>
                                                                <span class="detail-value"><?= esc($expense['description']) ?></span>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="detail-actions">
                                                            <a href="<?= base_url('expenses/edit/'.$expense['id']) ?>" class="btn btn-sm btn-warning"><i class="mdi mdi-pencil"></i> Edit</a>
                                                            <?php if($user->role !== 'employee'): ?>
                                                                <a href="javascript:void(0)" onclick="confirmExpenseAction('<?= base_url('expenses/delete/'.$expense['id']) ?>')" class="btn btn-sm btn-danger"><i class="mdi mdi-delete"></i> Delete</a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="desktop-only-col">
                                                <span class="badge badge-outline-secondary" style="font-size: 11px; padding: 4px 8px; border-radius: 4px;"><?= esc($expense['category_name'] ?? 'General') ?></span>
                                            </td>
                                            <td class="desktop-only-col fw-bold" style="color: #2b3344;" data-order="<?= $expense['amount'] ?>">&#8377;<?= number_format($expense['amount'], 2) ?></td>
                                            <td class="desktop-only-col capitalize-text">
                                                <?php if (!$expense['paid_by']): ?>
                                                    <span class="badge" style="background:#e8f4fd;color:#0d6efd;font-size:11px;padding:4px 8px;">Company</span>
                                                <?php else: ?>
                                                    <?= esc($expense['paid_by_name'] ?? 'Employee') ?>
                                                <?php endif; ?>
                                            </td>
                                            <td class="desktop-only-col" data-order="<?= $expense['expense_date'] ?>"><?= $formattedDate ?></td>
                                            <td class="desktop-only-col text-center">
                                                <?php if ($hasAttachment): ?>
                                                    <a href="<?= base_url('upload/expenses/' . $expense['attachment']) ?>" target="_blank" class="text-primary fs-5" title="View Receipt">
                                                        <i class="mdi mdi-file-document-outline"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="desktop-only-col">
                                                <a href="<?= base_url('expenses/edit/'.$expense['id']) ?>" class="text-primary me-2" title="Edit">
                                                    <i class="mdi mdi-pencil fs-5"></i>
                                                </a>
                                                <?php if($user->role !== 'employee'): ?>
                                                    <a href="javascript:void(0)" onclick="confirmExpenseAction('<?= base_url('expenses/delete/'.$expense['id']) ?>')" class="text-danger" title="Delete">
                                                        <i class="mdi mdi-delete fs-5"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                            <td class="mobile-expand-col text-center">
                                                <button type="button" class="expand-toggle" data-target="expense-details-<?= $expense['id'] ?>" aria-label="Expand details"></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Category Breakdown Chart Card -->
                    <div class="col-lg-4">
                        <div class="card h-100" style="border: 1px solid #eef2f6; border-radius: 8px;">
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-3" style="font-size: 15px; color: #2b3344;">
                                    <i class="mdi mdi-chart-pie me-1 text-primary"></i> Category-wise Expenses
                                </h5>
                                <?php if (!empty($chartData)): ?>
                                    <div style="position: relative; height: 260px;">
                                        <canvas id="expenseChart"></canvas>
                                    </div>
                                <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center text-center p-4" style="height: 220px;">
                                        <h6 class="text-muted mb-0">No expense records found to plot chart</h6>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ChartJS & SheetJS Export Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        const table = $('#expense-table').DataTable({
            order: [[4, 'desc']], // Sort by Date descending
            columnDefs: [
                {
                    targets: [6, 7], // Action & Mobile Expand column
                    orderable: false,
                    searchable: false
                }
            ],
            language: {
                search: '',
                searchPlaceholder: 'Search table...'
            }
        });

        // Chart Initialization
        <?php if (!empty($chartData)): ?>
            const chartCanvas = document.getElementById('expenseChart');
            if (chartCanvas) {
                const ctx = chartCanvas.getContext('2d');
                const chartData = <?= json_encode($chartData) ?>;
                
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: chartData.map(item => item.name),
                        datasets: [{
                            data: chartData.map(item => item.total),
                            backgroundColor: [
                                '#E66136', '#ff7b4a', '#2c3e50', '#17a2b8', 
                                '#28a745', '#ffc107', '#6f42c1', '#20c997'
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    padding: 12,
                                    font: { size: 11, family: 'Poppins' }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return ' ' + context.label + ': ₹' + Number(context.raw).toLocaleString('en-IN', {minimumFractionDigits: 2});
                                    }
                                }
                            }
                        }
                    }
                });
            }
        <?php endif; ?>

        // Export Excel
        $('#btnExportExpenses').on('click', function() {
            const wb = XLSX.utils.book_new();
            const tableEl = document.getElementById('expense-table');
            
            // Extract clean rows
            const rows = [];
            rows.push(['Title', 'Category', 'Amount (₹)', 'Paid By', 'Date']);
            
            <?php foreach($expenses as $exp): ?>
                rows.push([
                    <?= json_encode($exp['title']) ?>,
                    <?= json_encode($exp['category_name'] ?? 'General') ?>,
                    <?= (float)$exp['amount'] ?>,
                    <?= json_encode($exp['paid_by'] ? ($exp['paid_by_name'] ?? 'Employee') : 'Company') ?>,
                    <?= json_encode(date('d M Y', strtotime($exp['expense_date']))) ?>
                ]);
            <?php endforeach; ?>

            const ws = XLSX.utils.aoa_to_sheet(rows);
            XLSX.utils.book_append_sheet(wb, ws, "Expenses");
            XLSX.writeFile(wb, "Expenses_Report_" + new Date().toISOString().split('T')[0] + ".xlsx");
        });
    });

    function confirmExpenseAction(url) {
        Swal.fire({
            icon: 'warning',
            title: 'Delete Expense?',
            text: 'This expense entry will be deleted permanently.',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary ms-2'
            },
            buttonsStyling: false
        }).then(result => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }
</script>
<?= $this->endSection() ?>
