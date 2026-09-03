<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
    @media (max-width: 767px) {
        .attendenceall {
            font-size: 9px !important;
            padding: 5.3px !important;
        }

        .iconfontsize {
            font-size: 11px !important;
        }

        .cart-sm-title {
            font-size: 12px !important;
            margin-bottom: 5px !important;
        }

        .dataTables_length {
            margin-left: .1rem !important;
            margin-bottom: .5rem !important;
            font-size: 12px !important;
            float: left !important;
        }

        .dataTables_filter {
            font-size: 12px !important;
            float: left !important;
            /* margin-left: -5rem !important;  */
        }

        .col-sm-12.col-md-6 {
            flex: 0 0 25%;
            max-width: 26%;
        }

        .dataTables_filter label:before {
            content: "" !important;
        }

        #templateTable_length label {
            display: flex;
            align-items: center;
        }

        /* Hide the text inside the label */
        #templateTable_length label::first-text,
        #templateTable_length label::before {
            display: none !important;
        }

        /* Or a simpler and reliable trick */
        #templateTable_length label {
            font-size: 0;
            /* hide text */
        }

        #templateTable_length label input {
            font-size: 10px;
            /* reset font size for input */
        }

        #templateTable_length label {
            font-size: 0;
            /* hide all text inside the label */
        }

        #templateTable_length label select {
            font-size: 14px;
            /* restore font size for the dropdown */
        }

        #templateTable_filter label {
            font-size: 0;
        }

        #templateTable_filter input {
            font-size: 14px;
            /* Keep input font size normal */
        }
        div.dataTables_wrapper div.dataTables_filter input {
            margin-left: 0.5em;
            display: inline-block;
            width: 212px !important;
            height: 29px !important
        }

        .custom-select {
            height: 26px !important;
            width: 57px !important;
        }

    }
</style>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Generated Experience Letters</h4>

                    <a href="<?= base_url(
                        "/add-emp-exprience",
                    ) ?>" class="btn hr-btnbg attendenceall">
                        <i class="mdi mdi-plus iconfontsize"></i> Generate Letter
                    </a>

                </div>
                <div class="table-responsive">
                    <table class="table table-striped" id="templateTable">
                        <thead class="table-dark">
                            <tr>
                                <th>No.</th>
                                <th>Employee Name</th>
                                <th>Template Name</th>
                                <th>From Date</th>
                                <th>To Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="templateTableBody">
                            <!-- Injected via JS -->
                        </tbody>
                    </table>
                </div>

                <div id="noTemplates" class="text-center text-muted mt-4" style="display: none;">
                    No generated experience letters found.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Generated Letter Modal -->
