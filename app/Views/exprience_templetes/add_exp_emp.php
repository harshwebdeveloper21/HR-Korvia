<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
    @media (max-width: 767px) {
        .interviewsmbtn {
            font-size: 10px !important;
            padding: 8px !important;
            margin-top: 10px !important;
        }

    }
</style>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add Employee</h4>
                <form action="" method="" target="" id="experienceLetterForm">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label leave-sm-emp">Employee Name</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-domain fs-5"></i></span>
                                        </div>
                                        <select class="form-select" name="employee_id" id="employee">
                                            <option value="">Select Employee</option>
                                            <?php foreach (
                                                $employee
                                                as $employees
                                            ): ?>
                                                <option value="<?= $employees[
                                                    "id"
                                                ] ?>"><?= $employees[
    "username"
] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="invalid-feedback d-block" id="employee-error"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label leave-sm-emp">From Date</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-calendar-month-outline fs-5"></i></span>
                                        </div>
                                        <input type="date" class="form-control" name="from_date" id="from_date" readonly />
                                    </div>

                                    <div class="invalid-feedback d-block" id="from_date-error"></div>
                                </div>
                            </div>
                        </div>
                        <!-- From Date -->
                        <div class="col-md-10">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label leave-sm-emp">To Date</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-calendar-month-outline fs-5"></i></span>
                                        </div>
                                        <input type="date" class="form-control" name="to_date" id="to_date" />
                                    </div>


                                    <div class="invalid-feedback d-block" id="to_date-error"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label leave-sm-emp">Templete Name</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-calendar fs-5"></i></span>
                                        </div>
                                        <select class="form-select" name="template_id" id="templete">
                                            <option value="">Select Templete</option>
                                            <?php foreach (
                                                $template
                                                as $templates
                                            ): ?>
                                                <option value="<?= $templates[
                                                    "id"
                                                ] ?>"><?= $templates[
    "title"
] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="invalid-feedback d-block" id="templete-error"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">

                          <div class="text-center" id="template-preview">
                            <img id="template-image" src="" alt="Template Preview" class="img-fluid" style="max-height: 70px; display: none; border: 1px solid #ddd; padding: 5px;">
                        </div>
                        </div>
                    </div>
                    <div class="form-group text-end">
                        <a href="/generate-letter" class="btn hr-btnbg interviewsmbtn">
                            Back
                        </a>
                        <button type="submit" class="btn hr-btnbg interviewsmbtn">
                            <i class="mdi mdi-download iconfontsize"></i> Generate Letter
                        </button>
                    </div>
                </form>
                <div id="responseMessage"></div>
            </div>
        </div>
    </div>
</div>
<script>
    document.querySelector('#experienceLetterForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const employee = document.getElementById('employee');
        const fromDate = document.getElementById('from_date');
        const toDate = document.getElementById('to_date');
        const template = document.getElementById('templete');

        let isValid = true;

        // Clear previous errors
        [employee, fromDate, toDate, template].forEach(field => {
            field.classList.remove('is-invalid');
        });

        document.getElementById('employee-error').innerText = '';
        document.getElementById('from_date-error').innerText = '';
        document.getElementById('to_date-error').innerText = '';
        document.getElementById('templete-error').innerText = '';

        // Validation
        if (employee.value.trim() === '') {
            isValid = false;
            employee.classList.add('is-invalid');
            document.getElementById('employee-error').innerText = 'Please select an employee.';
        }

        if (fromDate.value.trim() === '') {
            isValid = false;
            fromDate.classList.add('is-invalid');
            document.getElementById('from_date-error').innerText = 'Please select a From Date.';
        }

        if (toDate.value.trim() === '') {
            isValid = false;
            toDate.classList.add('is-invalid');
            document.getElementById('to_date-error').innerText = 'Please select a To Date.';
        }

        if (fromDate.value && toDate.value) {
            const from = new Date(fromDate.value);
            const to = new Date(toDate.value);
            if (from > to) {
                isValid = false;
                toDate.classList.add('is-invalid');
                document.getElementById('to_date-error').innerText = 'From Date cannot be later than To Date.';
            }
        }

        if (template.value.trim() === '') {
            isValid = false;
            template.classList.add('is-invalid');
            document.getElementById('templete-error').innerText = 'Please select a template.';
        }

        if (!isValid) return;

        // Proceed with AJAX using Fetch API
        const form = e.target;
        const formData = new FormData(form);

        fetch('<?= base_url("api/generate-experience-letter") ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                const contentType = response.headers.get("Content-Type");

                if (contentType && contentType.includes("application/json")) {
                    return response.json().then(result => {
                        if (result.status === 'error') {
                            Swal.fire("Error", result.message, "error");
                        }
                    });
                }

                if (contentType && contentType.includes("application/pdf")) {
                    return response.blob().then(blob => {
                        let filename = "Experience_Letter.pdf";
                        const disposition = response.headers.get("Content-Disposition");
                        const matches = /filename="?([^"]+)"?/.exec(disposition);
                        if (matches && matches[1]) {
                            filename = matches[1];
                        }

                        const link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = filename;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    });
                }

                throw new Error("Unexpected response type.");
            })
            .catch(error => {
                Swal.fire("Error", "Something went wrong while generating the letter!", "error");
                console.error(error);
            });
    });
</script>

<script>
    $(document).ready(function() {
        $('#employee').on('change', function() {
            var employeeId = $(this).val();

            if (employeeId) {
                $.ajax({
                    url: "<?= site_url(
                        "api/employee/joining-date",
                    ) ?>/" + employeeId,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        if (response.joining_date) {
                            $('#from_date').val(response.joining_date);
                        } else {
                            $('#from_date').val('');
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to fetch joining date.',
                            confirmButtonColor: '#d33'
                        });
                        $('#from_date').val('');
                    }
                });
            } else {
                $('#from_date').val('');
            }
        });
          $('#templete').on('change', function () {
            const templateId = $(this).val();

            if (templateId) {
                $.ajax({
                    url: `<?= base_url("api/template/get/") ?>${templateId}`,
                    type: 'GET',
                    success: function (response) {
                        if (response.status && response.data.template_img) {
                            $('#template-image').attr('src', response.data.template_img).show();
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
