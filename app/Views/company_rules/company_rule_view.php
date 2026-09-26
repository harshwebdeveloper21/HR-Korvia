<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
    /* Prevent any horizontal overflow on the card and table wrapper */
    .table-responsive {
        overflow-x: hidden !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    #rules-Table {
        width: 100% !important;
        max-width: 100% !important;
        table-layout: fixed !important;
        border-collapse: collapse !important;
        margin-bottom: 0 !important;
    }

    #rules-Table th,
    #rules-Table td {
        white-space: normal !important;
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
        vertical-align: middle !important;
    }

    #rules-Table thead th {
        font-size: 12px !important;
        font-weight: 700 !important;
        padding: 10px 6px !important;
        line-height: 1.3 !important;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        vertical-align: middle !important;
    }

    /* Prevent DataTables sort arrows from crowding narrow header cells */
    #rules-Table thead th.sorting,
    #rules-Table thead th.sorting_asc,
    #rules-Table thead th.sorting_desc {
        padding-right: 18px !important;
    }

    #rules-Table tbody td {
        font-size: 13px !important;
        padding: 10px 6px !important;
        line-height: 1.35 !important;
        color: #2b3344;
    }

    /* Modern compact badges for Saturday Off Pattern */
    .badge-sat-rule {
        display: inline-block;
        background-color: #f1f5f9;
        color: #1e293b;
        border: 1px solid #cbd5e1;
        font-size: 11.5px;
        font-weight: 500;
        padding: 2px 7px;
        border-radius: 4px;
        margin: 2px 3px 2px 0;
        white-space: nowrap;
    }

    /* Prevent negative margin row overflow from DataTables controls */
    #rules-Table_wrapper .row {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    /* Mobile Responsive Optimizations (<768px) */
    @media (max-width: 767px) {
        #rules-Table {
            table-layout: auto !important;
        }

        #rules-Table th:first-child,
        #rules-Table td:first-child {
            width: calc(100% - 55px) !important;
            max-width: calc(100% - 55px) !important;
        }

        #rules-Table th.mobile-expand-col,
        #rules-Table td.mobile-expand-col {
            width: 55px !important;
            max-width: 55px !important;
            text-align: center !important;
            vertical-align: middle !important;
        }

        #rules-Table .expanded-details {
            display: none;
            position: static !important;
            width: 100% !important;
            left: auto !important;
            margin-top: 10px !important;
            padding: 10px 12px !important;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 3px solid #E66136;
            box-sizing: border-box !important;
        }

        #rules-Table .expanded-details.show {
            display: block !important;
            position: static !important;
            width: 100% !important;
            left: auto !important;
        }

        /* Clean DataTables length & search on mobile */
        #rules-Table_wrapper .dataTables_length,
        #rules-Table_wrapper .dataTables_filter {
            float: none !important;
            width: 100% !important;
            margin-bottom: 8px !important;
            display: flex !important;
            align-items: center !important;
            font-size: 13px !important;
        }

        #rules-Table_wrapper .dataTables_filter label {
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
            margin-bottom: 0 !important;
        }

        #rules-Table_wrapper .dataTables_filter input {
            flex: 1 1 auto !important;
            width: 100% !important;
            max-width: 100% !important;
            margin-left: 8px !important;
            height: 32px !important;
            font-size: 13px !important;
        }

        #rules-Table_wrapper .dataTables_paginate {
            float: none !important;
            text-align: center !important;
            margin-top: 10px !important;
        }
    }