<div class="modal fade" id="editLetterModal" tabindex="-1" aria-labelledby="editLetterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="editLetterModalLabel">Edit Generated Experience Letter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editLetterForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit_letter_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_employee_id" class="form-label font-weight-bold">Employee Name</label>
                        <select class="form-select" name="employee_id" id="edit_employee_id" required>
                            <option value="">Select Employee</option>
                            <?php if (!empty($employee)): ?>
                                <?php foreach ($employee as $emp): ?>
                                    <option value="<?= $emp['id'] ?>"><?= esc($emp['username']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_from_date" class="form-label font-weight-bold">From Date</label>
                        <input type="date" class="form-control" name="from_date" id="edit_from_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_to_date" class="form-label font-weight-bold">To Date</label>
                        <input type="date" class="form-control" name="to_date" id="edit_to_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_template_id" class="form-label font-weight-bold">Template Name</label>
                        <select class="form-select" name="template_id" id="edit_template_id" required>
                            <option value="">Select Template</option>
                            <?php if (!empty($templates)): ?>
                                <?php foreach ($templates as $tmpl): ?>
                                    <option value="<?= $tmpl['id'] ?>"><?= esc($tmpl['title']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn hr-btnbg" id="editSubmitBtn">
                        <i class="mdi mdi-check me-1"></i> Update Letter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- AJAX Script -->
<script>
    function loadTemplates() {
        $.ajax({
            url: '<?= site_url("api/getdata") ?>',
            type: 'GET',
            success: function(response) {
                if (response.status === 'success' && response.data.length > 0) {
                    let html = '';
                    response.data.forEach((template, index) => {
                        const fullName = (template.firstname || '') + (template.lastname ? ' ' + template.lastname : '');
                        html += `
                              <tr data-id="${template.id}">
                                <td>${index + 1}</td>
                                <td>${fullName || template.firstname}</td>
                                <td>${template.title}</td>
                                <td>${template.from_date}</td>
                                <td>${template.to_date}</td>
                                <td style="display: flex; align-items: center; gap: 10px;">
                                    <a href="#" class="text-warning fs-5 edit-letter" 
                                       data-id="${template.id}" 
                                       data-employee-id="${template.employee_id || ''}" 
                                       data-template-id="${template.template_id || ''}" 
                                       data-from-date="${template.from_date || ''}" 
                                       data-to-date="${template.to_date || ''}" 
                                       title="Edit">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <a href="<?= site_url("api/generate-experience/") ?>${template.id}" class="text-primary fs-5" title="Download PDF">
                                        <i class="mdi mdi-download"></i>
                                    </a>
                                    <a href="#" class="text-danger fs-5 delete-template" data-id="${template.id}" title="Delete">
                                        <i class="mdi mdi-delete"></i>
                                    </a>
                                </td>
                            </tr>
                        `;
                    });
                    $('#templateTableBody').html(html);

                    // Initialize DataTable
                    if ($.fn.DataTable.isDataTable('#templateTable')) {
                        $('#templateTable').DataTable().destroy();
                    }
                    $('#templateTable').DataTable({
                        responsive: true,
                        pageLength: 10,
                        language: {
                            searchPlaceholder: "Search",
                            search: ""
                        }
                    });

                    $('#noTemplates').hide();
                } else {
                    $('#templateTableBody').html('');
                    $('#noTemplates').show();
                }
            },
            error: function() {
                $('#templateTableBody').html('');
                $('#noTemplates').text('Error loading templates.').show();
            }
        });
    }

    $(document).ready(function() {
        loadTemplates();

        // Handle Edit Letter Click
        $(document).on('click', '.edit-letter', function(e) {
            e.preventDefault();
            const letterId = $(this).data('id');
            const empId = $(this).data('employee-id');
            const tmplId = $(this).data('template-id');
            const fromDate = $(this).data('from-date');
            const toDate = $(this).data('to-date');

            $('#edit_letter_id').val(letterId);
            $('#edit_employee_id').val(empId);
            $('#edit_template_id').val(tmplId);
            $('#edit_from_date').val(fromDate);
            $('#edit_to_date').val(toDate);

            // Fetch fresh details from API
            $.ajax({
                url: '<?= site_url("api/exprience-data/get/") ?>' + letterId,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success' && res.data) {
                        $('#edit_employee_id').val(res.data.employee_id);
                        $('#edit_template_id').val(res.data.template_id);
                        $('#edit_from_date').val(res.data.from_date);
                        $('#edit_to_date').val(res.data.to_date);
                    }
                }
            });

            $('#editLetterModal').modal('show');
        });

        // Auto fetch joining date on employee change in edit modal
        $('#edit_employee_id').on('change', function() {
            const empId = $(this).val();
            if (empId) {
                $.ajax({
                    url: '<?= site_url("api/employee/joining-date") ?>/' + empId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        if (res && res.joining_date) {
                            $('#edit_from_date').val(res.joining_date);
                        }
                    }
                });
            }
        });

        // Submit Edit Form
        $('#editLetterForm').on('submit', function(e) {
            e.preventDefault();
            const letterId = $('#edit_letter_id').val();
            const formData = $(this).serialize();

            $('#editSubmitBtn').prop('disabled', true).text('Updating...');

            $.ajax({
                url: '<?= site_url("api/exprience-data/update/") ?>' + letterId,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(res) {
                    $('#editSubmitBtn').prop('disabled', false).html('<i class="mdi mdi-check me-1"></i> Update Letter');
                    if (res.status === 'success') {
                        $('#editLetterModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadTemplates();
                    } else {
                        Swal.fire('Error', res.message || 'Failed to update letter.', 'error');
                    }
                },
                error: function(xhr) {
                    $('#editSubmitBtn').prop('disabled', false).html('<i class="mdi mdi-check me-1"></i> Update Letter');
                    let msg = 'Failed to update letter.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', msg, 'error');
                }
            });
        });
    });

    $(document).on('click', '.delete-template', function(e) {
        e.preventDefault();
        const button = $(this); // store the button reference
        const templateId = button.data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'hr-btnbg',
                cancelButton: 'hr-btnbg',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url(
                        "api/exprience-data/delete/",
                    ) ?>' + templateId,
                    type: 'DELETE',
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Deleted!', res.message, 'success');

                            // Remove the row directly without reloading the table
                            const row = button.closest('tr');
                            $('#templateTable').DataTable().row(row).remove().draw();

                            // If no rows left, show the "noTemplates" message
                            if ($('#templateTableBody tr').length === 0) {
                                $('#noTemplates').show();
                            }

                        } else {
                            Swal.fire('Error!', res.message || 'Something went wrong.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Failed to delete template.', 'error');
                    }
                });
            }
        });
    });
</script>

<?= $this->endSection() ?>
