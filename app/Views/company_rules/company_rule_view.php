<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
    @media (max-width: 767px) {
        .attendenceall {
            font-size: 9px !important;
            padding: 5.3px !important;
        }

        .iconfontsize {
            font-size: 11px !important;
        }

        .cart-sm-title {
            font-size: 12px !important;
            margin-bottom: 5px !important;
        }

        .dataTables_length {
            margin-left: .1rem !important;
            margin-bottom: .5rem !important;
            font-size: 12px !important;
            float: left !important;
        }

        .dataTables_filter {
            font-size: 12px !important;
            float: left !important;
            /* margin-left: -5rem !important;  */
        }

        .col-sm-12.col-md-6 {
            flex: 0 0 25%;
            max-width: 26%;
        }

        .dataTables_filter label:before {
            content: "" !important;
        }

        #leaveTypes-Table_length label {
            display: flex;
            align-items: center;
        }

        /* Hide the text inside the label */
        #leaveTypes-Table_length::first-text,
        #leaveTypes-Table_length label::before {
            display: none !important;
        }

        /* Or a simpler and reliable trick */
        #leaveTypes-Table_length label {
            font-size: 0;
            /* hide text */
        }

        #leaveTypes-Table_length label input {
            font-size: 10px;
            /* reset font size for input */
        }

        #leaveTypes-Table_length label {
            font-size: 0px;
            /* hide all text inside the label */
        }

        #leaveTypes-Table_filter label {
            font-size: 0;
        }

        #leaveTypes-Table_filter input {
            font-size: 14px;
            /* Keep input font size normal */
        }

        #leaveTypes-Table_length label select {
            font-size: 14px;
            /* restore font size for the dropdown */
        }

        div.dataTables_wrapper div.dataTables_filter input {
            margin-left: 0.5em;
            display: inline-block;
            width: 212px !important;
            height: 29px !important
        }

        .custom-select {
            height: 26px !important;
            width: 57px !important;
        }


        /* .form-control {
            height: 0px !important;
        }  */
    }
</style>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Manage Rules</h4>
                    <!-- <a href="/creates-rules" class="btn hr-btnbg attendenceall">
                        <i class="mdi mdi-plus iconfontsize"></i> Add Rule
                    </a> -->
                </div>
                <div class="table-responsive">
                    <table class="table table-striped" id="leaveTypes-Table">
                        <thead>
                            <tr>
                                <!-- <th>#</th> -->
                                <th>Working Hours</th>
                                <th>Saturday Off Pattern</th>
                                <th>Yearly Holidays</th>
                                <th>Tax</th>
                                <th>Taxable Salary Amount</th>
                                <th>Lunch Breack</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="rules-Table-Body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const token = localStorage.getItem('token');

        fetch('/api/company-rules/rules', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(responseData => {
                if (responseData.status === 'success') {
                    const rules = responseData.rules || responseData.data; // supports either key
                    let tableRows = '';

                    const ordinalMap = {
                        '1': '1st Saturday',
                        '2': '2nd Saturday',
                        '3': '3rd Saturday',
                        '4': '4th Saturday',
                        '5': '5th Saturday',
                    };

                    rules.forEach((rule, index) => {
                        // Convert "1,2" to "1st Saturday and 2nd Saturday"
                        const saturdayOffList = rule.saturday_off_pattern
                            .split(',')
                            .map(num => ordinalMap[num.trim()])
                            .filter(Boolean)
                            .join(' and ');

                        tableRows += `
                    <tr data-id="${rule.id}">
                        <td>${rule.working_hours_per_day} hours</td>
                        <td>${saturdayOffList || 'None'}</td>
                        <td>${rule.yearly_holidays ?? 0}</td>
                        <td>${rule.tax ?? 0}</td>
                        <td>${rule.salary_above_tax ?? 0}</td>
                        <td>${rule.lunch_break}</td>
                        <td><a href="/creates-rules" class="text-warning fs-5" title="Edit">
                                <i class="mdi mdi-pencil"></i>
                            </a>
                            <!-- <a href="#" class="text-danger fs-5" title="Delete" data-id="${rule.id}" onclick="deleteRule(event)">
                                <i class="mdi mdi-delete"></i>
                            </a> -->
                        </td>
                    </tr>
                `;
                    });

                    $('#rules-Table-Body').html(tableRows);

                    if ($.fn.DataTable.isDataTable('#rules-Table')) {
                        $('#rules-Table').DataTable().clear().destroy();
                    }

                    $('#rules-Table').DataTable({
                        language: {
                            search: "",
                            searchPlaceholder: "Search"
                        }
                    });

                } else {
                    console.error('Failed to fetch rules:', responseData.message);
                }
            })
            .catch(error => {
                console.error('Error fetching rules:', error);
            });
    });

    // Deletion
    function deleteRule(event) {
        event.preventDefault();
        const ruleId = event.target.closest('a').getAttribute('data-id');

        Swal.fire({
            title: 'Are you sure?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'hr-btnbg',
                cancelButton: 'hr-btnbg',
            }
        }).then(result => {
            if (result.isConfirmed) {
                fetch(`/api/rules/${ruleId}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('token')}`,
                            'Content-Type': 'application/json',
                        }
                    })

                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            document.querySelector(`tr[data-id="${ruleId}"]`)?.remove();
                            Swal.fire('Deleted!', 'The rule has been deleted.', 'success');
                        } else {
                            Swal.fire('Error!', data.message || 'Failed to delete.', 'error');
                        }
                    })
                    .catch(err => {
                        Swal.fire('Error!', err.message || 'Something went wrong.', 'error');
                    });
            }
        });
    }
</script>

<?= $this->endSection() ?>
