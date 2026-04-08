<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Employee Leave Management</h4>
                <p class="card-description">Add and update leave balances for employees.</p>

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
                                <label for="paid_leave">Paid Leave</label>
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
                                <label for="casual_leave">Casual Leave</label>
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
                                <th>Paid Leave</th>
                                <th>Casual Leave</th>
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

            fetch('/api/employee-leaves', {
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
                                <td>${item.username}</td>
                                <td>
                                    <span class="badge border border-primary text-primary">${item.paid_leave}</span>
                                </td>
                                <td>
                                    <span class="badge border border-primary text-primary">${item.casual_leave}</span>
                                </td>
                                <td>
                                    <span class="badge border border-primary text-primary">${item.total_leaves}</span>
                                </td>
                                <td>${new Date(item.updated_at).toLocaleDateString()}</td>
                                <td>
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
                    }
                })
                .catch(err => {
                    $('#leaveBalanceTableBody').html('<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>');
                });
        }
    });
</script>

<?= $this->endSection(); ?>