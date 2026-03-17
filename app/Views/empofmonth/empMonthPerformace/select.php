<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
    .generatemar-lg {
        margin-top: 5px !important;
    }

    @media (max-width: 767px) {
        .cart-sm-titles {
            font-size: 15px !important;
            /* margin-bottom: 5px !important; */
        }

        .interviewsmbtn {
            font-size: 10px !important;
            padding: 8px !important;
            margin-top: 10px !important;
        }
    }
    .cke_notifications_area{
    display: none !important;
}
</style>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title generatemar-lg">Generate Employee of The Month Performance</h4>
                <form class="form-sample" id="EOMForm">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label leave-sm-emp">Employee Name</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-domain fs-5"></i></span>
                                        </div>
                                        <select class="form-select" name="employee_id" id="employee_id">
                                            <option value="">Select Employee</option>
                                            <?php foreach (
                                                $topEmployees
                                                as $employee
                                            ): ?>
                                                <option value="<?= $employee[
                                                    "id"
                                                ] ?>"><?= $employee[
    "username"
] ?> (Rating: <?= $employee["rating"] ?>)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="error text-danger" id="employee_id-Error"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label leave-sm-emp">Template Name</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-calendar fs-5"></i></span>
                                        </div>
                                        <select class="form-select" name="template_id" id="template_id">
                                            <option value="">Select Template</option>
                                            <?php foreach (
                                                $letterTemplates
                                                as $template
                                            ): ?>
                                                <option value="<?= $template[
                                                    "id"
                                                ] ?>"><?= $template[
    "title"
] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="error text-danger" id="template_id-Error"></div>
                                </div>

                            </div>
                        </div>
                         <div class="col-md-2">
                             <div class="text-center" id="template-preview">
                                 <img id="template-image" src="" alt="Template Preview" class="img-fluid" style="max-height: 50px; display: none;">
                                </div>
                         </div>
                        <div class="col-md-10">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label leave-sm-emp">Month & Year</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-calendar-month fs-5"></i></span>
                                        </div>
                                        <input type="month" class="form-control" name="month_year" id="month_year" />
                                    </div>
                                    <div class="error text-danger" id="month_year-Error"></div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="form-group text-end">
                        <a href="/all-empof-month" class="btn hr-btnbg interviewsmbtn">
                            Back
                        </a>
                        <button type="button" class="btn hr-btnbg interviewsmbtn" id="generatePdfBtn">Generate PDF</button> <!-- Added this button -->
                    </div>
                </form>
                <div id="responseMessage"></div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $('#generatePdfBtn').on('click', function() {
        // Clear previous error messages and reset error class
        $('.error').text(''); // Clear all error messages

        const employee_id = $('#employee_id').val();
        const template_id = $('#template_id').val();
        const month_year = $('#month_year').val();

        let isValid = true; // Track if all fields are valid

        // Custom validation for Employee Name
        if (!employee_id) {
            $('#employee_id-Error').text('Employee is required.');
            isValid = false;
        }

        // Custom validation for Template Name
        if (!template_id) {
            $('#template_id-Error').text('Template is required.');
            isValid = false;
        }

        // Custom validation for Month & Year
        if (!month_year) {
            $('#month_year-Error').text('Month and year is required.');
            isValid = false;
        }
        // Ensure the selected month and year is not in the future
        const currentDate = new Date();
        const selectedDate = new Date(month_year + '-01'); // Assume the 1st of the selected month

        if (selectedDate > currentDate) {
            $('#month_year-Error').text('Select Current Month & year.');
            isValid = false;
        }

        // If any field is invalid, stop the process
        if (!isValid) {
            Swal.fire('Error', 'Please fill out all required fields and ensure the date is not in the future.', 'error');
            return; // Stop the AJAX request if validation fails
        }

        $('#loader').show();

        // Proceed with AJAX request if validation is successful
        $.ajax({
            url: '<?= site_url(
                "api/employee-ofthe-month-performance/generate",
            ) ?>',
            method: 'POST',
            data: {
                employee_id: employee_id,
                template_id: template_id,
                month_year: month_year
            },
            xhrFields: {
                responseType: 'blob' // handle PDF as blob
            },
            success: function(data, status, xhr) {
                $('#loader').hide();

                const blob = new Blob([data], {
                    type: 'application/pdf'
                });
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);

                // Get the username from the selected employee
                const employeeName = $('#employee_id option:selected').text().split(' (')[0]; // Extract the name

                // Get current date and time in a readable format
                const currentDate = new Date();
                const formattedDate = currentDate.toISOString().split('T')[0]; // Get yyyy-mm-dd format
                const formattedTime = currentDate.toTimeString().split(' ')[0]; // Get hh:mm:ss format

                // Set the dynamic filename with username and current timestamp
                const filename = `${employeeName}_Performance_${formattedDate}_${formattedTime}.pdf`;

                // Set the download attribute to trigger download with dynamic filename
                link.download = filename;
                link.click();

                // Optional: Clean up the blob URL after download
                window.URL.revokeObjectURL(link.href);

                // Show success message (use SweetAlert or any alert mechanism)
                Swal.fire({
                    title: 'PDF Generated Successfully!',
                    text: 'The performance PDF for ' + employeeName + ' has been generated and downloaded.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Reload the page after the user clicks OK
                        location.reload();
                    }
                });
            },
            error: function(xhr, status, error) {
                $('#loader').hide();

                // Handle custom validation error messages
                if (xhr.status === 400) { // Check for validation errors
                    const response = xhr.responseJSON; // Parse the response as JSON

                    // Handle specific server-side validation errors
                    if (response && response.message) {
                        Swal.fire('Error', response.message, 'error'); // Display server-side error message
                    } else {
                        // Display a generic error message for other issues
                        Swal.fire('Error', 'Certificate already generated for this employee in this month.', 'error');
                    }
                } else {
                    // Handle non-validation errors (e.g., network issues)
                    Swal.fire('Error', 'Something went wrong while processing the request. Please try again.', 'error');
                }
            }
        });
    });
      $(document).ready(function () {
        $('#template_id').on('change', function () {
            const templateId = $(this).val();

            if (templateId) {
                $.ajax({
                    url: `<?= base_url(
                        "api/template/getEmpMonthTemplate/",
                    ) ?>${templateId}`,
                    type: 'GET',
                    success: function (response) {
                        if (response.status && response.data.emp_image) {
                            $('#template-image').attr('src', response.data.emp_image).show();
                        } else {
                            $('#template-image').hide();
                        }
                    },
                    error: function () {
                        $('#template-image').hide();
                    }
                });
            } else {
                $('#template-image').hide();
            }
        });
    });
</script>



<?= $this->endSection() ?>
