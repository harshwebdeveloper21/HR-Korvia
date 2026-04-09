<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="card-title">Employee Leave Management</h4>
                        <p class="card-description">Add and update leave balances for employees.</p>
                    </div>
                </div>

                <!-- Status Message -->
                <div id="alertContainer"></div>

                <form class="form-sample" id="leaveBalanceForm">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="employee_id">Employee Name</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="mdi mdi-account fs-5"></i></span>
                                    </div>
                                    <select class="form-select" name="employee_id" id="employee_id" required>
                                        <option value="" disabled selected>Select Employee</option>
                                        <?php foreach ($employees as $emp): ?>
                                            <option value="<?= $emp['id'] ?>"><?= esc($emp['username']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="paid_leave">Allocated Paid Leave</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="mdi mdi-cash-multiple fs-5"></i></span>
                                    </div>
                                    <input type="number" step="0.5" min="0" class="form-control" name="paid_leave"
                                        id="paid_leave" placeholder="Enter Paid Leave" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="casual_leave">Allocated Casual Leave</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="mdi mdi-walk fs-5"></i></span>
                                    </div>
                                    <input type="number" step="0.5" min="0" class="form-control" name="casual_leave"
                                        id="casual_leave" placeholder="Enter Casual Leave" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group pt-4">
                                <button type="submit" class="btn hr-btnbg w-100 mt-2" id="submitBtn">Update</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive mt-4">
                    <table class="table table-striped" id="leaveBalanceTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Employee Name</th>
                                <th>Paid Leave </th>
                                <th>Casual Leave </th>
                                <th>Total Leave</th>
                                <th>Last Updated</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="leaveBalanceTableBody">
                            <!-- Data loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leave Details Modal -->
<div class="modal fade" id="viewLeaveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0">
            <div class="modal-header hr-btnbg" style="color: white; border-top-left-radius: 5px; border-top-right-radius: 5px;">
                <h5 class="modal-title m-0 fw-bold">
                    <i class="mdi mdi-calendar-text me-1"></i> <span id="modalEmpName"></span> - <span id="modalMonth"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-1 p-3 rounded" style="background-color: #f8f9fa; border-left: 4px solid #E66136;">
                    <h6 class="mb-0 fw-bold text-dark">Total Approved Leaves:</h6>
                    <span class="badge rounded-pill px-3 py-2" style="background-color: #E66136; font-size: 14px;"><span id="modalTotalLeaves"></span> Days</span>
                </div>
                <small class="text-muted d-block mb-3 fst-italic">* Note: Manual overrides in the Payroll module are not shown here.</small>
                
                <div class="table-responsive border rounded" style="max-height: 300px; overflow-y: auto; overflow-x: auto;">
                    <table class="table table-striped table-hover mb-0 text-center">
                        <thead style="background-color: #000; color: #fff; position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th class="text-white py-3">Leave Dates</th>
                                <th class="text-white py-3">Leave Type</th>
                                <th class="text-white py-3">Count</th>
                            </tr>
                        </thead>
                        <tbody id="modalLeaveDetailsBody">
                            <tr>
                                <td colspan="3" class="text-center py-4">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        const token = localStorage.getItem('token');
        loadLeaveBalances();

        // Handle Change Employee to pre-fill data
        $('#employee_id').on('change', function () {
            const employeeId = $(this).val();
            if (employeeId) {
                fetch(`/api/employee-leaves/${employeeId}`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            $('#paid_leave').val(res.data.paid_leave);
                            $('#casual_leave').val(res.data.casual_leave);
                            $('#submitBtn').text('Update Balance');
                        } else {
                            $('#paid_leave').val('');
                            $('#casual_leave').val('');
                            $('#submitBtn').text('Add Balance');
                        }
                    })
                    .catch(() => {
                        $('#paid_leave').val('');
                        $('#casual_leave').val('');
                        $('#submitBtn').text('Add Balance');
                    });
            }
        });

        // Handle Form Submission
        $('#leaveBalanceForm').on('submit', function (e) {
            e.preventDefault();
            const formData = {
                employee_id: $('#employee_id').val(),
                paid_leave: $('#paid_leave').val(),
                casual_leave: $('#casual_leave').val(),
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            };

            fetch('/api/employee-leaves/store', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        Swal.fire('Success', res.message, 'success');
                        loadLeaveBalances();
                    } else {
                        Swal.fire('Error', res.message || 'Failed to update balance', 'error');
                    }
                })
                .catch(err => Swal.fire('Error', 'An error occurred', 'error'));
        });

        function loadLeaveBalances() {
            $('#leaveBalanceTableBody').html('<tr><td colspan="7" class="text-center">Loading...</td></tr>');

            fetch(`/api/employee-leaves`, {
                headers: { 'Authorization': `Bearer ${token}` }
            })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        let html = '';
                        res.data.forEach((item, index) => {
                            html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td><span class="fw-bold">${item.username}</span></td>
                                <td>
                                    <span class="badge border border-primary text-primary px-2 py-1" title="Allocated">${item.paid_leave}</span>
                                    / 
                                    <span class="badge border border-danger text-danger px-2 py-1" title="Used">${item.paid_used}</span>
                                </td>
                                <td>
                                    <span class="badge border border-primary text-primary px-2 py-1" title="Allocated">${item.casual_leave}</span>
                                    / 
                                    <span class="badge border border-danger text-danger px-2 py-1" title="Used">${item.casual_used}</span>
                                </td>
                                <td>
                                    <span class="badge border border-primary text-primary px-2 py-1" title="Allocated">${item.total_leaves}</span>
                                    / 
                                    <span class="badge border border-danger text-danger px-2 py-1" title="Used">${item.total_used}</span>
                                </td>
                                <td>${new Date(item.updated_at).toLocaleDateString()}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info btn-view-leave me-1" data-id="${item.employee_id}">
                                        <i class="mdi mdi-eye"></i> View
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary btn-edit" data-id="${item.employee_id}" 
                                            data-paid="${item.paid_leave}" data-casual="${item.casual_leave}">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        `;
                        });
                        $('#leaveBalanceTableBody').html(html || '<tr><td colspan="7" class="text-center">No records found</td></tr>');

                        // Re-bind edit buttons
                        $('.btn-edit').on('click', function () {
                            const id = $(this).data('id');
                            const paid = $(this).data('paid');
                            const casual = $(this).data('casual');
                            $('#employee_id').val(id).trigger('change');
                            $('#paid_leave').val(paid);
                            $('#casual_leave').val(casual);
                            $('#submitBtn').text('Update Balance');
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        });

                        // Bind View buttons
                        $('.btn-view-leave').on('click', function () {
                            const employeeId = $(this).data('id');

                            // Show modal with loading state
                            $('#modalLeaveDetailsBody').html('<tr><td colspan="3" class="text-center">Loading...</td></tr>');
                            var modal = new bootstrap.Modal(document.getElementById('viewLeaveModal'));
                            modal.show();

                            fetch(`/api/employee-leaves/details?employee_id=${employeeId}`, {
                                headers: { 'Authorization': `Bearer ${token}` }
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === 'success') {
                                        $('#modalEmpName').text(data.data.employee_name);
                                        $('#modalMonth').text(data.data.month);
                                        $('#modalTotalLeaves').text(data.data.total_count);

                                        let tbody = '';
                                        if (data.data.records.length > 0) {
                                            data.data.records.forEach(rec => {
                                                const dateDisplay = rec.start_date === rec.end_date
                                                    ? rec.start_date
                                                    : `${rec.start_date} to ${rec.end_date}`;
                                                tbody += `
                                                <tr>
                                                    <td>${dateDisplay}</td>
                                                    <td><span class="badge bg-light text-dark border">${rec.leave_type || 'Unknown'}</span></td>
                                                    <td class="fw-bold">${rec.no_of_day}</td>
                                                </tr>
                                            `;
                                            });
                                        } else {
                                            tbody = '<tr><td colspan="3" class="text-center text-muted">No approved leaves found for this month in the system.</td></tr>';
                                        }
                                        $('#modalLeaveDetailsBody').html(tbody);
                                    }
                                })
                                .catch(error => {
                                    $('#modalLeaveDetailsBody').html('<tr><td colspan="3" class="text-center text-danger">Error loading data</td></tr>');
                                });
                        });
                    }
                })
                .catch(err => {
                    $('#leaveBalanceTableBody').html('<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>');
                });
        }
    });
</script>

<?= $this->endSection(); ?>