</style>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Manage Rules</h4>
                    <a href="/creates-rules" class="btn hr-btnbg text-white">
                        <i class="mdi mdi-plus"></i> Add Company Rules
                    </a>
                
                </div>
                <div class="table-responsive">
                    <table class="table table-striped w-100" id="rules-Table">
                        <thead>
                            <tr>
                                <th style="width: 15%;">Branch</th>
                                <th style="width: 14%;">Working Hours</th>
                                <th class="desktop-only-col" style="width: 20%;">Saturday Off Pattern</th>
                                <th class="desktop-only-col text-center" style="width: 10%;">Yearly Holidays</th>
                                <th class="desktop-only-col text-center" style="width: 8%;">Tax</th>
                                <th class="desktop-only-col text-center" style="width: 12%;">Taxable Salary</th>
                                <th class="desktop-only-col text-center" style="width: 10%;">Lunch Break</th>
                                <th class="desktop-only-col text-center" style="width: 11%;">Action</th>
                                <th class="mobile-expand-col text-center" style="width: 50px;">Details</th>
                            </tr>
                        </thead>
                        <tbody id="rules-Table-Body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const token = localStorage.getItem('token');

        fetch('/api/company-rules/rules', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(responseData => {
                if (responseData.status === 'success') {
                    const rules = responseData.rules || responseData.data; // supports either key
                    let tableRows = '';

                    const ordinalMap = {
                        '1': '1st Saturday',
                        '2': '2nd Saturday',
                        '3': '3rd Saturday',
                        '4': '4th Saturday',
                        '5': '5th Saturday',
                    };

                    const satShortMap = {
                        '1': '1st Sat',
                        '2': '2nd Sat',
                        '3': '3rd Sat',
                        '4': '4th Sat',
                        '5': '5th Sat',
                    };

                    if (rules.length === 0) {
                        $('#header-action-btn').html('<a href="/creates-rules" class="btn hr-btnbg text-white"><i class="mdi mdi-plus"></i> Add Company Rules</a>');
                        tableRows = `
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="mdi mdi-alert-circle-outline text-muted" style="font-size: 48px;"></i>
                                    </div>
                                    <h5 class="text-muted mb-3">No company rules found.</h5>
                                </td>
                            </tr>
                        `;
                    } else {
                        $('#header-action-btn').html('');
                        rules.forEach((rule, index) => {
                        // Badges for desktop table
                        const satBadges = rule.saturday_off_pattern
                            ? rule.saturday_off_pattern
                                .split(',')
                                .map(num => num.trim())
                                .filter(num => satShortMap[num])
                                .map(num => `<span class="badge-sat-rule">${satShortMap[num]}</span>`)
                                .join(' ') || '<span class="text-muted">None</span>'
                            : '<span class="text-muted">None</span>';

                        // Clean text for mobile detail panel
                        const saturdayOffList = rule.saturday_off_pattern
                            ? rule.saturday_off_pattern
                                .split(',')
                                .map(num => ordinalMap[num.trim()] || num.trim())
                                .filter(Boolean)
                                .join(', ') || 'None'
                            : 'None';

                        tableRows += `
                    <tr data-id="${rule.id}">
                        <td><span class="fw-bold text-danger">${rule.branch_name || 'Global'}</span></td>
                        <td>
                            <div style="flex: 1;">
                                <span class="fw-bold">${rule.working_hours_per_day} hours</span>
                                <div class="expanded-details" id="rule-details-${rule.id}">
                                    <div class="detail-row">
                                        <span class="detail-label">Saturday Off:</span>
                                        <span class="detail-value">${saturdayOffList}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Yearly Holidays:</span>
                                        <span class="detail-value">${rule.yearly_holidays ?? 0}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Tax:</span>
                                        <span class="detail-value">${rule.tax ?? 0}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Taxable Salary:</span>
                                        <span class="detail-value">${rule.salary_above_tax ?? 0}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Lunch Break:</span>
                                        <span class="detail-value">${rule.lunch_break ?? '-'}</span>
                                    </div>
                                    <div class="detail-actions">
                                        <a href="/creates-rules?branch_id=${rule.branch_id || ''}" class="btn btn-sm btn-warning"><i class="mdi mdi-pencil"></i> Edit Rules</a>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="desktop-only-col">${satBadges}</td>
                        <td class="desktop-only-col text-center">${rule.yearly_holidays ?? 0}</td>
                        <td class="desktop-only-col text-center">${rule.tax ?? 0}</td>
                        <td class="desktop-only-col text-center">${rule.salary_above_tax ?? 0}</td>
                        <td class="desktop-only-col text-center">${rule.lunch_break ?? '-'}</td>
                        <td class="desktop-only-col text-center">
                            <a href="/creates-rules?branch_id=${rule.branch_id || ''}" class="text-warning fs-5" title="Edit Rules">
                                <i class="mdi mdi-pencil"></i>
                            </a>
                        </td>
                        <td class="mobile-expand-col text-center">
                            <button type="button" class="expand-toggle" data-target="rule-details-${rule.id}" aria-label="Expand details"></button>
                        </td>
                    </tr>
                `;
                    });
                    }

                    $('#rules-Table-Body').html(tableRows);

                    if ($.fn.DataTable.isDataTable('#rules-Table')) {
                        $('#rules-Table').DataTable().clear().destroy();
                    }

                    const dt = $('#rules-Table').DataTable({
                        autoWidth: false,
                        responsive: false,
                        columnDefs: [
                            {
                                targets: [6, 7],
                                orderable: false,
                                searchable: false
                            }
                        ],
                        language: {
                            search: "",
                            searchPlaceholder: "Search"
                        }
                    });

                    dt.on('draw', function() {
                        if (typeof applyMobileTableVisibility === 'function') {
                            applyMobileTableVisibility();
                        }
                    });

                    if (typeof applyMobileTableVisibility === 'function') {
                        applyMobileTableVisibility();
                    }

                } else {
                        $('#header-action-btn').html('');
                    console.error('Failed to fetch rules:', responseData.message);
                }
            })
            .catch(error => {
                console.error('Error fetching rules:', error);
            });
    });

    // Deletion
    function deleteRule(event) {
        event.preventDefault();
        const ruleId = event.target.closest('a').getAttribute('data-id');

        Swal.fire({
            title: 'Are you sure?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'hr-btnbg',
                cancelButton: 'hr-btnbg',
            }
        }).then(result => {
            if (result.isConfirmed) {
                fetch(`/api/rules/${ruleId}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('token')}`,
                            'Content-Type': 'application/json',
                        }
                    })

                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            document.querySelector(`tr[data-id="${ruleId}"]`)?.remove();
                            Swal.fire('Deleted!', 'The rule has been deleted.', 'success');
                        } else {
                        $('#header-action-btn').html('');
                            Swal.fire('Error!', data.message || 'Failed to delete.', 'error');
                        }
                    })
                    .catch(err => {
                        Swal.fire('Error!', err.message || 'Something went wrong.', 'error');
                    });
            }
        });
    }
</script>

<?= $this->endSection() ?>
