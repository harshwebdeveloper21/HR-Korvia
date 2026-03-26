<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-Step Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <form id="multiStepForm" method="post">
        <!-- Step 1 -->
        <div class="form-step" id="step-1">
            <h3>Personal Information</h3>
            <label for="firstname">First Name</label>
            <input type="text" name="firstname" id="firstname">
            <label for="lastname">Last Name</label>
            <input type="text" name="lastname" id="lastname">
            <label for="email">Email</label>
            <input type="email" name="email" id="email">
            <button type="button" class="next-step">Next</button>
        </div>

        <!-- Step 2 -->
        <div class="form-step" id="step-2" style="display: none;">
            <h3>Contact Information</h3>
            <label for="contact_number">Contact Number</label>
            <input type="text" name="contact_number" id="contact_number">
            <label for="address_1">Address 1</label>
            <input type="text" name="address_1" id="address_1">
            <label for="address_2">Address 2</label>
            <input type="text" name="address_2" id="address_2">
            <button type="button" class="prev-step">Previous</button>
            <button type="button" class="next-step">Next</button>
        </div>

        <!-- Step 3 -->
        <div class="form-step" id="step-3" style="display: none;">
            <h3>Professional Information</h3>
            <label for="employee_id">Employee ID</label>
            <input type="text" name="employee_id" id="employee_id">
            <label for="designation_id">Designation</label>
            <input type="text" name="designation_id" id="designation_id">
            <label for="department_id">Department</label>
            <input type="text" name="department_id" id="department_id">
            <button type="button" class="prev-step">Previous</button>
            <button type="submit" class="submit-form">Submit</button>
        </div>
    </form>

    <script>
        $(document).ready(function() {
            var currentStep = 1;
            
            // Show next step
            $('.next-step').on('click', function() {
                var stepId = '#step-' + currentStep;
                $(stepId).hide();
                currentStep++;
                var nextStepId = '#step-' + currentStep;
                $(nextStepId).show();
            });

            // Show previous step
            $('.prev-step').on('click', function() {
                var stepId = '#step-' + currentStep;
                $(stepId).hide();
                currentStep--;
                var prevStepId = '#step-' + currentStep;
                $(prevStepId).show();
            });

            // Submit form using AJAX
            $('#multiStepForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                
                $.ajax({
                    url: '<?= site_url('userinfo/save'); ?>', // Adjust the controller URL
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Form submitted successfully!',
                            confirmButtonColor: '#3085d6'
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error occurred during submission.',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
