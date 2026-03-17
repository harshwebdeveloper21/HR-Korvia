<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
 .perviedisplay {
        display: flex;
        justify-content: end;
        margin-bottom: 2px;

    }

    @media (max-width: 767px) {
        .interviewsmbtn {
            font-size: 10px !important;
            padding: 5px !important;
            margin-top: 10px !important;
        }

        .perviedisplay {
            display: block !important;
            justify-content: unset !important;
            margin-bottom: 0px !important;
        }
    }
</style>
<!-- designation -->

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add Performance</h4>



                <form class="form-sample" id="performanceForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrfToken">
    <input type="hidden" name="id" id="id" value="">

    <!-- Employee Name & Designation -->
    <div class="row gy-3">
        <!-- Employee Name -->
        <div class="col-md-6">
            <label class="form-label leave-sm-emp">Employee Name</label>
            <div class="input-group">
                <span class="input-group-text"><i class="mdi mdi-account fs-5"></i></span>
                <select class="form-select" name="user_id" id="user_id" onchange="fetchDesignation()">
                    <option value="" disabled selected>Select Employee Name</option>
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?= $employee["id"] ?>"><?= esc(
    $employee["username"],
) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Designation + Add Designation Button -->
        <div class="col-md-6">

                <label class="form-label leave-sm-emp">Designation</label>
               <div class="input-group">
                <span class="input-group-text"><i class="mdi mdi-briefcase fs-5"></i></span>
                <select class="form-select" name="designation_id" id="designation_id">
                    <option value="" selected>Select Designation</option>
                       <script>
                                                function fetchDesignation() {
                                                    return new Promise((resolve, reject) => {
                                                        let userId = document.getElementById("user_id").value;

                                                        if (userId) {
                                                            fetch(`<?= base_url(
                                                                "api/get-user-designation/",
                                                            ) ?>${userId}`)
                                                                .then(response => response.json())
                                                                .then(data => {
                                                                    if (data.status === 'success') {
                                                                        document.getElementById("designation_id").innerHTML = `
                                                                            <option value="${data.designation_id}" selected>
                                                                                ${data.designation_name} - ${data.department_name}
                                                                            </option>`;
                                                                    } else {
                                                                        document.getElementById("designation_id").innerHTML = `<option value="" selected>No Designation Found</option>`;
                                                                    }
                                                                    resolve();
                                                                })
                                                                .catch(error => {
                                                                    console.error('Error:', error);
                                                                    reject(error);
                                                                });
                                                        } else {
                                                            document.getElementById("designation_id").innerHTML = `<option value="" selected>Select Designation</option>`;
                                                            resolve();
                                                        }
                                                    });
                                                }
                                            </script>
                </select>
            </div>
        </div>
    </div>

    <!-- Reviewer & Review Date -->
    <div class="row gy-3 mt-1">
        <div class="col-md-6">
            <label class="form-label leave-sm-emp">Reviewer</label>
            <div class="input-group">
                <span class="input-group-text"><i class="mdi mdi-account fs-5"></i></span>
                <select class="form-select" name="reviewer_id" id="reviewer_id">
                    <option value="" disabled selected>Select Reviewer</option>
                    <?php foreach ($reviewers as $reviewer): ?>
                        <option value="<?= $reviewer["id"] ?>"><?= esc(
    $reviewer["username"],
) ?> (<?= esc($reviewer["role"]) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label leave-sm-emp">Review Date</label>
            <div class="input-group">
                <span class="input-group-text"><i class="mdi mdi-calendar fs-5"></i></span>
                <input type="date" class="form-control" name="review_date" id="review_date" />
            </div>
        </div>
    </div>

    <!-- Performance Inputs -->
    <div class="row gy-3 mt-1">
        <div class="col-md-6">
            <label class="form-label leave-sm-emp">Goals Achieved</label>
            <textarea class="form-control" name="goals_achieved" id="goals_achieved" placeholder="Enter Goals Achieved"></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label leave-sm-emp">Team Work</label>
            <textarea class="form-control" name="team_work" id="team_work" placeholder="Enter Team Work"></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label leave-sm-emp">Management</label>
            <textarea class="form-control" name="management" id="management" placeholder="Enter Management Skill"></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label leave-sm-emp">Presentation Skill</label>
            <textarea class="form-control" name="presentation_skill" id="presentation_skill" placeholder="Enter Presentation Skill"></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label leave-sm-emp">Behaviour</label>
            <textarea class="form-control" name="behaviour" id="behaviour" placeholder="Enter Behaviour"></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label leave-sm-emp">Rating</label>
            <div class="input-group">
                <span class="input-group-text"><i class="mdi mdi-star fs-5"></i></span>
                <input type="number" class="form-control" name="rating" id="rating" placeholder="Enter Rating" />
            </div>
        </div>
    </div>

    <!-- Notes -->
    <div class="row gy-3 mt-1">
        <div class="col-md-6">
            <label class="form-label leave-sm-emp">Notes</label>
            <textarea class="form-control" name="notes" id="notes" placeholder="Enter Notes" rows="4"></textarea>
        </div>
    </div>

    <!-- Buttons -->
    <div class="text-end mt-4">
        <a href="/performanceview" class="btn hr-btnbg interviewsmbtn me-2">Back</a>
        <button type="submit" class="btn hr-btnbg interviewsmbtn" id="submitBtn">Submit</button>
    </div>
</form>

                <div id="responseMessage"></div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token');
        let isEditMode = false; // Flag to track whether we're in edit mode
        let performanceId = null; // To store the performance ID for updating

        // Handle form submission
        $('#performanceForm').on('submit', function(e) {
            e.preventDefault();

            // Clear previous validation messages
            $('.invalid-feedback').remove();
            $('.is-invalid').removeClass('is-invalid');

            let url = `<?= base_url(
                "api/performance/create",
            ) ?>`; // Default URL for insert
            let method = 'POST'; // Default method for insert
            let formData = new FormData();

            // Manually gather form data
            $('#performanceForm').find('input, select, textarea').each(function() {
                const name = $(this).attr('name');
                const value = $(this).val();
                if (name && value) {
                    formData.append(name, value);
                }
            });

            if (isEditMode) {
                url = `<?= base_url(
                    "api/performance/update/",
                ) ?>${performanceId}`; // URL for update
                method = 'POST'; // Method for update
            }
              let csrfTokenName = '<?= csrf_token() ?>';
             let csrfTokenValue = $('#csrfToken').val();
            formData.append(csrfTokenName, csrfTokenValue);
            $('#loader').show();

            $.ajax({
                url: url,
                type: method,
                headers: {
                    'Authorization': `Bearer ${token}`,
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#loader').hide();

                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: isEditMode ? 'Updated Successfully!' : 'Inserted Successfully!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false,
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg',

                            }
                        }).then(() => {
                            window.location.href = '/performanceview'; // Redirect to performance view
                        });

                        $('#performanceForm')[0].reset(); // Reset the form
                        if (isEditMode) {
                            $('#submitBtn').text('Submit'); // Reset button text after update
                            isEditMode = false;
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message,
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg',

                            }
                        });
                    }
                },
                error: function(xhr) {
                    $('#loader').hide();

                    let message = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred.';

                    // Check if message is an object (validation errors), otherwise, show the message directly
                    if (typeof message === 'object') {
                        $.each(message, function(key, value) {
                            let inputField = $(`[name="${key}"]`);
                            if (inputField.length) {
                                inputField.addClass('is-invalid');
                                if (!inputField.next('.invalid-feedback').length) {
                                    inputField.after(`<div class="invalid-feedback">${value}</div>`);
                                }
                            }
                        });
                    } else {
                        // Display general error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: message, // Shows "Performance record already exists for this month."
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg',

                            }
                        });
                    }
                }

            });
        });

        const params = new URLSearchParams(window.location.search);
        let Id = params.get('id');

        if (!Id) {
            const pathParts = window.location.pathname.split('/');
            var temp_id = pathParts[pathParts.length - 1];
            if (!isNaN(temp_id) && !isNaN(parseFloat(temp_id))) {
                Id = temp_id;
            }
        }

        if (Id) {
            performanceId = Id;
            fetchPerformanceData(Id);
        }

        // Function to fetch performance data for editing
        function fetchPerformanceData(Id) {
            console.log("Fetching data for performance ID: " + Id);
            $.ajax({
                url: `<?= base_url("api/performance/") ?>${Id}`,
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                },
                success: function(responseData) {
                    console.log(responseData);

                    if (responseData.status === 'success' && responseData.data.length > 0) {
                        const performance = responseData.data[0];

                        $('#user_id').val(performance.user_id);
                        $('#review_date').val(performance.review_date);
                        $('#reviewer_id').val(performance.reviewer_id);
                        // $('#designation_id').val(performance.designation_id);
                        $('#goals_achieved').val(performance.goals_achieved);
                        $('#team_work').val(performance.team_work);
                        $('#management').val(performance.management);
                        $('#presentation_skill').val(performance.presentation_skill);
                        $('#behaviour').val(performance.behaviour);
                        $('#rating').val(performance.rating);
                        $('#notes').val(performance.notes);
                        // Fetch designation based on user_id
                        fetchDesignation().then(() => {
                            $('#designation_id').val(performance.designation_id); // Set it after options are loaded
                        });
                        $('#id').val(performance.id);
                        $('#submitBtn').text('Update');
                        $('.card-title').text('Edit Performance');
                        isEditMode = true;
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Not Found!',
                            text: 'Performance record not found.',
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg',

                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching performance:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Error fetching performance record.',
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'hr-btnbg',

                        }
                    });
                }
            });
        }
    });

</script>
<?= $this->endSection() ?>
