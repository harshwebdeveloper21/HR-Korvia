<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
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
       @media (max-width: 768px) {
.font-size-candidate{
    font-size: 10px !important;
}
       }
       .capitalize-text {
        text-transform: capitalize;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link rel="stylesheet" href="<?= base_url("assets/css/jobs.css") ?>">
<div class="container mt-4">
    <!-- Header -->
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold text-dark">
            <i class="fas fa-user-graduate iconfontsize" style="color: #E66136;"></i></i>Candidate Onboarding Details
        </h2>
        <a href="<?= base_url(
            "/onboardingview",
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
                            <i class="mdi mdi-account-outline me-1" style="color: #E66136;"></i> Candidate Name:
                        </h4>
                        <div class="col-sm-6 col-6">
                            <p class="info-value capitalize-text" id="candidate_id"></p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <h4 class="col-sm-6 info-label font-size-candidate col-6">
                            <i class="mdi mdi-email-outline me-1" style="color: #E66136;"></i> Email:
                        </h4>
                        <div class="col-sm-6 col-6">
                            <p class="info-value" id="email"></p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <h4 class="col-sm-6 info-label font-size-candidate col-6">
                            <i class="mdi mdi-card-account-details-outline me-1" style="color: #E66136;"></i>Job Apply Date:
                        </h4>
                        <div class="col-sm-6 col-6">
                            <p class="info-value" id="job_apply_date"></p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <h4 class="col-sm-6 info-label font-size-candidate col-6">
                            <i class="mdi mdi-briefcase-outline me-1" style="color: #E66136;"></i>Documents:
                        </h4>
                        <div class="col-sm-6 col-6">
                            <ul id="documents" class="documnet-ps">
                                <!-- List items will be dynamically added here -->
                            </ul>
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
                    <span><i class="fa fa-bar-chart" style="color: #E66136;"></i></span>
                    <h5>Job Type</h5>
                    <p id="job_title" class="capitalize-text"></p>
                </div>
                <div class="info-list">
                    <span><i class="fa fa-money" style="color: #E66136;"></i></span>
                    <h5>Department</h5>
                    <p id="departement" class="capitalize-text"></p>
                </div>
                <div class="info-list">
                    <span><i class="fa fa-suitcase" style="color: #E66136;"></i></span>
                    <h5>Job Start Date</h5>
                    <p id="job_date"></p>
                </div>
                <div class="info-list">
                    <span><i class="fa fa-ticket" style="color: #E66136;"></i></span>
                    <h5>Onboarding Status</h5>
                    <p id="onbording" class="capitalize-text"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AJAX Script -->
<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token'); // Get token for authorization
        const onboardingId = window.location.pathname.split('/').pop(); // Get Job ID from URL

        console.log("onboarding ID from URL:", onboardingId); // Debugging output

        if (!onboardingId || isNaN(onboardingId)) { // Ensure it's a valid number
            $('#candidate_id').text("Error: No Job ID provided.");
            return;
        }
        // Fetch job details using AJAX
        $.ajax({
            url: `/api/onboarding/${onboardingId}`, // Ensure this matches your actual API route
            type: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
            success: function(response) {
                if (response.status === 'success') {
                    const onboarding = response.data;

                    // Populate job details dynamically
                    $('#candidate_id').text(onboarding.candidate_name);
                    $('#email').text(onboarding.email);

                    $('#job_title').text(onboarding.job_title);
                    $('#departement').text(onboarding.department_name);
                    $('#job_date').text(onboarding.start_date);
                    $('#onbording').text(onboarding.onboarding_status);
                    $('#job_apply_date').text(onboarding.job_date);
                    const documents = onboarding.docu_submitted.split(',');
                    const docList = $('#documents');
                    docList.empty(); // Clear any existing content

                    documents.forEach(doc => {
                        docList.append(`<li>${doc.trim()}</li>`);
                    });
                } else {
                    $('#candidate_id').text("onboarding not found.");
                }
            },
            error: function(xhr) {
                $('#candidate_id').text("Error fetching job details.");
                console.error("Error:", xhr.responseText);
            }
        });
    });
</script>

<?= $this->endSection() ?>
