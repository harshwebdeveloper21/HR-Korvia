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
    font-size: 12px !important;
}
      }
</style>

<!-- AJAX Script -->

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link rel="stylesheet" href="<?= base_url("assets/css/jobs.css") ?>">
<div class="container mt-4">
    <!-- Header -->
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold text-dark">
            <i class="fas fa-user-graduate iconfontsize" style="color: #E66136;"></i> Interview Details
        </h2>
        <a href="<?= base_url(
            "/addinterview",
        ) ?>" class="btn hr-btnbg interviewsmbtn">
            <i class="mdi mdi-arrow-left me-1 iconfontsize"></i> Back
        </a>
    </div>

    <!-- Profile Card -->
    <div class="row g-4">
        <div class="col-md-12 clo-lg-12">
            <div class="card shadow-sm">
                <div class="card-body job-det-info job-widget">
                    <h4 class="mb-3 page-title">Personal Information</h4>
                    <hr>

                    <div class="mb-3 row">
                        <h4 class="col-sm-4 info-label font-size-candidate col-6">
                        <i class="mdi mdi-account-outline me-1" style="color: #E66136;"></i> Candidate Name:
                        </h4>
                        <div class="col-sm-8 col-6">
                            <p class="info-value capitalize-text" id="candidate_id"></p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <h4 class="col-sm-4 info-label font-size-candidate col-6">
                        <i class="mdi mdi-briefcase-variant-outline me-1" style="color: #E66136;"></i> Job Name:
                        </h4>
                        <div class="col-sm-8 col-6">
                            <p class="info-value capitalize-text" id="job_title"></p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <h4 class="col-sm-4 info-label font-size-candidate col-6">
                        <i class="mdi mdi-calendar-clock-outline me-1" style="color: #E66136;"></i> Interview Date:
                        </h4>
                        <div class="col-sm-8 col-6">
                            <p class="info-value capitalize-text" id="schedule_date"></p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <h4 class="col-sm-4 info-label font-size-candidate col-6">
                        <i class="mdi mdi-checkbox-marked-circle-outline me-1" style="color: #E66136;"></i> Status:
                        </h4>
                        <div class="col-sm-8 col-6">
                        <p class="info-value capitalize-text" id="status"></p>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <h4 class="col-sm-4 info-label font-size-candidate col-6">
                        <i class="mdi mdi-text-box-outline me-1" style="color: #E66136;"></i> Description:
                        </h4>
                        <div class="col-sm-8 col-6">
                        <p class="info-value capitalize-text" id="description"></p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- Right Column -->

    </div>
</div>


<script>
 $(document).ready(function () {
    const token = localStorage.getItem('token'); // Get token for authorization
    const interviewId = window.location.pathname.split('/').pop(); // Get Job ID from URL

    console.log("interview ID from URL:", interviewId); // Debugging output

    if (!interviewId || isNaN(interviewId)) {  // Ensure it's a valid number
        $('#job_type').text("Error: No interview ID provided.");
        return;
    }

    // Fetch job details using AJAX
    $.ajax({
        url: `/api/interviews/${interviewId}`,  // Ensure this matches your actual API route
        type: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
        },
        success: function (response) {
            if (response.status === 'success') {
                const interview = response.data;

                // Populate job details dynamically
                $('#candidate_id').text(interview.candidate_name);
                $('#job_title').text(interview.job_title);

                $('#status').text(interview.interview_status);

                // $('#description').text(interview.description);
                $('#description').text(interview.description ? interview.description : 'N/A');

                $('#schedule_date').text(interview.schedule_date);



            }
        },
        error: function (xhr) {
            $('#job_type').text("Error fetching interview details.");
            console.error("Error:", xhr.responseText);
        }
    });
});

</script>

<?= $this->endSection() ?>
