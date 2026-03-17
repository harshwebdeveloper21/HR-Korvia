<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>

<style>
    @media (min-width: 375px) and (max-width: 667px) {
        .sm-margin {
            margin-top: 8px !important;
        }
    }
</style>

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add Holiday</h4>

                <form id="holidayForm">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Title</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-clock-outline fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="title" id="title" placeholder="title" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Holiday Date</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-clock-outline fs-5"></i></span>
                                        </div>
                                        <input type="date" class="form-control" name="holiday_date" id="holiday_date" placeholder="title"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Description</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-clock-outline fs-5"></i></span>
                                        </div>
                                        <textarea class="form-control" id="description" name="description" rows="1" placeholder="Description"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="row mb-3">
                        <label for="description" class="col-sm-2 col-form-label">Description</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="mdi mdi-calendar-range fs-5"></i>
                                    </span>
                                </div>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Description"></textarea>
                            </div>
                        </div>
                    </div> -->

                    <div class="form-group text-end sm-margin">
                        <a href="/holidays" class="btn hr-btnbg">Back</a>
                        <button type="submit" class="btn hr-btnbg" id="submitBtn">Submit</button>
                    </div>

                    <div id="responseMessage" class="mt-2"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $('#holidayForm').on('submit', function(e) {
            e.preventDefault();

            const data = {
                title: $('#title').val(),
                holiday_date: $('#holiday_date').val(),
                description: $('#description').val()
            };
            if (title === '') {
        $('#responseMessage').html(`<div class="text-danger">Please enter the holiday title.</div>`);
        return;
    }

    if (holiday_date === '') {
        $('#responseMessage').html(`<div class="text-danger">Please select the holiday date.</div>`);
        return;
    }
            const token = localStorage.getItem('token');

            $.ajax({
                url: '/api/holioday/store', // You can change this to /api/holidays/store if needed
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify(data),
                success: function(response) {
                    if (response.status === 'success') {
                        $('#responseMessage').html(`<div class="alert text-success">${response.message}</div>`);
                        $('#holidayForm')[0].reset();

                        // Optional: Refresh or redirect after success
                        location.reload();
                    } else {
                        $('#responseMessage').html(`<div class="text-danger">${response.message}</div>`);
                    }
                },
                error: function() {
                    $('#responseMessage').html(`<div class="text-danger">Something went wrong. Please try again.</div>`);
                }
            });
        });
    });
</script>

<?= $this->endSection(); ?>