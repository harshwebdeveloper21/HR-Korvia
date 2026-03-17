<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>

<link rel="stylesheet" href="<?= base_url('assets/css/calender.css?hi') ?>">

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <!-- Header with User Filter, Leave Type, Date Filter, and Add Leave Button -->
                <div class="header-controls mb-3">
                    <h4 class="card-title">Leave Calendar</h4>
                    <!-- Right Side: Filters and Add Leave Button -->
                    <div class="d-flex align-items-center gap-3">
                        <a href="/addleave" class="btn hr-btnbg w-100">
                            <i class="mdi mdi-plus"></i> Add Leave
                        </a>
                    </div>
                </div>
                <div class="row">
                    <!-- Left Side: Team Member List and Search Input -->
                    <div class="col-md-4">
                        <!-- Team Members List -->
                        <div class="team-members">
                            <h5 class="mb-2">Employees</h5>
                            <!-- <hr> -->
                            <hr style="border-top: 1px solid #c9c4c1; width: 100%;">
                            <div class="scrollable-container">
                                <ul class="list-group" id="employee-list">
                                    <!-- Employees will be displayed here dynamically -->
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: Full Calendar -->
                    <div class="col-md-8">
                        <div class="team-members">
                            <div id="calendar" class="full-calendar"></div>
                        </div>
                        <!-- Leave Types Legend Below the Calendar -->
                        <div class="leave-legend mt-4 d-flex">

                            <div class="legend-item mx-2">
                                <span class="legend-box sick-leave"></span>
                                <span class="legend-label">Sick Leave</span>
                            </div>
                            <div class="legend-item mx-2">
                                <span class="legend-box paid-leave"></span>
                                <span class="legend-label">Paid Leave</span>
                            </div>
                            <div class="legend-item mx-2">
                                <span class="legend-box vacation-leave"></span>
                                <span class="legend-label">Vacation Leave</span>
                            </div>
                            <div class="legend-item mx-2">
                                <span class="legend-box casual-leave"></span>
                                <span class="legend-label">Casual Leave</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Leave Details & Approval Modal -->
<div class="modal fade" id="leaveModal" tabindex="-1" aria-labelledby="leaveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leaveModalLabel">Leave Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="leaveId">
                <p><strong>Employee:</strong> <span id="leaveUser"></span></p>
                <p><strong>Reason:</strong> <span id="leaveReason"></span></p>

                <div id="statusContainer">
                    <label for="leaveStatus">Status:</label>
                    <select id="leaveStatus" class="form-control">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                    <span id="leaveStatusText" style="display: none;"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn  hr-btnbg" data-bs-dismiss="modal">Close</button>
                <button type="button" id="updateLeaveStatus" class="btn  hr-btnbg" style="display: none;">Update Status</button>
            </div>
        </div>
    </div>
