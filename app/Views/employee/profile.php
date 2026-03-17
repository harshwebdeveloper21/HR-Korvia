<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<style>
    .lg-card-margin {
        margin-bottom: 10px;
    }

    @media (max-width: 767px) {
        .interviewsmbtn {
            font-size: 12px !important;
            padding: 8px !important;
            margin-top: 10px !important;
        }

        .ul-sm-fontsize {
            font-size: 11.2px !important;
        }

        .tab-content {
            overflow: hidden;
        }

        .tab-pane {
            overflow-x: auto;
        }
    }

    .capitalize-text {
        text-transform: capitalize;
    }
</style>
<link rel="stylesheet" href="<?= base_url(env('ImagePath') . 'assets/css/profile.css') ?>">
<div class="container py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="d-flex justify-content-end">
          
            <a href="#" id="editOverviewBtn"
                class="align-items-center btn btn-sm d-flex justify-content-center rounded"
                style="background-color: #E66136; color: #fff; padding: 10px; text-decoration: none;">
                <i class="mdi mdi-pencil-outline fs-6" style="color: #fff;"></i> Edit Profile
            </a>

        </div>


        <div class="col-md-4">
            <div class="card p-3 lg-card-margin">
                <div class="text-center">
                    <input type="file" id="profile_image_input" name="profile_image" accept="image/*" style="display: none;" />
                    <input type="hidden" name="id_change_image" id="id_change_image">
                    <label for="profile_image_input" style="cursor: pointer;">
                        <img id="profile_image"
                            src="<?= base_url(env('ImagePath') . 'upload/default-profile.jpg'); ?>"
                            alt="Profile Avatar"
                            class="rounded-circle"
                            width="130"
                            height="130"
                            title="Click to change profile image" />
                    </label>
                    <h5 class="mb-0 capitalize-text mt-2" id="user_info-firstname"></h5>
                </div>


                <hr>
                <div>
                    <p class="section-title">About</p>
                    <p><i class="mdi mdi-phone me-2"></i><span class="info-label">Phone:</span><span id="profile-phone"></span></p>
                    <p><i class="mdi mdi-email-outline me-2"></i><span class="info-label">Email:</span><span id="profile-email"></span></p>
                </div>

                <hr>
                <div>
                    <p class="section-title">Address</p>
                    <p><i class="mdi mdi-map-marker me-2"></i><span class="info-label">Address:</span><span id="address1" class="capitalize-text">390 Market Street</span></p>
                    <p><i class="mdi mdi-city me-2"></i><span class="info-label">City:</span><span id="city1" class="capitalize-text">San Francisco</span></p>
                    <p><i class="mdi mdi-numeric me-2"></i><span class="info-label">Postcode:</span><span id="postcode1" class="capitalize-text">94102</span></p>
                </div>
                <hr>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Tabs -->
            <ul class="nav nav-tabs ul-sm-fontsize" id="profileTabs">

                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#overview"><i class="fa fa-info-circle" aria-hidden="true"></i> Overview</a>
                </li>

                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#compnydetail"><i class="fa fa-address-card" aria-hidden="true"></i> Address & Contacts</a></li>

                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#compensation"><i class="fa fa-tasks" aria-hidden="true"></i> Job Details</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#emergency"><i class="fa fa-bank"> </i> Bank Details</a></li>

            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Overview Tab -->

                <div class="tab-pane fade show active" id="overview">
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="section-title mb-0">Overview</h6>
                                <a href="#" id="editOverview"
                                    class="d-flex align-items-center justify-content-center rounded btn btn-sm mt-3"
                                    style="background-color: #E66136; color: white;">
                                    <i class="mdi mdi-pencil-outline fs-7"></i> Edit Overview
                                </a>
                            </div>
                            <div class="card-body">
                                <table class="table mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="tdfont">
                                                <input type="hidden" name="users-id" id="users-id">
                                                <i class="mdi mdi-account-outline me-1 mdicon"></i> First Name
                                            </td>

                                            <td id="users-username" class="capitalize-text"></td>
                                        </tr>
                                        <tr>
                                            <td class="tdfont"><i class="mdi mdi-account-circle-outline me-1 mdicon"></i> Last Name</td>
                                            <td id="user_info-lastname" class="capitalize-text"></td>
                                        </tr>
                                        <tr>
                                            <td class="tdfont"><i class="mdi mdi-account-circle-outline me-1 mdicon"></i>Email</td>
                                            <td id="users-email"></td>
                                        </tr>
                                        <tr>
                                            <td class="tdfont"><i class="mdi mdi-gender-male-female me-1 mdicon"></i> Gender</td>
                                            <td id="user_info-gender" class="capitalize-text"></td>
                                        </tr>
                                        <tr>
                                            <td class="tdfont"><i class="mdi mdi-cake-variant-outline me-1 mdicon"></i> Date Of Birth</td>
                                            <td id="user_info-date_of_birth"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal -->
                <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form id="editForm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit Profile</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="mb-3">
                                        <input type="hidden" name="edit-id" id="edit-id">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="edit-firstname">
                                        <div class="text-danger" id="error-firstname"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="edit-lastname">
                                        <div class="text-danger" id="error-lastname"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" id="edit-email">
                                        <div class="text-danger" id="error-email"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Gender</label><br>
                                        <div class="d-flex ms-lg-4">
                                            <div class="form-check form-check-inline me-5">
                                                <input class="form-check-input" type="radio" name="gender" id="genderMale" value="male">
                                                <label class="form-check-label" for="genderMale">Male</label>
                                            </div>
                                            <div class="form-check form-check-inline me-5">
                                                <input class="form-check-input" type="radio" name="gender" id="genderFemale" value="female">
                                                <label class="form-check-label" for="genderFemale">Female</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="gender" id="genderOther" value="other">
                                                <label class="form-check-label" for="genderOther">Other</label>
                                            </div><br>
                                            <div class="text-danger" id="error-gender"></div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Date of Birth</label>
                                        <input type="date" class="form-control" id="edit-dob">
                                        <div class="text-danger" id="error-dob"></div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="submit" class="btn" style="background-color: #E66136; color: white;">Save Changes</button>
                                    <button type="button" class="btn" style="background-color: #E66136; color: white;" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="tab-pane fade" id="compnydetail">
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="section-title">Address & Contacts Information</h6>
                                <a href="#" id="editAddress"
                                    class="d-flex align-items-center justify-content-center rounded btn btn-sm mt-3"
                                    style="background-color: #E66136; color: white;">
                                    <i class="mdi mdi-pencil-outline fs-7"></i> Edit Address
                                </a>
                            </div>
                            <div class="card-body">
                                <table class="table mb-0">
                                    <tbody>
                                        <tr>
                                            <input type="hidden" name="user-id" id="user-id">
                                            <td class="tdfont">
                                                <i class="mdi mdi-account-outline me-1 mdicon"></i>Address1
                                            </td>

                                            <td id="user_info-address_1" class="capitalize-text"></td>
                                        </tr>
                                        <tr>
                                            <td class="tdfont"><i class="mdi mdi-account-circle-outline me-1 mdicon"></i>Address2</td>
                                            <td id="user_info-address_2" class="capitalize-text"></td>
                                        </tr>
                                        <tr>
                                            <td class="tdfont"><i class="mdi mdi-account-circle-outline me-1 mdicon"></i>Country</td>
                                            <td id="user_info-country_name" class="capitalize-text"></td>
                                        </tr>
                                        <tr>
                                            <td class="tdfont"><i class="mdi mdi-gender-male-female me-1 mdicon"></i>State</td>
                                            <td id="user_info-state_name" class="capitalize-text"></td>
                                        </tr>

                                        <tr>
                                            <td class="tdfont"> <i class="mdi mdi-earth me-1 mdicon"></i>City</td>
                                            <td id="user_info-city_name" class="capitalize-text"></td>
                                        </tr>
                                        <tr>
                                            <td class="tdfont"> <i class="mdi mdi-map-marker me-1 mdicon"></i>Postcode</td>
                                            <td id="user_info-postcode" class="capitalize-text"></td>
                                        </tr>
                                        <tr>
                                            <td class="tdfont"><i class="mdi mdi-domain me-1 mdicon"></i>Contact Number</td>
                                            <td id="user_info-contact_number" class="capitalize-text"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form id="editAddressForm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editAddressModalLabel">Edit Address & Contact Info</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body">

                                    <input type="hidden" id="edit-user-id">

                                    <div class="mb-3">
                                        <label class="form-label">Address 1</label>
                                        <input type="text" class="form-control" id="edit-address1" name="address_1">
                                        <div class="text-danger" id="error-address_1"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Address 2</label>
                                        <input type="text" class="form-control" id="edit-address2" name="address_2">
                                        <div class="text-danger" id="error-address_2"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Country</label>
                                        <select class="form-control" id="edit-country" name="country_id">
                                            <option value="">Select Country</option>
                                        </select>
                                        <div class="text-danger" id="error-country_id"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">State</label>
                                        <select class="form-control" id="edit-state" name="state_id">
                                            <option value="">Select State</option>
                                        </select>
                                        <div class="text-danger" id="error-state_id"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">City</label>
                                        <select class="form-control" id="edit-city" name="city_id">
                                            <option value="">Select City</option>
                                        </select>
                                        <div class="text-danger" id="error-city_id"></div>
                                    </div>


                                    <div class="mb-3">
                                        <label class="form-label">Postcode</label>
                                        <input type="text" class="form-control" id="edit-postcode" name="postcode">
                                        <div class="text-danger" id="error-postcode"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Contact Number</label>
                                        <input type="text" class="form-control" id="edit-contact" name="contact_number">
                                        <div class="text-danger" id="error-contact_number"></div>
                                    </div>

                                </div>

                                <div class="modal-footer">
                                    <button type="submit" class="btn" style="background-color: #E66136; color: white;">Save Changes</button>
                                    <button type="button" class="btn" style="background-color: #E66136; color: white;" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Compensation Tab -->
                <div class="tab-pane fade" id="compensation">
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="section-title">Job Details</h6>
                            <a href="#" id="openEditJobModal"
                                class="d-flex align-items-center justify-content-center rounded btn btn-sm"
                                style="background-color: #E66136; color: white;">
                                <i class="mdi mdi-pencil-outline fs-7"></i> Edit Job
                            </a>

                        </div>
                        <div class="card-body">
                            <table class="table mb-0">
                                <tbody>
                                    <tr>
                                        <td class="tdfont">
                                            <input type="hidden" name="user-lg" id="user-lg">
                                            <i class="mdi mdi-account-outline me-1 mdicon"></i>Employee ID
                                        </td>

                                        <td id="user_info-employee_id"></td>
                                    </tr>
                                    <tr>
                                        <td class="tdfont"><i class="mdi mdi-account-circle-outline me-1 mdicon"></i>Department</td>
                                        <td id="user_info-department_name" class="capitalize-text"></td>
                                    </tr>
                                    <tr>
                                        <td class="tdfont"><i class="mdi mdi-account-circle-outline me-1 mdicon"></i>Designation</td>
                                        <td id="user_info-designation_name" class="capitalize-text"></td>
                                    </tr>
                                    <tr>
                                        <td class="tdfont"><i class="mdi mdi-gender-male-female me-1 mdicon"></i>Joining Date</td>
                                        <td id="user_info-joining_date" class="capitalize-text"></td>
                                    </tr>

                                    <tr>
                                        <td class="tdfont"> <i class="mdi mdi-earth me-1 mdicon"></i>Working Location</td>
                                        <td id="user_info-working_location" class="capitalize-text"></td>
                                    </tr>
                                    <tr>
                                        <td class="tdfont"> <i class="mdi mdi-map-marker me-1 mdicon"></i>Postcode</td>
                                        <td id="user_info-postcode1" class="capitalize-text"></td>
                                    </tr>
                                    <tr>
                                        <td class="tdfont"><i class="mdi mdi-domain me-1 mdicon"></i>Salary</td>
                                        <td id="user_info-salary" class="capitalize-text"></td>
                                    </tr>
                                    <tr>
                                        <td class="tdfont"><i class="mdi mdi-domain me-1 mdicon"></i>Role</td>
                                        <td id="user_info-role" class="capitalize-text"></td>
                                    </tr>


                                </tbody>
                            </table>



                        </div>
                    </div>
                </div>

                <div class="modal fade" id="editJobModal" tabindex="-1" aria-labelledby="editJobModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form id="jobForm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Job Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <input type="hidden" name="user_id" id="job-user-id">

                                    <div class="mb-3">
                                        <label class="form-label">Employee ID</label>
                                        <input type="text" class="form-control" name="employee_id" id="job-employee-id">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Department</label>
                                        <select class="form-control" name="department_id" id="job-department">
                                            <option value="">Select Department</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Designation</label>
                                        <select class="form-control" name="designation_id" id="job-designation">
                                            <option value="">Select Designation</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Joining Date</label>
                                        <input type="date" class="form-control" name="joining_date" id="job-joining-date">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Working Location</label>
                                        <select class="form-control" name="working_location" id="job-working-location">
                                            <option value="">Select Working Location</option>
                                            <option value="Remote">Remote</option>
                                            <option value="On-Site">On-Site</option>
                                        </select>
                                        <!-- <input type="text" class="form-control" name="working_location" id="job-working-location"> -->
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Postcode</label>
                                        <input type="text" class="form-control" name="postcode" id="job-postcode">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Salary</label>
                                        <input type="number" class="form-control" name="salary" id="job-salary">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Role</label>
                                        <select class="form-control" name="role" id="job-role">
                                            <option value="">Select Role</option>
                                            <option value="admin">Admin</option>
                                            <option value="employee">Employee</option>
                                            <option value="hr">Hr</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="submit" class="btn" style="background-color: #E66136; color: white;">Save Changes</button>
                                    <button type="button" class="btn" style="background-color: #E66136; color: white;" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Other Tabs Placeholder -->
                <div class="tab-pane fade" id="emergency">
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="section-title">Bank Details</h6>
                            <a href="#" id="editBankBtn" class="btn btn-sm rounded" style="background-color: #E66136; color: white;">
                                <i class="mdi mdi-pencil-outline"></i> Edit Bank Details
                            </a>
                        </div>
                        <div class="card-body">
                            <table class="table mb-0">
                                <tbody>
                                    <tr>
                                        <input type="hidden" name="user-lg-id" id="user-lg-id">
                                        <td class="tdfont"><i class="mdi mdi-domain me-1 mdicon"></i>Bank Name</td>
                                        <td id="bank_name" class="capitalize-text"></td>
                                    </tr>
                                    <tr>
                                        <td class="tdfont"><i class="mdi mdi-map-marker me-1 mdicon"></i>Account Number</td>
                                        <td id="acc_number" class="capitalize-text"></td>
                                    </tr>
                                    <tr>
                                        <td class="tdfont"><i class="mdi mdi-account-outline me-1 mdicon"></i>Account Holder Name</td>
                                        <td id="acc_in_name" class="capitalize-text"></td>
                                    </tr>
                                    <tr>
                                        <td class="tdfont"><i class="mdi mdi-phone me-1 mdicon"></i>Branch Name</td>
                                        <td id="branch_name" class="capitalize-text"></td>
                                    </tr>
                                    <tr>
                                        <td class="tdfont"><i class="mdi mdi-email-outline me-1 mdicon"></i>Branch Code</td>
                                        <td id="branch_code" class="capitalize-text"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="editBankModal" tabindex="-1" aria-labelledby="editBankModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form id="editBankForm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Bank Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    <input type="hidden" id="bank-user-id" name="user_id">

                                    <div class="mb-3">
                                        <label class="form-label">Bank Name</label>
                                        <input type="text" class="form-control" id="bank-name" name="bank_name">
                                        <div class="text-danger" id="error-bank_name"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Account Number</label>
                                        <input type="text" class="form-control" id="account-number" name="acc_number">
                                        <div class="text-danger" id="error-acc_number"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Account Holder Name</label>
                                        <input type="text" class="form-control" id="account-in-name" name="acc_in_name">
                                        <div class="text-danger" id="error-acc_in_name"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Branch Name</label>
                                        <input type="text" class="form-control" id="branch-name" name="branch_name">
                                        <div class="text-danger" id="error-branch_name"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Branch Code</label>
                                        <input type="text" class="form-control" id="branch-code" name="branch_code">
                                        <div class="text-danger" id="error-branch_code"></div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="submit" class="btn" style="background-color: #E66136; color: white;">Save</button>
                                    <button type="button" class="btn" style="background-color: #E66136; color: white;" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="tab-pane fade" id="performance">
                    <p class="mt-3">Performance history loading...</p>
                </div>
                <div class="tab-pane fade" id="editprofile">
                    <h4 class="card-title mb-4">Edit Profile</h4>

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="task-list-box" id="landing-task">

                            </div><!-- end -->
                        </div><!-- end col -->
                    </div><!-- end row -->
                </div>
            </div>
            <div class="text-end mt-4">
                <a href="<?= base_url('/empview') ?>" class="btn hr-btnbg interviewsmbtn">
                    <i class="mdi mdi-arrow-left me-1 iconfontsize"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>


