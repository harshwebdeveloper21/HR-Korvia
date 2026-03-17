<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
     .capitalize-text {
        text-transform: capitalize;
    }
      @media (max-width: 767px) {
    .interviewsmbtn{
     font-size: 10px !important;
    padding: 8px !important;
    margin-top: 10px !important;
}
.font-size-candidate{
    font-size: 11px !important;
}
      }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link rel="stylesheet" href="<?= base_url("assets/css/jobs.css") ?>">
<div class="container mt-4">
    <!-- Header -->
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold text-dark">
            <i class="fas fa-user-graduate" style="color: #E66136;"></i></i> Candidate Details
        </h2>
        <a href="<?= base_url(
            "/candidateview",
        ) ?>" class="btn hr-btnbg interviewsmbtn">
            <i class="mdi mdi-arrow-left me-1 iconfontsize"></i> Back
        </a>
    </div>

    <!-- Profile Card -->
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body job-det-info job-widget">
                    <h4 class="mb-3 page-title">Personal Information</h4>
                    <hr>

                    <div class="mb-3 row">
                        <h4 class="col-sm-6 info-label font-size-candidate col-6">
                            <i class="mdi mdi-account-circle me-1" style="color: #E66136;"></i> Candidate Name:
                        </h4>
                        <div class="col-sm-6 col-6">
                            <p class="info-value capitalize-text" id="candidate_id"></p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <h4 class="col-sm-6 info-label font-size-candidate col-6">
                            <i class="mdi mdi-email me-1" style="color: #E66136;"></i> Email:
                        </h4>
                        <div class="col-sm-6 col-6">
                            <p class="info-value" id="email"></p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <h4 class="col-sm-6 info-label font-size-candidate col-6">
                            <i class="mdi mdi-phone me-1" style="color: #E66136;"></i>Phone:
                        </h4>
                        <div class="col-sm-6 col-6">
                            <p class="info-value capitalize-text" id="phone"></p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <h4 class="col-sm-6 info-label font-size-candidate col-6">
                            <i class="mdi mdi-check-circle me-1" style="color: #E66136;"></i>Description:
                        </h4>
                        <div class="col-sm-6 col-6">
                            <p class="info-value capitalize-text" id="description"></p>
                            <!-- List items will be dynamically added here -->

                        </div>
                    </div>

                    <div class="mb-3 row">
                        <h4 class="col-sm-6 info-label font-size-candidate col-6">
                            <i class="mdi mdi-file-document me-1" style="color: #E66136;"></i>Resume:
                        </h4>
                        <div class="col-sm-6 col-6">
                            <!-- <p class="info-value" id="Resume"></p> -->
                            <p><span id="resume" class="text-muted capitalize-text"></span></p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- Right Column -->
        <div class="col-md-4">
            <div class="job-det-info job-widget">
                <h4 class="page-title">Job Information</h4>
                <hr>
                <div class="info-list">
                    <span><i class="mdi mdi-clipboard-text" style="color: #E66136;"></i></span>
                    <h5>Job Title:</h5>
                    <p id="job_type" class="capitalize-text"></p>
                </div>
                <div class="info-list">
                    <span><i class="mdi mdi-calendar" style="color: #E66136;"></i></span>
                    <h5>Application Date:</h5>
                    <p id="post_date" class="capitalize-text"></p>
                </div>
                <div class="info-list">
                    <span><i class="mdi mdi-progress-check" style="color: #E66136;"></i></span>
                    <h5>Status</h5>
                    <p id="statuss" class="capitalize-text"></p>
                </div>

            </div>
        </div>
    </div>
</div>


<!-- AJAX Script -->
<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token'); // Get token for authorization
        const candidateId = window.location.pathname.split('/').pop(); // Get Job ID from URL

        console.log("candidate ID from URL:", candidateId); // Debugging output

        if (!candidateId || isNaN(candidateId)) { // Ensure it's a valid number
            $('#job_type').text("Error: No interview ID provided.");
            return;
        }

        // Fetch job details using AJAX
        $.ajax({
            url: `/api/candidate/${candidateId}`, // Ensure this matches your actual API route
            type: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
            success: function(response) {
                if (response.status === 'success') {
                    const candidate = response.data;

                    // Populate job details dynamically
                    $('#candidate_id').text(candidate.candidate_name);
                    $('#email').text(candidate.email);
                    $('#phone').text(candidate.phone_number);
                    $('#status').text(candidate.candidate_status);

                    $('#description').text(candidate.notes || 'N/A');
                    $('#job_type').text(candidate.job_title);
                    $('#post_date').text(candidate.post_date);
                    $('#statuss').text(candidate.status);
                    if (candidate.resume) {
                        let resumeUrl = "<?= base_url(
                            "api/candidate/download-resume/",
                        ) ?>" + candidate.id;
                        console.log("Generated Resume URL:", resumeUrl);
                        $('#resume').html(
                            `<a href="${resumeUrl}" class="bn-download" style="color: #E66136;" download>Download Resume</a>`
                        );
                    } else {
                        $('#resume').html('<p>No Resume Available</p>');
                    }
                }
            },
            error: function(xhr) {
                $('#job_type').text("Error fetching interview details.");
                console.error("Error:", xhr.responseText);
            }
        });
    });
</script>

<?= $this->endSection() ?>