</div>
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script>
    let calendar; // Define globally
    let userRole = null; // Store user role globally

    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');

        calendar = new FullCalendar.Calendar(calendarEl, { // ✅ Use global variable
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            locale: 'en',
            events: [], // Events will be dynamically added
            eventClick: function(info) {
                $('#leaveModal').modal('show');
                $('#leaveId').val(info.event.id);
                $('#leaveUser').text(info.event.extendedProps.user);
                $('#leaveReason').text(info.event.extendedProps.description);
                $('#leaveStatus').val(info.event.extendedProps.status);
                $('#updateLeaveStatus').data('event', info.event);
                
                // Show/hide status controls based on user role
                if (userRole === 'employee') {
                    // Hide dropdown and button, show read-only text
                    $('#leaveStatus').hide();
                    $('#leaveStatusText').text(info.event.extendedProps.status).show();
                    $('#updateLeaveStatus').hide();
                } else {
                    // Show dropdown and button for admin/hr
                    $('#leaveStatus').show();
                    $('#leaveStatusText').hide();
                    $('#updateLeaveStatus').show();
                }
            },
            eventDidMount: function(info) {
                tippy(info.el, {
                    content: `<strong>${info.event.title}</strong><br>${info.event.extendedProps.description}`,
                    placement: 'top',
                    animation: 'fade',
                    allowHTML: true
                });
            }
        });

        calendar.render(); // ✅ Ensure calendar is initialized before fetching leave data

        fetchLeaveData(); // ✅ Fetch leave data after calendar is initialized
    });

    function fetchLeaveData(employeeId) {
        const token = localStorage.getItem('token');

        $.ajax({
            url: `/api/leave`, // Fetch leave records
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`
            },
            success: function(response) {
                if (response.status === 'success') {
                    console.log("Leave Data:", response.data);

                    const events = response.data.flatMap(leave =>
                        leave.leaves.map(leaveItem => {
                            let startDate = leaveItem.start_date || new Date().toISOString();
                            let endDate = leaveItem.end_date || startDate;
                            endDate = `${endDate}T23:59:59`;

                            let backgroundColor;

                            switch (leaveItem.status) {
                                case 'approved':
                                    backgroundColor = '#28a745'; // Green
                                    break;
                                case 'pending':
                                    backgroundColor = '#ffca28'; // Yellow
                                    break;
                                case 'rejected':
                                    backgroundColor = '#6c757d'; // Gray
                                    break;
                                default:
                                    backgroundColor = '#dc3545';
                                    break;
                            }

                            switch (leaveItem.leave_type) {
                                case 'Sick Leave':
                                    backgroundColor = '#28a745';
                                    break;
                                case 'Paid Leave':
                                    backgroundColor = '#ff6347';
                                    break;
                                case 'Vacation Leave':
                                    backgroundColor = '#d3c75e';
                                    break;
                                case 'Casual Leave':
                                    backgroundColor = '#17a2b8';
                                    break;
                            }

                            return {
                                id: leaveItem.id ? leaveItem.id.toString() : `leave-${Math.random()}`,
                                title: `${leave.firstname} - ${leaveItem.status.charAt(0).toUpperCase() + leaveItem.status.slice(1)}`,
                                start: startDate,
                                end: endDate,
                                backgroundColor: backgroundColor,
                                extendedProps: {
                                    user: `${leave.firstname}`,
                                    description: leaveItem.reason || "No reason provided",
                                    status: leaveItem.status
                                }
                            };
                        })
                    );

                    calendar.addEventSource(events); // ✅ Add events to FullCalendar
                    console.log("Generated Events:", events);
                } else {
                    console.error('Failed to fetch leave data:', response.message);
                }
            },
            error: function(error) {
                console.error('Error fetching leaves:', error);
            }
        });
    }

    fetchEmployees()

    function fetchEmployees() {
        const token = localStorage.getItem('token');
        $.ajax({
            url: '/api/leave',
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`
            },
            success: function(response) {
                console.log('Employee API Response:', response); // ✅ Debugging

                if (response.status === 'success') {
                    let employees = response.data;
                    userRole = response.role; // 👈 store user role globally
                    let employeeList = $('#employee-list');
                    employeeList.empty();


                    employees.forEach(function(employee) {
                        let employeeItem = `
                            <li class="list-group-item">
                                <div class="team-member d-flex align-items-center">
                                    <img src="<?= base_url('upload/') ?>${employee.profile_image || 'default-profile.jpg'}" 
                                        alt="${employee.firstname}" class="profile-pic rounded-circle mx-3" width="40" height="40">
                                    <div>
                                        <strong>${employee.firstname}</strong>
                                    </div>
                                    
                                </div>
                                <hr class="my-1" style="border-top: 1px solid #c9c4c1; width: 100%;">
                            </li>
                        `;

                        employeeList.append(employeeItem);
                    });

                } else {
                    console.log('Error fetching employees:', response.message);
                }
            },
            error: function() {
                console.log('An error occurred while fetching employees.');
            }
        });
    }

    $('#updateLeaveStatus').click(function() {
        const token = localStorage.getItem('token');
        const leaveId = $('#leaveId').val(); // Get leave ID

        if (!leaveId) {
            Swal.fire({
                icon: 'error',
                title: 'Missing Leave ID',
                text: 'Leave ID is required to update the leave status.',
                confirmButtonText: 'OK',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'hr-btnbg',

                }
            });
            return;
        }

        const newStatus = $('#leaveStatus').val();
        const event = $(this).data('event'); // Get event reference

        $.ajax({
            url: `/api/leave/update/${leaveId}`, // ✅ Ensure URL contains leaveId
            method: 'POST',
            data: JSON.stringify({
                status: newStatus
            }), // Send JSON data
            contentType: "application/json",
            headers: {
                'Authorization': `Bearer ${token}`
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: response.message,
                        text: 'The leave status has been successfully updated.',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'hr-btnbg',

                        }
                    }).then(() => {
                        location.reload(); // Reload page after clicking OK
                    });

                    // Update event color based on status
                    event.setProp('backgroundColor', newStatus === 'approved' ? '#28a745' : '#dc3545');
                    event.setExtendedProp('status', newStatus);

                    $('#leaveModal').modal('hide');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to update leave status',
                        text: response.message || 'There was an issue updating the leave status. Please try again.',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'hr-btnbg',

                        }
                    });
                }
            },
            error: function(xhr) {
                console.error('Error updating leave status:', xhr);

                let errorMessage = 'There was an error. Check console for details.';

                if (xhr.responseJSON && xhr.responseJSON.messages && xhr.responseJSON.messages.error) {
                    errorMessage = xhr.responseJSON.messages.error; // Get API error message
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error updating leave status',
                    text: errorMessage,
                    confirmButtonText: 'OK',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'hr-btnbg',

                    }
                });
            }
        });
    });
</script>

<?= $this->endSection(); ?>