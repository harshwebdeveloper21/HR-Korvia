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
                    <input type="hidden" name="id" id="id">
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
                                        <input type="date" class="form-control" name="holiday_date" id="holiday_date" placeholder="title"  />
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
        const token = localStorage.getItem('token');

        // ✅ Get ID from URL
        const urlParts = window.location.pathname.split('/');
        const id = urlParts[urlParts.length - 1];
        // console.log(id);

        if (id) {
            $.ajax({
                url: `/api/holidays/edit_holiday/${id}`,
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        const holiday = response.holiday;
                        $('#id').val(holiday.id);
                        $('#title').val(holiday.title);
                        $('#holiday_date').val(holiday.holiday_date);
                        $('#description').val(holiday.description);

                        $('#submitBtn').text('Update');
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to fetch holiday details.', 'error');
                }
            });
        }
        $('#holidayForm').on('submit', function(e) {
            e.preventDefault();

            const data = {
                id: $('#id').val(),
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
                url: '/api/holidays/update',
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify(data),
                success: function(response) {
                    if (response.status === 'success') {
                        $('#responseMessage').html(`<div class="text-success">${response.message}</div>`);
                        $('#holidayForm')[0].reset();
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