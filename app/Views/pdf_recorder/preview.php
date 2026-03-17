<?= $this->extend("layout") ?>
<?= $this->section("content") ?>

<style>
    /* Summary Cards - Using rgb(230, 97, 54) orange color */
    .summary-card {
        border-left: 4px solid rgb(230, 97, 54);
        background: #fff;
        padding: 12px 15px;
        border-radius: 6px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        margin-bottom: 0;
        height: 100%;
        transition: all 0.3s ease;
    }
    .summary-card:hover {
        box-shadow: 0 2px 6px rgba(230, 97, 54, 0.15);
    }
    .summary-card h6 {
        color: #6c757d;
        font-size: 11px;
        margin-bottom: 6px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .summary-card h4 {
        color: #1E283D;
        font-weight: 600;
        font-size: 16px;
        margin: 0;
        line-height: 1.2;
    }
    #summaryCards .col-md-3,
    #summaryCards .col-md-4 {
        margin-bottom: 12px;
    }
    
    /* Table Responsive */
    .table-responsive {
        max-height: calc(100vh - 550px);
        min-height: 300px;
        overflow-y: auto;
        overflow-x: hidden;
        position: relative;
    }
    
    /* Filter Section */
    .filter-section {
        margin-bottom: 15px !important;
    }
    .filter-section h6 {
        font-size: 12px;
        margin-bottom: 8px;
        font-weight: 600;
        color: #1E283D;
    }
    
    /* Form Controls - Fixed Heights and Widths */
    .filter-section .form-control,
    .filter-section .form-select,
    .filter-section .form-control-sm,
    .filter-section .form-select-sm {
        height: 1.95rem !important;
        font-size: 0.812rem;
        padding: 0.4375rem 0.75rem;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        width: 100%;
    }
    
    /* Buttons - Match Input Field Heights, Auto Width */
    .filter-section .btn,
    .filter-section .btn-sm {
        height: 1.95rem !important;
        width: auto !important;
        min-width: fit-content;
        padding: 0.4375rem 0.81rem;
        font-size: 0.812rem;
        line-height: 1.2;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Transaction Row */
    .transaction-row {
        cursor: pointer;
    }
    .transaction-row:hover {
        background-color: #f8f9fa;
    }
    
    /* Amount Colors - Using website success/danger colors */
    .credit-amount {
        color: #34B1AA;
        font-weight: 600;
    }
    .debit-amount {
        color: #F95F53;
        font-weight: 600;
    }
    
    /* Transactions Table */
    #transactionsTable {
        table-layout: fixed;
        width: 100%;
        word-wrap: break-word;
        font-size: 12px;
        margin-bottom: 0;
    }
    #transactionsTable thead {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #1E283D;
    }
    #transactionsTable thead th {
        background-color: #1E283D !important;
        color: #ffffff !important;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        font-size: 10px;
        position: sticky;
        top: 0;
        z-index: 11;
        padding: 8px 8px;
        text-align: left;
        white-space: nowrap;
    }
    #transactionsTable thead th.text-end {
        text-align: right;
    }
    #transactionsTable th,
    #transactionsTable td {
        word-wrap: break-word;
        word-break: break-word;
        white-space: normal;
        vertical-align: middle;
        padding: 6px 8px;
        font-size: 12px;
        line-height: 1.4;
    }
    #transactionsTable tbody td {
        background-color: #ffffff;
        color: #1E283D;
    }
    #transactionsTable th:nth-child(1),
    #transactionsTable td:nth-child(1) {
        width: 10%;
    }
    #transactionsTable th:nth-child(2),
    #transactionsTable td:nth-child(2) {
        width: 10%;
    }
    #transactionsTable th:nth-child(3),
    #transactionsTable td:nth-child(3) {
        width: 35%;
    }
    #transactionsTable th:nth-child(4),
    #transactionsTable td:nth-child(4) {
        width: 8%;
    }
    #transactionsTable th:nth-child(5),
    #transactionsTable td:nth-child(5) {
        width: 12%;
    }
    #transactionsTable th:nth-child(6),
    #transactionsTable td:nth-child(6) {
        width: 12%;
    }
    #transactionsTable th:nth-child(7),
    #transactionsTable td:nth-child(7) {
        width: 13%;
    }
    
    /* Highlighted Transaction Row - Using rgb(230, 97, 54) orange color */
    .transaction-row.highlighted {
        background-color: #fff3cd !important;
        border-left: 5px solid rgb(230, 97, 54) !important;
        box-shadow: 0 2px 6px rgba(230, 97, 54, 0.4) !important;
    }
    .transaction-row.highlighted:hover {
        background-color: #ffe69c !important;
    }
    .transaction-row.highlighted td {
        background-color: #fff3cd !important;
    }
    .transaction-row.highlighted:hover td {
        background-color: #ffe69c !important;
    }
    
    /* Search Highlight - Using rgb(230, 97, 54) orange color */
    .search-highlight {
        background-color: rgb(230, 97, 54) !important;
        color: #ffffff !important;
        padding: 2px 4px;
        border-radius: 3px;
        font-weight: 600;
    }
    
    /* Card Title - Using website dark color */
    .card-title {
        color: #1E283D;
        font-weight: 600;
    }
    
    /* Text Muted - Using website gray */
    .text-muted {
        color: #6c757d !important;
    }
    
    /* Buttons in Transaction Section */
    #exportBtn {
        height: 1.95rem;
        padding: 0.4375rem 0.81rem;
        font-size: 0.812rem;
        display: flex;
        align-items: center;
    }
    
    /* Search Input Group */
    #searchInput {
        height: 1.95rem;
        font-size: 0.812rem;
    }
    
    .input-group-text {
        height: 1.95rem;
        padding: 0.4375rem 0.75rem;
    }
    
    #clearSearch {
        height: 1.95rem !important;
        padding: 0.4375rem 0.81rem;
        font-size: 0.812rem;
    }
    
    /* Badge */
    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }
    
    /* Upload New PDF Button */
    .btn-secondary {
        background-color: #05C3FB;
        border-color: #05C3FB;
        color: #ffffff;
        height: 1.95rem;
        padding: 0.4375rem 0.81rem;
        font-size: 0.812rem;
        display: flex;
        align-items: center;
    }
    .btn-secondary:hover {
        background-color: #04b0e0;
        border-color: #04b0e0;
        color: #ffffff;
    }
    
    /* Apply Filter Button - rgb(230, 97, 54) Orange */
    .btn-primary {
        background-color: rgb(230, 97, 54);
        border-color: rgb(230, 97, 54);
        color: #ffffff;
    }
    .btn-primary:hover {
        background-color: rgb(200, 80, 45);
        border-color: rgb(200, 80, 45);
        color: #ffffff;
    }
    
    /* Apply Date Range Button - rgb(230, 97, 54) Orange */
    .btn-success {
        background-color: rgb(230, 97, 54);
        border-color: rgb(230, 97, 54);
        color: #ffffff;
    }
    .btn-success:hover {
        background-color: rgb(200, 80, 45);
        border-color: rgb(200, 80, 45);
        color: #ffffff;
    }
    
    /* Export Button - rgb(230, 97, 54) Orange */
    #exportBtn.btn-success {
        background-color: rgb(230, 97, 54);
        border-color: rgb(230, 97, 54);
        color: #ffffff;
    }
    #exportBtn.btn-success:hover {
        background-color: rgb(200, 80, 45);
        border-color: rgb(200, 80, 45);
        color: #ffffff;
    }
    
    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        /* Filter Section - Stack on Mobile */
        .filter-section .col-md-3,
        .filter-section .col-md-4 {
            margin-bottom: 10px;
        }
        
        /* Buttons - Full Width on Mobile */
        .filter-section .btn,
        .filter-section .btn-sm {
            width: 100% !important;
            margin-top: 0;
        }
        
        /* Form Controls - Full Width on Mobile */
        .filter-section .form-control,
        .filter-section .form-select {
            width: 100% !important;
        }
        
        /* Summary Cards - Full Width on Mobile */
        #summaryCards .col-md-3,
        #summaryCards .col-md-4 {
            width: 100%;
            margin-bottom: 10px;
        }
        
        /* Summary Card Text Sizing */
        .summary-card h4 {
            font-size: 14px;
        }
        
        .summary-card h6 {
            font-size: 10px;
        }
        
        /* Table Responsive - Better Mobile Handling */
        .table-responsive {
            max-height: calc(100vh - 400px);
            min-height: 200px;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Remove overflow-x hidden on mobile */
        .table-responsive[style*="overflow-x: hidden"] {
            overflow-x: auto !important;
        }
        
        /* Table Font Sizes on Mobile */
        #transactionsTable {
            font-size: 11px;
        }
        
        #transactionsTable thead th {
            font-size: 9px;
            padding: 6px 4px;
        }
        
        #transactionsTable th,
        #transactionsTable td {
            padding: 4px 4px;
            font-size: 11px;
        }
        
        /* Transaction Section Header - Stack on Mobile */
        .card-body .d-flex.justify-content-between.align-items-center {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 10px;
        }
        
        .card-body .d-flex.justify-content-between.align-items-center > div {
            width: 100%;
            flex-direction: column;
            gap: 10px;
        }
        
        /* Export Button and Search - Full Width on Mobile */
        #exportBtn {
            width: 100% !important;
            margin-bottom: 0;
        }
        
        .input-group {
            width: 100% !important;
            margin-bottom: 0;
        }
        
        /* Badge in Transaction Section - Full Width on Mobile */
        .card-body .badge {
            width: 100%;
            text-align: center;
            display: block;
        }
        
        /* Badge - Adjust on Mobile */
        .badge {
            font-size: 0.7rem;
            padding: 0.3em 0.6em;
        }
        
        /* Upload New PDF Button - Full Width on Mobile */
        .d-flex.flex-wrap.justify-content-between.align-items-center {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .d-flex.flex-wrap.justify-content-between.align-items-center .btn-secondary {
            width: 100%;
            margin-top: 10px;
        }
        
        /* Card Title - Smaller on Mobile */
        .card-title {
            font-size: 16px;
        }
        
        /* Filter Section Headings */
        .filter-section h6 {
            font-size: 11px;
        }
        
        /* Form Labels - Smaller on Mobile */
        .form-label {
            font-size: 12px !important;
        }
    }
    
    /* Tablet Responsive Styles */
    @media (min-width: 769px) and (max-width: 991px) {
        /* Buttons - Slightly Larger on Tablet */
        .filter-section .btn,
        .filter-section .btn-sm {
            width: 100% !important;
        }
        
        /* Summary Cards - 2 per row on Tablet */
        #summaryCards .col-md-3,
        #summaryCards .col-md-4 {
            width: 50%;
        }
    }
