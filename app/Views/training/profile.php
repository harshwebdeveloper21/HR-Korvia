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
<!-- Include FontAwesome (if not already included) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link rel="stylesheet" href="<?= base_url("assets/css/jobs.css") ?>">
<div class="container mt-4">
    <!-- Header -->
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold text-dark">
            <i class="fas fa-user-graduate iconfontsize" style="color: #E66136;"></i></i> Employee Training Details
        </h2>
        <a href="<?= base_url(
            "/trainingview",
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
                        <h4 class="col-sm-5 info-label font-size-candidate col-6">
                            <i class="mdi mdi-account-outline me-1" style="color: #E66136;"></i> Name:
                        </h4>
                        <div class="col-sm-7 col-6">
                            <p class="info-value capitalize-text" id="employeeName"></p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <h4 class="col-sm-5 info-label font-size-candidate col-6">
                            <i class="mdi mdi-email-outline me-1" style="color: #E66136;"></i> Email:
                        </h4>
                        <div class="col-sm-7 col-6">
                            <p class="info-value capitalize-text" id="employeeEmail"></p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <h4 class="col-sm-5 info-label font-size-candidate col-6">
                            <i class="mdi mdi-card-account-details-outline me-1" style="color: #E66136;"></i> Employee ID:
                        </h4>
                        <div class="col-sm-7 col-6">
                            <p class="info-value capitalize-text" id="employee_id"></p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <h4 class="col-sm-5 info-label font-size-candidate col-6">
                            <i class="mdi mdi-briefcase-outline me-1" style="color: #E66136;"></i> Designation:
                        </h4>
                        <div class="col-sm-7 col-6">
                            <p class="info-value capitalize-text" id="designation"></p>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <h4 class="col-sm-5 info-label font-size-candidate col-6">
                            <i class="mdi mdi-domain me-1" style="color: #E66136;"></i> Department:
                        </h4>
                        <div class="col-sm-7 col-6">
                            <p class="info-value capitalize-text" id="employeeDepartment"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Right Column -->
        <div class="col-md-4">
            <div class="job-det-info job-widget">
                <h4 class="page-title">Training Information</h4>
                <hr>
                <div class="info-list">
                    <span><i class="fas fa-file-alt" style="color: #E66136;"></i></span>
                    <h5>Training Title</h5>
                    <p id="training_title" class="capitalize-text"></p>
                </div>
                <div class="info-list">
                    <span><i class="fas fa-calendar-alt" style="color: #E66136;"></i></span>
                    <h5>Training Start Date:</h5>
                    <p id="start_date" class="capitalize-text"></p>
                </div>
                <div class="info-list">
                    <span><i class="fas fa-calendar-check" style="color: #E66136;"></i></span>
                    <h5>Training End Date:</h5>
                    <p id="end_date" class="capitalize-text"></p>
                </div>
                <div class="info-list">
                    <span><i class="fas fa-map-marker-alt" style="color: #E66136;"></i></span>
                    <h5>Location:</h5>
                    <p id="location" class="capitalize-text"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token');

        // Extract userId from the current URL dynamically
        const userId = window.location.pathname.split('/').pop(); // Extract last part of URL, e.g., '1'

        $.ajax({
            url: `<?= site_url("training/details") ?>/${userId}`,
            type: "GET",
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
            success: function(response) {
                if (response.status === 'success') {
                    let data = response.data;
                    // Populate fields with data
                    $("#employeeName").text(data.employee_name ?? 'N/A');
                    $("#designation").text(data.designation_name ?? 'N/A'); // already used
                    $("#department").text(data.department_name ?? 'N/A');
                    $("#employeeEmail").text(data.email ?? 'N/A');
                    // $("#employee_id").text(data.employee_id ?? 'N/A');
                    $("#employee_id").text(data.employee_id ? 'EMP#' + data.employee_id : 'N/A');
                    $("#employeeDesignation").text(data.designation_name ?? 'N/A');
                    $("#employeeDepartment").text(data.department_name ?? 'N/A');
                    $("#training_title").text(data.training_title ?? 'N/A');
                    $("#description").text(data.description ?? 'N/A');
                    $("#start_date").text(data.start_date ?? 'N/A');
                    $("#end_date").text(data.end_date ?? 'N/A');
                    $("#location").text(data.location ?? 'N/A');

                    // Hide any previous error messages
                    $("#error-message").hide();
                } else {
                    // Display error message if no record found
                    $("#error-message").text('training record not found. Please check the ID and try again.').show();
                }
            },
            error: function(xhr, status, error) {
                // Handle specific error status codes
                if (xhr.status === 404) {
                    // Handle 404 not found error
                    $("#error-message").text('training record not found. Please check the ID and try again.').show();
                } else {
                    // Display a generic error message
                    $("#error-message").text('An error occurred while loading training data. Please try again later.').show();
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>
