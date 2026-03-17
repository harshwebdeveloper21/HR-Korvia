<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Hero Section -->
<section class="text-center py-4">
    <h2 class="fw-bold">FIND YOUR DREAM JOB HERE!</h2>
    <p class="text-muted">Explore the latest job openings and apply for the best opportunities available today!</p>
    <div class="container">
        <!-- Search Bar -->
        <div class="search-bar mb-4">
            <input type="text" class="form-control" id="jobSearch" placeholder="Search jobs">
        </div>
    </div>
</section>

<!-- Job Listings -->
<div class="container">
    <div class="row" id="jobList"></div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token'); // Get token for authorization
        let jobsData = []; // Store jobs for searching

        fetchJobs();

        function fetchJobs() {
            $.ajax({
                url: "<?= base_url('api/applyjob'); ?>",
                type: "GET",
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                dataType: "json",
                success: function(response) {
                    if (response.status === 'success') {
                        jobsData = response.data; // Store the data for searching
                        renderJobs(jobsData);
                    } else {
                        $('#jobList').html('<p class="text-danger">No jobs available.</p>');
                    }
                },
                error: function() {
                    $('#jobList').html('<p class="text-danger">Error fetching jobs.</p>');
                }
            });
        }

        function renderJobs(jobs) {
            let jobHtml = '';
            jobs.forEach(function(job) {
                jobHtml += `
                <div class="col-md-4 mb-3 job-item" data-title="${job.job_title.toLowerCase()}" data-department="${job.department_name.toLowerCase()}">
                    <div class="card job-card">
                        <div class="card-body">
                            <h5 class="job-title fw-bold" style="color: #E66136;">${job.job_title}</h5>
                            <div class="job-tags mb-2">
                                <span class="badge text-dark">
                                    <i class="fas fa-briefcase"></i> ${job.department_name}
                                </span>
                            </div>
                            <p class="text-muted mb-1"><i class="fas fa-clock"></i> ${job.experience} Years</p>
                            <p class="text-muted"><i class="fas fa-calendar-alt"></i> Posted ${job.post_date} days ago</p>
                        </div>
                        <div class="card-footer bg-white text-end">
                            <a href="<?= base_url('/candidate') ?>" class="btn rounded-pill px-4 hr-btnbg">
                                Apply <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            `;
            });
            $('#jobList').html(jobHtml);
        }

        // Search functionality
        $('#jobSearch').on('keyup', function() {
            let searchText = $(this).val().toLowerCase();
            $('.job-item').each(function() {
                let jobTitle = $(this).data('title');
                let jobDept = $(this).data('department');

                if (jobTitle.includes(searchText) || jobDept.includes(searchText)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });
</script>

<?= $this->endSection(); ?>