<!-- Error Message Container -->
<div id="error-message" class="alert alert-danger d-none"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editOverviewBtn = document.getElementById('editOverviewBtn');

        editOverviewBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const profileEmployeeId = editOverviewBtn.getAttribute('data-id'); // get the profile id
            window.location.href = `/employee/${profileEmployeeId}`; // redirect to edit page
        });
    });
</script>

<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token'); // JWT token
        const userId = '<?= $id; ?>'; // Employee ID passed from the controller

        function showError(message) {
            $('#error-message').text(message).removeClass('d-none'); // Show error message
        }

        if (userId) {
            $.ajax({
                url: `<?= site_url('employee/details'); ?>/${userId}`,
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        $('#error-message').addClass('d-none'); // Hide error if successful

                        const user = data.data;
                        console.log(user);

                        function setText(selector, value) {
                            $(selector).text(value ? value : 'N/A');
                        }

                        setText('#users-username', user.username);
                        setText('#users-id', user.user_id);

                        setText('#users-email', user.email);
                        setText('#profile-email', user.email);
                        setText('#user_info-firstname', user.firstname + ' ' + (user.lastname || ''));
                        setText('#user_info-gender', user.gender);
                        setText('#user_info-lastname', user.lastname);
                        setText('#user_info-date_of_birth', user.date_of_birth);
                        setText('#user_info-address_1', user.address_1);
                        setText('#address1', user.address_1);
                        setText('#user_info-address_2', user.address_2);
                        setText('#user_info-country_name', user.country_name);
                        setText('#user_info-state_name', user.state_name);
                        setText('#user_info-city_name', user.city_name);
                        setText('#city1', user.city_name);
                        setText('#user_info-postcode', user.postcode);
                        setText('#user_info-postcode1', user.postcode);
                        setText('#postcode1', user.postcode);
                        setText('#user_info-contact_number', user.contact_number);
                        setText('#number1', user.contact_number);
                        setText('#profile-phone', user.contact_number);
                        setText('#user_info-designation_name', user.designation_name);
                        setText('#user_info-department_name', user.department_name);
                        setText('#user_info-employee_id', 'EMP#' + user.employee_id);
                        setText('#user_info-joining_date', user.joining_date);
                        setText('#user_info-working_location', user.working_location);
                        setText('#user_info-role', user.role);
                        setText('#user_info-salary', user.salary);
                        setText('#acc_number', user.acc_number);
                        setText('#bank_name', user.bank_name);
                        setText('#ifsc_code', user.ifsc_code);
                        setText('#acc_in_name', user.acc_in_name);
                        setText('#branch_name', user.branch_name);
                        setText('#branch_code', user.branch_code);
                        // setText('#id_change_image', user.id);
                        $('#id_change_image').val(user.user_id);
                        $('#user-id').val(user.user_id);
                        $('#user-lg-id').val(user.user_id);
                        $('#user-lg').val(user.user_id);
                        $('#editOverviewBtn').attr('data-id', user.user_id);



                        let profileImage = user.profile_image ? user.profile_image : '<?= base_url(env('ImagePath') . "upload/default-profile.jpg"); ?>';
                        $('#profile_image').attr('src', profileImage);
                    } else {
                        showError(data.message);
                    }
                },
                error: function(xhr, status, error) {
                    showError('Error fetching staff details: ' + error);
                }
            });
        } else {
            showError('No user ID provided.');
        }
    });
    // document.getElementById('profile_image_input').addEventListener('change', function(event) {
    //     const file = event.target.files[0];
    //     if (file) {
    //         const reader = new FileReader();
    //         reader.onload = function(e) {
    //             document.getElementById('profile_image').src = e.target.result;
    //         };
    //         reader.readAsDataURL(file);
    //     }
    // });
    document.getElementById('profile_image_input').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const id_change_image = document.getElementById('id_change_image').value;
        if (file) {
            // Preview the image
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profile_image').src = e.target.result;
            };
            reader.readAsDataURL(file);

            // Upload the image using FormData
            const formData = new FormData();
            formData.append('profile_image', file);
            formData.append('id', id_change_image); // replace USER_ID with the logged-in user ID (JS variable or hardcoded)

            fetch('<?= base_url('/api/change_image') ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(response => {
                    if (response.status === 'success') {
                        Swal.fire('Success', response.message, 'success');
                    } else {
                        Swal.fire('Error', response.message || 'Something went wrong', 'error');
                    }
                })
                .catch(error => {
                    console.error('Upload error:', error);
                    Swal.fire('Error', 'Image upload failed', 'error');
                });
        }
    });
    $(document).ready(function() {
        // Open modal and fill form
        $('#editOverview').on('click', function() {
            $('#edit-id').val($('#users-id').text().trim());
            $('#edit-firstname').val($('#users-username').text().trim());
            $('#edit-lastname').val($('#user_info-lastname').text().trim());
            $('#edit-email').val($('#users-email').text().trim());
            $('#edit-dob').val($('#user_info-date_of_birth').text().trim());

            // Set gender radio button (case-insensitive match)
            const gender = $('#user_info-gender').text().trim().toLowerCase();
            $('input[name="gender"]').prop('checked', false); // Clear any selected
            $('input[name="gender"]').each(function() {
                if ($(this).val().toLowerCase() === gender) {
                    $(this).prop('checked', true);
                }
            });

            $('#editModal').modal('show');
        });

        // Submit form and update table
        $('#editForm').on('submit', function(e) {
            e.preventDefault();

            // Clear previous errors
            $('.text-danger').text('');

            // Collect form data
            const data = {
                id: $('#edit-id').val(),
                firstname: $('#edit-firstname').val(),
                lastname: $('#edit-lastname').val(),
                email: $('#edit-email').val(),
                gender: $('input[name="gender"]:checked').val(),
                dob: $('#edit-dob').val()
            };

            $.ajax({
                url: '/api/save_overview',
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.status === 'success') {
                        $('#users-username').text(data.firstname);
                        $('#user_info-lastname').text(data.lastname);
                        $('#users-email').text(data.email);
                        $('#user_info-gender').text(data.gender);
                        $('#user_info-date_of_birth').text(data.dob);

                        $('#editModal').modal('hide');
                        Swal.fire('Success', response.message, 'success');
                    } else if (response.status === 'error' && response.errors) {
                        $.each(response.errors, function(field, message) {
                            $('#error-' + field).text(message);
                        });
                    } else {
                        Swal.fire(response.message || 'Something went wrong!');
                        // alert(response.message || 'Something went wrong!');
                    }
                },
                error: function(xhr) {
                    alert('Server error: ' + xhr.responseText);
                }
            });
        });

        // Helper function to populate select options and set selected value
        function populateSelectOptions(selectId, dataList, selectedId) {
            const select = $(selectId);
            select.empty().append('<option value="">Select</option>');

            // Determine label key based on selectId
            let labelKey = 'name';
            if (selectId.includes('country')) labelKey = 'country_name';
            else if (selectId.includes('state')) labelKey = 'state_name';
            else if (selectId.includes('city')) labelKey = 'city_name';
            else if (selectId.includes('designation')) labelKey = 'designation_name';
            else if (selectId.includes('department')) labelKey = 'department_name';

            dataList.forEach(item => {
                const isSelected = item.id == selectedId ? 'selected' : '';
                select.append(`<option value="${item.id}" ${isSelected}>${item[labelKey]}</option>`);
            });
        }


        // On Edit Address button click
        $('#editAddress').on('click', function() {
            const user_id = $('#user-id').val(); // make sure this hidden input exists and has value

            $.ajax({
                url: '/api/get_user_address_data/' + user_id,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const user = response.user;

                        $('#edit-user-id').val(user.user_id);
                        $('#edit-address1').val(user.address_1);
                        $('#edit-address2').val(user.address_2);
                        $('#edit-postcode').val(user.postcode);
                        $('#edit-contact').val(user.contact_number);

                        // Populate select fields
                        populateSelectOptions('#edit-country', response.country, user.country_id);
                        populateSelectOptions('#edit-state', response.state, user.state_id);
                        populateSelectOptions('#edit-city', response.city, user.city_id);

                        $('#editAddressModal').modal('show');
                    } else {
                        alert('User not found');
                    }
                },
                error: function() {
                    alert('Error fetching address data.');
                }
            });
        });
        $('#editAddressForm').on('submit', function(e) {
            e.preventDefault();

            const formData = {
                user_id: $('#edit-user-id').val(),
                address_1: $('#edit-address1').val(),
                address_2: $('#edit-address2').val(),
                country_id: $('#edit-country').val(),
                state_id: $('#edit-state').val(),
                city_id: $('#edit-city').val(),
                postcode: $('#edit-postcode').val(),
                contact_number: $('#edit-contact').val()
            };

            // Clear previous errors
            $('.text-danger').text('');

            $.ajax({
                url: '/api/update_user_address', // Define this route
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#editAddressModal').modal('hide');
                        Swal.fire('Success', response.message, 'success');
                        location.reload();
                    } else if (response.errors) {
                        // Show validation errors
                        $.each(response.errors, function(field, msg) {
                            $('#error-' + field).text(msg);
                        });
                    } else {
                        Swal.fire(response.message || 'Something went wrong!');
                    }
                },
                error: function() {
                    alert('Server error while updating address.');
                }
            });
        });

        $('#editBankBtn').on('click', function() {
            const user_id = $('#user-lg-id').val(); // Get from hidden input

            $.ajax({
                url: '/api/get_user_bank_data/' + user_id,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const bank = response.data;

                        $('#bank-user-id').val(bank.user_id);
                        $('#bank-name').val(bank.bank_name);
                        $('#account-number').val(bank.acc_number);
                        $('#account-in-name').val(bank.acc_in_name);
                        $('#branch-name').val(bank.branch_name);
                        $('#branch-code').val(bank.branch_code);

                        $('#editBankModal').modal('show');
                    } else {
                        alert('Bank data not found');
                    }
                },
                error: function() {
                    alert('Server error fetching bank details');
                }
            });
        });
        $('#editBankForm').on('submit', function(e) {
            e.preventDefault();

            const formData = $(this).serialize();

            $('.text-danger').text('');

            $.ajax({
                url: '/api/update_user_bank_data',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#editBankModal').modal('hide');
                        Swal.fire('Success', response.message, 'success');
                        location.reload(); // or refresh bank section
                    } else if (response.errors) {
                        $.each(response.errors, function(field, msg) {
                            $('#error-' + field).text(msg);
                        });
                    } else {
                        Swal.fire(response.message || 'Something went wrong!');
                    }
                },
                error: function() {
                    alert('Error submitting bank details');
                }
            });
        });

        function populateSelectOptionsjob(selectId, dataList, selectedId) {
            const select = $(selectId);
            select.empty().append('<option value="">Select</option>');

            let labelKey = 'name';
            if (selectId.includes('department')) labelKey = 'department_name';
            else if (selectId.includes('designation')) labelKey = 'designation_name';

            dataList.forEach(item => {
                const isSelected = item.id == selectedId ? 'selected' : '';
                select.append(`<option value="${item.id}" ${isSelected}>${item[labelKey]}</option>`);
            });
        }

        // Call modal on button click
        $('#openEditJobModal').on('click', function() {
            const user_id = $('#user-lg').val(); // hidden input

            $.ajax({
                url: '/api/get_user_address_data/' + user_id,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const user = response.user;
                        const departments = response.department || [];
                        const designations = response.designation || [];

                        $('#job-user-id').val(user.user_id);
                        // $('#job-employee-id').val(user.employee_id);
                        $('#job-joining-date').val(user.joining_date);
                        $('#job-working-location').val(user.working_location);
                        $('#job-postcode').val(user.postcode);
                        $('#job-salary').val(user.salary);
                        $('#job-role').val(user.role);
                        if (user.employee_id === '0') {
                            user.employee_id = 'EMP#' + Math.floor(Math.random() * 90000 + 10000);
                        }
                        $('#job-employee-id').val('EMP#' +user.employee_id);

                        populateSelectOptionsjob('#job-department', departments, user.department_id);
                        populateSelectOptionsjob('#job-designation', designations, user.designation_id);

                        $('#editJobModal').modal('show');
                    } else {
                        alert('User not found');
                    }
                },
                error: function() {
                    alert('Error fetching address data.');
                }
            });
        });

        $('#jobForm').on('submit', function(e) {
            e.preventDefault();

            const formData = {
                user_id: $('#job-user-id').val(),
                employee_id: $('#job-employee-id').val(),
                department_id: $('#job-department').val(),
                designation_id: $('#job-designation').val(),
                joining_date: $('#job-joining-date').val(),
                working_location: $('#job-working-location').val(),
                postcode: $('#job-postcode').val(),
                salary: $('#job-salary').val(),
                role: $('#job-role').val()
            };

            $.ajax({
                url: '/api/update_user_job_data', // You need this API in your controller
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Success', response.message, 'success');
                        location.reload();
                        $('#editJobModal').modal('hide');
                        // Optional: reload data on page
                    } else {
                        if (response.errors) {
                            // Show validation errors
                            console.error(response.errors);
                            alert('Please fill all required fields correctly.');
                        } else {
                            Swal.fire(response.message || 'Something went wrong!');;
                        }
                    }
                },
                error: function() {
                    alert('An error occurred while updating job data.');
                }
            });
        });

    });
</script>


<?= $this->endSection(); ?>