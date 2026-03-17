<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="<?= base_url("assets/css/jobs.css") ?>">
<style>
     .capitalize-text {
        text-transform: capitalize;
    }
    .lg-btn-show{
        display: block;
    }
    .lg-btn-none{
        display: none;
    }
       @media (max-width: 767px) {
          .lg-btn-show{
        display: none;
    }
    .lg-btn-none{
        display: block;
    }
    .text-mrgindetail{
        margin-top: 15px !important;
    }
    .interviewsmbtn{
        font-size: 12px !important;
        padding: 8px !important;
    }
       }
</style>
<div class="page-wrapper">
    <!-- Page Content -->
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="row">
                <div class="col-sm-12 ml-4 col-6">
                    <h3 class="mb-0 fw-bold text-dark text-mrgindetail">
                        <i class="mdi mdi-briefcase-outline me-2 iconfontsize" style="color: #E66136;"></i> Job Details
                         </h3>


                </div>
                 <div class="text-end lg-btn-none col-6">
                              <a href="<?= base_url(
                                  "/jobview",
                              ) ?>" class="btn hr-btnbg interviewsmbtn">
                                 <i class="mdi mdi-arrow-left iconfontsize me-1"></i>Back</a>
                </div>
            </div>
        </div>
        <!-- /Page Header -->
        <div class="row">
            <div class="col-md-8">

                <div class="job-info job-widget">
                    <h3 class="job-title">
                        <i class="mdi mdi-briefcase-outline me-2" style="color: #E66136;"></i>
                        <span id="job_title"></span>
                    </h3>
                    <hr>
                    <span class="job-dept">
                        <i class="mdi mdi-office-building-marker-outline me-1" style="color: #E66136;"></i>
                        <span id="department"></span>
                    </span>

                    <ul class="job-post-det">
                        <li>
                            <i class="mdi mdi-calendar-outline" style="color: #E66136; font-size:18px;"></i>
                            Job Apply Date:
                            <span style="color: #E66136;" id="post_date"></span>
                        </li>
                        <li>
                            <i class="mdi mdi-calendar-outline" style="color: #E66136; font-size:18px;"></i>
                            Last Date:
                            <span style="color: #E66136;" id="close_date"></span>
                        </li>
                    </ul>

                </div>
                <div class="job-content job-widget">
                    <div class="job-desc-title">
                        <h4>
                            <i class="mdi mdi-file-document-outline me-2" style="color: #E66136;"></i>
                            Job Description
                        </h4>
                    </div>

                    <div class="job-description">
                        <p id="description" class="capitalize-text"></p>
                    </div>

                </div>
            </div>
            <div class="col-md-4">
                <div class="job-det-info job-widget">
                    <a href="<?= base_url(
                        "/jobview",
                    ) ?>" class="btn hr-btnbg w-100 lg-btn-show">Back</a>
                    <div class="info-list">
                        <span><i class="fa fa-bar-chart"></i></span>
                        <h5>Job Type</h5>
                        <p id="job_type" class="capitalize-text"></p>
                    </div>
                    <div class="info-list">
                        <span><i class="fa fa-money"></i></span>
                        <h5>Salary</h5>
                        <p id="salary_range" class="capitalize-text"></p>
                    </div>
                    <div class="info-list">
                        <span><i class="fa fa-suitcase"></i></span>
                        <h5>Experience</h5>
                        <p id="experience" class="capitalize-text"></p>
                    </div>
                    <div class="info-list">
                        <span><i class="fa fa-ticket"></i></span>
                        <h5>Age</h5>
                        <p id="age" class="capitalize-text"></p>
                    </div>
                    <div class="info-list">
                        <span><i class="fa fa-ticket"></i></span>
                        <h5>Gender</h5>
                        <p id="gender" class="capitalize-text"></p>
                    </div>
                    <div class="info-list">
                        <span><i class="fa fa-map-signs"></i></span>
                        <h5>Location</h5>
                        <p id="location" class="capitalize-text">Loading...</p>
                        <p id="address" class="capitalize-text">Loading...</p>
                        <p id="city" class="capitalize-text">Loading...</p>
                        <p id="country" class="capitalize-text">Loading...</p>
                        <p id="postal_code" class="capitalize-text">Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- AJAX Script -->
<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token'); // Get token for authorization
        const jobId = window.location.pathname.split('/').pop(); // Get Job ID from URL

        console.log("Job ID from URL:", jobId); // Debugging output

        if (!jobId || isNaN(jobId)) { // Ensure it's a valid number
            $('#job_title').text("Error: No Job ID provided.");
            return;
        }

        // Fetch job details using AJAX
        $.ajax({
            url: "<?= base_url("api/job/") ?>" + jobId,
            type: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
            success: function(response) {
                if (response.status === 'success') {
                    const job = response.data;
                    console.log("Job Data:", job); // Check if gender exists
                    // Populate job details dynamically
                    $('#job_title').text(job.job_title);
                    $('#description').text(job.description || 'N/A');
                    // $('#salary_range').text(job.salary_range ? '$' + job.salary_range : 'N/A');
                    $('#department').text(job.department_name);
                    $('#job_type').text(job.job_type);
                    $('#salary_range').text('$' + job.salary_range);
                    $('#applied_for').text(job.job_title);
                    $('#post_date').text(job.post_date);
                    $('#close_date').text(job.close_date);
                    $('#experience').text(job.experience);
                    $('#status').text(job.status);
                    $('#location').text(job.job_location);
                    $('#address').text(job.address);
                    $('#city').text(job.city_name);
                    $('#country').text(job.country_name);
                    $('#postal_code').text(job.postal_code);
                    $('#age').text(job.age);
                    $('#gender').text(job.gender ? job.gender : 'N/A'); // Ensure gender displays
                } else {
                    $('#job_title').text("Job not found.");
                }
            },
            error: function(xhr) {
                $('#job_title').text("Error fetching job details.");
                console.error("Error:", xhr.responseText);
            }
        });
    });
</script>

<?= $this->endSection() ?>