</style>

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                    <h4 class="card-title" style="color: #1E283D; font-weight: 600;">PDF Statement - Bank Statement Preview</h4>
                    <a href="<?= base_url(
                        "pdf-recorder",
                    ) ?>" class="btn btn-secondary btn-sm">
                        <i class="mdi mdi-arrow-left me-2"></i>Upload New PDF
                    </a>
                </div>

                <div class="mb-3">
                    <p class="text-muted" style="color: #6c757d !important;"><strong style="color: #1E283D;">File:</strong> <?= esc(
                        $fileName,
                    ) ?></p>
                </div>

                <!-- Filter Section -->
                <div class="row mb-2 filter-section">
                    <div class="col-md-12 mb-2">
                        <h6 class="text-muted mb-2" style="font-size: 12px; font-weight: 600;">Filter by Month/Year</h6>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size: 13px; margin-bottom: 4px; color: #1E283D;">Filter by Year</label>
                        <select class="form-select form-select-sm" id="yearFilter">
                            <option value="">All Years</option>
                            <?php foreach ($availableYears as $year): ?>
                                <option value="<?= $year ?>"><?= $year ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size: 13px; margin-bottom: 4px; color: #1E283D;">From Month</label>
                        <select class="form-select form-select-sm" id="fromMonthFilter">
                            <option value="">Select Start Month</option>
                            <?php foreach (
                                $availableMonths
                                as $key => $label
                            ): ?>
                                <option value="<?= $key ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size: 13px; margin-bottom: 4px; color: #1E283D;">To Month</label>
                        <select class="form-select form-select-sm" id="toMonthFilter">
                            <option value="">Select End Month</option>
                            <?php foreach (
                                $availableMonths
                                as $key => $label
                            ): ?>
                                <option value="<?= $key ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" class="btn btn-primary btn-sm" id="applyFilter">
                            <i class="mdi mdi-filter me-2"></i>Apply Filter
                        </button>
                    </div>
                </div>

                <!-- Date Range Filter Section -->
                <div class="row mb-2 filter-section">
                    <div class="col-md-12 mb-2">
                        <h6 class="text-muted mb-2" style="font-size: 12px; font-weight: 600;">Or Filter by Date Range</h6>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" style="font-size: 13px; margin-bottom: 4px; color: #1E283D;">From Date</label>
                        <input type="date" class="form-control form-control-sm" id="fromDateFilter">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" style="font-size: 13px; margin-bottom: 4px; color: #1E283D;">To Date</label>
                        <input type="date" class="form-control form-control-sm" id="toDateFilter">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" class="btn btn-success btn-sm" id="applyDateFilter" style="width: auto; min-width: fit-content;">
                            <i class="mdi mdi-calendar-range me-2"></i>Apply Date Range
                        </button>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-2 g-2" id="summaryCards">
                    <div class="col-md-3" id="openingBalanceCard" style="display: none;">
                        <div class="summary-card">
                            <h6>Opening Balance</h6>
                            <h4 id="openingBalance">₹0.00</h4>
                        </div>
                    </div>
                    <div class="col-md-3" id="totalDebitCard">
                        <div class="summary-card">
                            <h6>Total Debit</h6>
                            <h4 class="debit-amount" id="totalDebit">₹0.00</h4>
                        </div>
                    </div>
                    <div class="col-md-3" id="totalCreditCard">
                        <div class="summary-card">
                            <h6>Total Credit</h6>
                            <h4 class="credit-amount" id="totalCredit">₹0.00</h4>
                        </div>
                    </div>
                    <div class="col-md-3" id="closingBalanceCard">
                        <div class="summary-card">
                            <h6>Closing Balance</h6>
                            <h4 id="closingBalance">₹0.00</h4>
                        </div>
                    </div>
                </div>

                <!-- Transactions Table -->
                <div class="card">
                    <div class="card-body" style="padding: 15px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0" style="font-size: 18px; color: #1E283D; font-weight: 600;">Transactions</h5>
                            <div class="d-flex align-items-center gap-3">
                                <button type="button" class="btn btn-success btn-sm" id="exportBtn">
                                    <i class="mdi mdi-file-excel me-2"></i>Export to Excel
                                </button>
                                <div class="input-group" style="width: 300px;">
                                    <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                                    <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Search transactions...">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" id="clearSearch" style="display: none; height: 1.95rem;">
                                        <i class="mdi mdi-close"></i>
                                    </button>
                                </div>
                                <span class="badge bg-primary" id="transactionCount" style="background-color: rgb(230, 97, 54) !important;">0 transactions</span>
                            </div>
                        </div>
                        <div class="table-responsive" style="overflow-x: hidden;">
                            <table class="table table-hover table-bordered" id="transactionsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width: 100px;">Date</th>
                                        <th style="min-width: 100px;">Value Date</th>
                                        <th style="min-width: 200px;">Particulars</th>
                                        <th style="min-width: 80px;">Cheque No</th>
                                        <th class="text-end" style="min-width: 100px;">Debit</th>
                                        <th class="text-end" style="min-width: 100px;">Credit</th>
                                        <th class="text-end" style="min-width: 120px;">Balance</th>
                                    </tr>
                                </thead>
                                <tbody id="transactionsBody">
                                    <tr>
                                        <td colspan="7" class="text-center">Loading transactions...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const yearFilter = document.getElementById('yearFilter');
    const fromMonthFilter = document.getElementById('fromMonthFilter');
    const toMonthFilter = document.getElementById('toMonthFilter');
    const fromDateFilter = document.getElementById('fromDateFilter');
    const toDateFilter = document.getElementById('toDateFilter');
    const applyFilterBtn = document.getElementById('applyFilter');
    const applyDateFilterBtn = document.getElementById('applyDateFilter');
    const transactionsBody = document.getElementById('transactionsBody');
    const transactionCount = document.getElementById('transactionCount');

    // Store all transactions data
    let allTransactions = <?= json_encode($parsedData["transactions"] ?? []) ?>;
    let openingBalance = <?= $parsedData["opening_balance"] ?? 0 ?>;
    let closingBalance = <?= $parsedData["closing_balance"] ?? 0 ?>;

    // Set initial card widths based on opening balance
    const openingBalanceCard = document.getElementById('openingBalanceCard');
    const totalDebitCard = document.getElementById('totalDebitCard');
    const totalCreditCard = document.getElementById('totalCreditCard');
    const closingBalanceCard = document.getElementById('closingBalanceCard');

    // Show opening balance card when opening balance is non-zero (positive or negative)
    if (openingBalance !== 0) {
        openingBalanceCard.className = 'col-md-3';
        totalDebitCard.className = 'col-md-3';
        totalCreditCard.className = 'col-md-3';
        closingBalanceCard.className = 'col-md-3';
    } else {
        totalDebitCard.className = 'col-md-4';
        totalCreditCard.className = 'col-md-4';
        closingBalanceCard.className = 'col-md-4';
    }

    // Initial load
    loadTransactions();

    // Apply filter
    applyFilterBtn.addEventListener('click', function() {
        // Clear date range filters when using month/year filter
        fromDateFilter.value = '';
        toDateFilter.value = '';
        loadTransactions();
    });

    // Apply date range filter
    applyDateFilterBtn.addEventListener('click', function() {
        // Clear month/year filters when using date range filter
        yearFilter.value = '';
        fromMonthFilter.value = '';
        toMonthFilter.value = '';
        loadTransactions();
    });

    // Auto-apply on filter change
    yearFilter.addEventListener('change', function() {
        // Clear date range filters
        fromDateFilter.value = '';
        toDateFilter.value = '';
        loadTransactions();
    });

    fromMonthFilter.addEventListener('change', function() {
        // Clear date range filters
        fromDateFilter.value = '';
        toDateFilter.value = '';
        // Auto-update "To Month" if it's before "From Month"
        if (toMonthFilter.value && fromMonthFilter.value) {
            if (toMonthFilter.value < fromMonthFilter.value) {
                toMonthFilter.value = fromMonthFilter.value;
            }
        }
        loadTransactions();
    });

    toMonthFilter.addEventListener('change', function() {
        // Clear date range filters
        fromDateFilter.value = '';
        toDateFilter.value = '';
        // Auto-update "From Month" if it's after "To Month"
        if (fromMonthFilter.value && toMonthFilter.value) {
            if (fromMonthFilter.value > toMonthFilter.value) {
                fromMonthFilter.value = toMonthFilter.value;
            }
        }
        loadTransactions();
    });

    // Date range filter change handlers
    fromDateFilter.addEventListener('change', function() {
        // Clear month/year filters
        yearFilter.value = '';
        fromMonthFilter.value = '';
        toMonthFilter.value = '';
        // Validate date range
        if (toDateFilter.value && fromDateFilter.value > toDateFilter.value) {
            toDateFilter.value = fromDateFilter.value;
        }
    });

    toDateFilter.addEventListener('change', function() {
        // Clear month/year filters
        yearFilter.value = '';
        fromMonthFilter.value = '';
        toMonthFilter.value = '';
        // Validate date range
        if (fromDateFilter.value && toDateFilter.value < fromDateFilter.value) {
            fromDateFilter.value = toDateFilter.value;
        }
    });

    function loadTransactions() {
        const year = yearFilter.value;
        const fromMonth = fromMonthFilter.value;
        const toMonth = toMonthFilter.value;
        const fromDate = fromDateFilter.value;
        const toDate = toDateFilter.value;

        // Build query string
        let queryParams = new URLSearchParams();
        if (year) queryParams.append('year', year);
        if (fromMonth) queryParams.append('from_month', fromMonth);
        if (toMonth) queryParams.append('to_month', toMonth);
        if (fromDate) queryParams.append('from_date', fromDate);
        if (toDate) queryParams.append('to_date', toDate);

        // Show loading
        transactionsBody.innerHTML = '<tr><td colspan="7" class="text-center">Loading...</td></tr>';

        fetch(`<?= base_url(
            "api/pdf-recorder/filtered-data",
        ) ?>?${queryParams.toString()}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Update opening balance from filtered data
                openingBalance = data.data.totals.opening_balance || 0;
                displayTransactions(data.data.transactions);
                updateSummary(data.data.totals);
                transactionCount.textContent = `${data.data.count} transaction(s)`;
            } else {
                transactionsBody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading transactions</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            transactionsBody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading transactions</td></tr>';
        });
    }

    function displayTransactions(transactions) {
        if (transactions.length === 0) {
            transactionsBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No transactions found for selected filter</td></tr>';
            return;
        }

        let html = '';

        // Add opening balance row when non-zero (positive or negative)
        if (transactions.length > 0 && openingBalance !== 0) {
            const firstTransaction = transactions[0];
            const firstDate = firstTransaction.transaction_date || firstTransaction.date;
            const date = new Date(firstDate);
            const formattedDate = date.toLocaleDateString('en-IN', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
            const openingDisplay = (openingBalance < 0 ? '-' : '') + '₹' + formatNumber(Math.abs(openingBalance));
            html += `
                <tr class="table-info">
                    <td style="word-wrap: break-word; white-space: normal;">${formattedDate}</td>
                    <td style="word-wrap: break-word; white-space: normal;">${formattedDate}</td>
                    <td style="word-wrap: break-word; white-space: normal; max-width: 400px;"><strong>Opening Balance</strong></td>
                    <td style="word-wrap: break-word; white-space: normal;"></td>
                    <td class="text-end" style="word-wrap: break-word; white-space: normal;"></td>
                    <td class="text-end" style="word-wrap: break-word; white-space: normal;"></td>
                    <td class="text-end" style="word-wrap: break-word; white-space: normal;"><strong>${openingDisplay}</strong></td>
                </tr>
            `;
        }

        transactions.forEach(transaction => {
            // Format transaction date
            const transDate = transaction.transaction_date || transaction.date;
            const date = new Date(transDate);
            const formattedDate = date.toLocaleDateString('en-IN', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });

            // Format value date
            const valueDate = transaction.value_date || transaction.date;
            const valueDateObj = new Date(valueDate);
            const formattedValueDate = valueDateObj.toLocaleDateString('en-IN', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });

            // Get debit, credit, and balance
            const debit = parseFloat(transaction.debit || 0);
            const credit = parseFloat(transaction.credit || 0);
            const balance = parseFloat(transaction.balance || 0);

            // If debit/credit not available, calculate from amount
            let finalDebit = debit;
            let finalCredit = credit;
            if (debit === 0 && credit === 0 && transaction.amount !== undefined) {
                const amount = parseFloat(transaction.amount || 0);
                if (amount < 0) {
                    finalDebit = Math.abs(amount);
                } else {
                    finalCredit = amount;
                }
            }

            const particulars = escapeHtml(transaction.particulars || transaction.description || 'N/A');
            const chequeNo = transaction.cheque_no || '';

            html += `
                <tr class="transaction-row">
                    <td style="word-wrap: break-word; white-space: normal;">${formattedDate}</td>
                    <td style="word-wrap: break-word; white-space: normal;">${formattedValueDate}</td>
                    <td style="word-wrap: break-word; white-space: normal; max-width: 400px;">${particulars}</td>
                    <td style="word-wrap: break-word; white-space: normal;">${chequeNo}</td>
                    <td class="text-end ${finalDebit > 0 ? 'debit-amount' : ''}" style="word-wrap: break-word; white-space: normal;">${finalDebit > 0 ? '₹' + formatNumber(finalDebit) : ''}</td>
                    <td class="text-end ${finalCredit > 0 ? 'credit-amount' : ''}" style="word-wrap: break-word; white-space: normal;">${finalCredit > 0 ? '₹' + formatNumber(finalCredit) : ''}</td>
                    <td class="text-end" style="word-wrap: break-word; white-space: normal;">${(balance < 0 ? '-' : '') + '₹' + formatNumber(Math.abs(balance))}</td>
                </tr>
            `;
        });

        transactionsBody.innerHTML = html;
    }

    function updateSummary(totals) {
        const openingBalance = totals.opening_balance || 0;
        const openingBalanceCard = document.getElementById('openingBalanceCard');
        const openingBalanceElement = document.getElementById('openingBalance');
        const totalDebitCard = document.getElementById('totalDebitCard');
        const totalCreditCard = document.getElementById('totalCreditCard');
        const closingBalanceCard = document.getElementById('closingBalanceCard');

        // Show/hide opening balance card when non-zero (positive or negative); display with correct sign
        if (openingBalance !== 0) {
            openingBalanceCard.style.display = 'block';
            openingBalanceElement.textContent = (openingBalance < 0 ? '-' : '') + '₹' + formatNumber(Math.abs(openingBalance));
            openingBalanceCard.className = 'col-md-3';
            totalDebitCard.className = 'col-md-3';
            totalCreditCard.className = 'col-md-3';
            closingBalanceCard.className = 'col-md-3';
        } else {
            openingBalanceCard.style.display = 'none';
            totalDebitCard.className = 'col-md-4';
            totalCreditCard.className = 'col-md-4';
            closingBalanceCard.className = 'col-md-4';
        }

        document.getElementById('totalDebit').textContent = `₹${formatNumber(totals.total_debit || 0)}`;
        document.getElementById('totalCredit').textContent = `₹${formatNumber(totals.total_credit || 0)}`;
        const closingVal = totals.closing_balance ?? 0;
        document.getElementById('closingBalance').textContent = (closingVal < 0 ? '-' : '') + '₹' + formatNumber(Math.abs(closingVal));
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(num);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearch');
    let currentSearchTerm = '';

    searchInput.addEventListener('input', function() {
        currentSearchTerm = this.value.trim();
        if (currentSearchTerm.length > 0) {
            clearSearchBtn.style.display = 'block';
            highlightSearchResults(currentSearchTerm);
        } else {
            clearSearchBtn.style.display = 'none';
            clearHighlights();
        }
    });

    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        currentSearchTerm = '';
        clearSearchBtn.style.display = 'none';
        clearHighlights();
    });

    // Store original displayTransactions function
    const originalDisplayTransactions = displayTransactions;

    // Override displayTransactions to apply search highlights after loading
    window.displayTransactions = function(transactions) {
        originalDisplayTransactions(transactions);
        // Re-apply search highlights if there's an active search
        if (currentSearchTerm && currentSearchTerm.length > 0) {
            setTimeout(() => {
                highlightSearchResults(currentSearchTerm);
            }, 100);
        }
    };

    function highlightSearchResults(searchTerm) {
        const rows = transactionsBody.querySelectorAll('.transaction-row');
        const searchLower = searchTerm.toLowerCase();
        let matchCount = 0;

        // First, clear any existing highlights
        clearHighlights();

        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            let found = false;

            cells.forEach(cell => {
                const cellText = cell.textContent || cell.innerText || '';
                const cellTextLower = cellText.toLowerCase();

                if (cellTextLower.includes(searchLower)) {
                    found = true;
                    // Highlight the matching text in the cell
                    const regex = new RegExp(`(${escapeRegex(searchTerm)})`, 'gi');
                    const highlightedHTML = cellText.replace(regex, '<span class="search-highlight">$1</span>');
                    cell.innerHTML = highlightedHTML;
                }
            });

            if (found) {
                row.classList.add('highlighted');
                matchCount++;
            }
        });

        // Update search count badge
        const countBadge = document.getElementById('transactionCount');
        if (matchCount > 0) {
            const totalCount = countBadge.textContent.match(/\d+/)?.[0] || '0';
            countBadge.textContent = `${totalCount} transaction(s) - ${matchCount} highlighted`;
            countBadge.style.backgroundColor = 'rgb(230, 97, 54)';
        } else if (currentSearchTerm.length > 0) {
            const totalCount = countBadge.textContent.match(/\d+/)?.[0] || '0';
            countBadge.textContent = `${totalCount} transaction(s) - No matches`;
            countBadge.style.backgroundColor = '#F95F53';
        }
    }

    function clearHighlights() {
        const rows = transactionsBody.querySelectorAll('.transaction-row');
        rows.forEach(row => {
            row.classList.remove('highlighted');

            // Remove highlight spans and restore original text
            const cells = row.querySelectorAll('td');
            cells.forEach(cell => {
                const highlights = cell.querySelectorAll('.search-highlight');
                highlights.forEach(highlight => {
                    const textNode = document.createTextNode(highlight.textContent);
                    highlight.parentNode.replaceChild(textNode, highlight);
                });
                // Normalize to merge text nodes
                cell.normalize();
            });
        });

        // Reset badge
        const countBadge = document.getElementById('transactionCount');
        if (countBadge) {
            const totalCount = countBadge.textContent.match(/\d+/)?.[0] || '0';
            countBadge.textContent = `${totalCount} transaction(s)`;
            countBadge.style.backgroundColor = 'rgb(230, 97, 54)';
        }
    }

    function escapeRegex(str) {
        return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    // Export to Excel functionality
    const exportBtn = document.getElementById('exportBtn');
    exportBtn.addEventListener('click', function() {
        const year = yearFilter.value;
        const fromMonth = fromMonthFilter.value;
        const toMonth = toMonthFilter.value;
        const fromDate = fromDateFilter.value;
        const toDate = toDateFilter.value;

        // Build query string with current filter values
        let queryParams = new URLSearchParams();
        if (year) queryParams.append('year', year);
        if (fromMonth) queryParams.append('from_month', fromMonth);
        if (toMonth) queryParams.append('to_month', toMonth);
        if (fromDate) queryParams.append('from_date', fromDate);
        if (toDate) queryParams.append('to_date', toDate);

        // Create download link
        const exportUrl = `<?= base_url(
            "api/pdf-recorder/export",
        ) ?>?${queryParams.toString()}`;

        // Create a temporary link and trigger download
        const link = document.createElement('a');
        link.href = exportUrl;
        link.download = 'bank_statement_export.xlsx';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
});
</script>

<?= $this->endSection() ?>
