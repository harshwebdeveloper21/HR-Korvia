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
                <h4 class="card-title">Add Interview</h4>
                <form class="form-sample" method="POST" action="" id="interviewForm">
                    <div class="row">
                        <!-- User Dropdown (Username) -->
                        <div class="col-md-6">
                            <div class="form-group row">

                                <label class="col-sm-3 col-form-label">Candidate name</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <input type="hidden" id="id" name="id" value="">
                                            <span class="input-group-text"><i class="mdi mdi-account fs-5"></i></span>
                                        </div>
                                        <select class="form-select" name="candidate_id" id="candidate" onchange="fetchJobId()">
                                            <option value="" disabled selected>Select Candidate Name</option>
                                            <?php foreach (
                                                $candidates
                                                as $candidate
                                            ): ?>

                                                <option value="<?= $candidate[
                                                    "id"
                                                ] ?>"><?= $candidate[
    "candidate_name"
] ?></option>
                                            <?php endforeach; ?>
                                            <!-- Add more users as needed -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Title -->
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Job Title</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-pencil fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="job_title" id="job_title"
                                            placeholder="Enter Interview Title" />
                                        <script>
                                            function fetchJobId() {
                                                let candidateId = document.getElementById("candidate").value;
                                                console.log(candidateId);
                                                $('#job_title').prop('disabled', true);
                                                if (candidateId) {
                                                    fetch(`<?= base_url(
                                                        "api/get-candidate-job/",
                                                    ) ?>${candidateId}`)
                                                        .then(response => response.json())
                                                        .then(data => {
                                                            if (data.status === 'success') {
                                                                document.getElementById("job_title").value = data.job_title;
                                                            } else {
                                                                document.getElementById("job_title").value = "Not Found";
                                                            }
                                                        })
                                                        .catch(error => console.error('Error:', error));
                                                } else {
                                                    document.getElementById("job_title").value = "";
                                                }
                                            }
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Description -->
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Description</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <!-- <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-pencil fs-5"></i></span>
                                        </div> -->
                                        <textarea class="form-control" name="description" id="description"
                                            placeholder="Enter Interview Description" rows="4"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Schedule Date</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-calendar fs-5"></i></span>
                                        </div>
                                        <input type="datetime-local" class="form-control" name="schedule_date" id="schedule_date" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">

                    </div>
                    <div class="text-end">
                        <a href="<?= base_url(
                            "/addinterview",
                        ) ?>" class="btn hr-btnbg interviewsmbtn">
                            Back
                        </a>
                        <button type="submit" class="btn hr-btnbg interviewsmbtn" id="submitBtn"><span class="spinner-border spinner-border-sm me-2 d-none" id="submitSpinner" role="status" aria-hidden="true"></span>Submit</button>
                    </div>
                    <div id="responseMessage"></div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token');
        let isEditMode = false; // Track if in edit mode
        let interviewId = null;

        $('#interviewForm').on('submit', function(e) {
            e.preventDefault();

            $('#submitBtn').attr('disabled', true);
            $('#submitSpinner').removeClass('d-none');

            // 🔐 Build FormData and add CSRF manually from meta
            const formData = new FormData(this);
            const csrfName = $('meta[name="csrf-token"]').attr('data-name');
            const csrfHash = $('meta[name="csrf-token"]').attr('content');
            formData.append(csrfName, csrfHash);

            // Clear previous validation messages
            $('.invalid-feedback').remove();
            $('.is-invalid').removeClass('is-invalid');

            const url = isEditMode ? `/api/interviews/${interviewId}` : '/api/interviews';
            const method = 'POST';

            $.ajax({
                url: url,
                type: method,
                headers: {
                    'Authorization': `Bearer ${token}`
                    // ❌ Don't set Content-Type manually when using FormData
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false,
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'hr-btnbg',
                        }
                    }).then(() => {
                        window.location.href = "/addinterview";
                    });

                    $('#interviewForm')[0].reset();
                    if (isEditMode) {
                        $('#submitBtn').text('Submit');
                        isEditMode = false;
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    if (response?.message === 'The interview has already been completed and cannot be changed.') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Action Blocked!',
                            text: response.message,
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg',
                            }
                        });
                        return;
                    }

                    if (response?.errors) {
                        if (typeof displayValidationErrors === 'function') displayValidationErrors(response.errors);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response?.message || 'Something went wrong! Please try again.',
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg',
                            }
                        });
                    }
                },
                complete: function() {
                    $('#submitSpinner').addClass('d-none');
                    $('#submitBtn').attr('disabled', false);
                }
            });
        });

        // Get ID from query or URL path
        const params = new URLSearchParams(window.location.search);
        let Id = params.get('id');
        if (!Id) {
            const pathParts = window.location.pathname.split('/');
            Id = pathParts[pathParts.length - 1];
        }

        // You can later use:
        // if (Id) { fetchUserData(Id); }
    });
</script>

<?= $this->endSection() ?>
