$(document).ready(function () {
    function updateProgress(step) {
        const totalSteps = 4;
        const progress = (step / totalSteps) * 100;

        $('#progress-bar')
            .css('width', `${progress}%`)
            .attr('aria-valuenow', progress)
    }
    //update progressbase
    updateProgress(currentStep)

    function showStep(step) {
        $(".step").removeClass("active");
        $(`#step${step}`).addClass("active");
    }

    // Handle Step 1: Unzip Files
    // $("#unzipBtn").click(function () {
    //     $("#loaderStep1").show();
    //     $("#alertStep1").addClass("d-none");
    //     $("#successAlertStep1").addClass("d-none");
    //     fetch('installer-config/unzip.php')
    //         .then(response => response.text())
    //         .then(data => {
    //             $("#loaderStep1").hide();
    //             if (data === 'success') {
    //                 $("#successAlertStep1").removeClass("d-none").text("File unzipped successfully.");
    //                 setTimeout(() => {
    //                     showStep(++currentStep); // Move to Step 2
    //                     updateProgress(currentStep)
    //                 }, 500)
    //             } else {
    //                 $("#loaderStep1").hide();
    //                 $("#alertStep1").removeClass("d-none").text(data);
    //             }
    //         })
    //         .catch(error => {
    //             $("#loaderStep1").hide();
    //             $("#alertStep1").removeClass("d-none").text("An unexpected error occurred.");
    //             console.error('Error:', error);
    //         });
    // });

    // $("#unzipBtn").click(function () {
    //     $("#loaderStep1").show();
    //     $("#alertStep1").addClass("d-none");
    //     $("#successAlertStep1").addClass("d-none");
    
    //     $("#progressBarContainer").show();
    
    //     let progressBar = $('#progressBar');
    
    //     fetch('installer-config/unzip.php')
    //         .then(response => {
    //             const reader = response.body.getReader();
    //             const decoder = new TextDecoder();
    //             let done = false;
    //             let receivedLength = 0;
    //             let chunks = [];
    
    //             reader.read().then(function processText({ done, value }) {
    //                 if (done) {
    //                     return; // When done, we stop reading
    //                 }
    
    //                 // Decode and collect the chunk
    //                 chunks.push(value);
    //                 receivedLength += value.length;
    //                 let chunkText = decoder.decode(value, { stream: true });
    
    //                 // Try to parse the chunk as JSON and update progress
    //                 try {
    //                     let jsonData = JSON.parse(chunkText);
    //                     if (jsonData.progress) {
    //                         let progress = jsonData.progress;
    //                         progressBar.css("width", progress + "%");
    //                         progressBar.attr("aria-valuenow", progress);
    //                         progressBar.text(progress + "%");
    
    //                         // If progress is 100%, show success message
    //                         if (progress === 100) {
    //                             $("#loaderStep1").hide();
    //                             $("#successAlertStep1").removeClass("d-none").text("File unzipped successfully.");
    //                             setTimeout(() => {
    //                                 showStep(++currentStep);
    //                                 updateProgress(currentStep);
    //                             }, 500);
    //                         }
    //                     }
    //                 } catch (error) {
    //                     console.error('Error parsing JSON:', error);
    //                 }
    
    //                 // Continue reading more data
    //                 reader.read().then(processText);
    //             });
    //         })
    //         .catch(error => {
    //             $("#loaderStep1").hide();
    //             $("#alertStep1").removeClass("d-none").text("An unexpected error occurred.");
    //             console.error('Error:', error);
    //         });
    // });
    
    $("#unzipBtn").click(function () {
        $("#loaderStep1").show();
        $("#alertStep1").addClass("d-none");
        $("#successAlertStep1").addClass("d-none");
    
        $("#progressBarContainer").show();
        let progressBar = $('#progressBar');
        let unzipCompleted = false; // Flag to ensure next step is triggered only once
    
        fetch('installer-config/unzip.php')
            .then(response => {
                const reader = response.body.getReader();
                const decoder = new TextDecoder();
    
                const processText = ({ done, value }) => {
                    if (done) return; // Stop reading when done
    
                    let chunkText = decoder.decode(value, { stream: true });
    
                    try {
                        let jsonData = JSON.parse(chunkText); // Parse each chunk
                        if (jsonData.progress) {
                            let progress = jsonData.progress;
                            progressBar.css("width", progress + "%");
                            progressBar.attr("aria-valuenow", progress);
                            progressBar.text(progress + "%");
    
                            if (progress === 100 && !unzipCompleted) {
                                unzipCompleted = true; // Ensure it's triggered once
                                $("#loaderStep1").hide();
                                $("#successAlertStep1").removeClass("d-none").text("File unzipped successfully.");
                                setTimeout(() => {
                                    showStep(++currentStep);
                                    updateProgress(currentStep);
                                }, 500);
                            }
                        }
                    } catch (error) {
                        console.error('Error parsing JSON:', error);
                    }
    
                    reader.read().then(processText); // Continue reading
                };
    
                reader.read().then(processText);
            })
            .catch(error => {
                $("#loaderStep1").hide();
                $("#alertStep1").removeClass("d-none").text("An unexpected error occurred.");
                console.error('Error:', error);
            });
    });
    
    
    

    // Handle Step 3: Import Database
    $("#importDbBtn").click(function () {
        $("#loaderStep3").show();
        $("#alertStep3").addClass("d-none");
        $("#successAlertStep3").addClass("d-none");
        $.ajax({
            url: 'installer-config/import_db.php',
            type: 'POST',
            processData: false,
            contentType: false,
            success: function (response) {
                $("#loaderStep3").hide();
             
                try {
                    const res = JSON.parse(response);

                    if (res.status === 'success') {
                        $("#successAlertStep3").removeClass("d-none").text(res.message);
                        setTimeout(() => {
                            showStep(++currentStep); // Move to Step 4
                            updateProgress(currentStep)
                        }, 500)
                    } else {
                        $("#loaderStep3").hide();
                        $("#alertStep3").removeClass("d-none").text(res.message);
                    }
                } catch (e) {

                    console.error('Error parsing response:', e);
                    $("#loaderStep3").hide();
                    $("#alertStep3").removeClass("d-none").text("Invalid response from server.");
                }
            },
            error: function () {
                $("#loaderStep3").hide();
                $("#alertStep3").removeClass("d-none").text("An unexpected error occurred.");
            }
        });
    });

    // Handle db Click
    $("#dbForm").submit(function (e) {
        e.preventDefault()

        let valid = true;

        $("#dbForm input").each(function () {
            var field = $(this);
            var fieldValue = field.val();
            var fieldId = field.attr('id');

            // Skip password field
            if (field.attr('type') !== 'password') {
                // Check for empty values
                if (!fieldValue) {
                    valid = false;
                    field. Class('is-invalid');
                } else {
                    // Check for spaces in the database name
                    if (fieldId === 'dbName' && /\s/.test(fieldValue)) {
                        valid = false;
                        field.addClass('is-invalid');
                    } else {
                        field.removeClass('is-invalid');
                    }
                }
            }
        });
        $("#alertStep2").addClass("d-none");
        $('#loaderStep2').show();
        $("#successAlertStep2").addClass("d-none");

        if (valid) {
            const formData = new FormData(document.getElementById("dbForm"));
            $.ajax({
                url: 'installer-config/save_db_config.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response === 'success') {
                        $("#successAlertStep2").removeClass("d-none").text("Database configuration saved successfully.");
                        setTimeout(() => {
                            showStep(++currentStep);
                            updateProgress(currentStep)
                        }, 500) // Move to Step 3
                    } else {
                        $("#loaderStep2").hide();
                        $("#alertStep2").removeClass("d-none").text(response);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Database config Error occurred:', error);
                    $("#loaderStep2").hide();
                    $("#alertStep2").removeClass("d-none").text("Failed to save database configuration.");
                }
            });
        } else {
            $("#loaderStep2").hide();
            $("#alertStep2").removeClass("d-none").text("Please fill all required fields correctly.");
        }

    });

    //admin setup form
    // $("#adminSignupBtn").click(function () {

    //     const adminUsername = $("#adminUsername").val().trim();
    //     const adminEmail = $("#adminEmail").val().trim();
    //     const adminPassword = $("#adminPassword").val().trim();
    //     const subscriptionPlan = $("#subscriptionPlan").val();
    //     const staffLimit = $("#staffLimit").val();


    //     $("#alertStep4").addClass("d-none");
    //     $("#successAlertStep4").addClass("d-none");
    //     $('#loaderStep4').show();
    //     let isValid = true;

    //     // Validation
    //     if (adminUsername === "") {
    //         $("#adminUsername").addClass("is-invalid");
    //         isValid = false;
    //     } else {
    //         $("#adminUsername").removeClass("is-invalid");
    //     }

    //     if (adminEmail === "" || !/^\S+@\S+\.\S+$/.test(adminEmail)) {
    //         $("#adminEmail").addClass("is-invalid");
    //         isValid = false;
    //     } else {
    //         $("#adminEmail").removeClass("is-invalid");
    //     }

    //     if (adminPassword === "") {
    //         $("#adminPassword").addClass("is-invalid");
    //         isValid = false;
    //     } else {
    //         $("#adminPassword").removeClass("is-invalid");
    //     }

    //     if (subscriptionPlan === "" || staffLimit === "") {
    //         $("#alertStep4").removeClass("d-none").text("Please select a subscription plan and staff limit.");
    //         $('#loaderStep4').hide();
    //         return;
    //     }

    //     if (!isValid) {
    //         $("#alertStep4").removeClass("d-none").text("Please fill all required fields correctly.");
    //         $('#loaderStep4').hide();
    //         return;
    //     }

    //     // AJAX Request to Add Admin
    //     $.ajax({
    //         url: 'installer-config/add_admin.php',
    //         type: 'POST',
    //         data: {
    //             adminUsername: adminUsername,
    //             adminEmail: adminEmail,
    //             adminPassword: adminPassword,
    //             subscriptionPlan: subscriptionPlan,
    //             staffLimit: staffLimit,
    //         },
    //         success: function (response) {

    //             try {
    //                 const res = JSON.parse(response);

    //                 if (res.status === 'success') {
    //                     $("#successAlertStep4").removeClass("d-none").text(res.message);
    //                     location.href = res.url;
    //                 } else {
    //                     $('#loaderStep4').hide();
    //                     $("#alertStep4").removeClass("d-none").text(res.message);
    //                 }
    //             } catch (e) {
    //                 $('#loaderStep4').hide();
    //                 $("#alertStep4").removeClass("d-none").text("Invalid server response.");
    //                 console.error("Error parsing response:", e);
    //             }
    //         },
    //         error: function () {
    //             $('#loaderStep4').hide();
    //             $("#alertStep4").removeClass("d-none").text("An unexpected error occurred.");
    //         }
    //     });
    // });


    $("#adminSignupBtn").click(function () {

        const adminUsername = $("#adminUsername").val().trim();
        const adminEmail = $("#adminEmail").val().trim();
        const adminPassword = $("#adminPassword").val().trim();
        const companyname = $("#companyname").val().trim();
        // const companylogo = $("#logo_img").val().trim();
        var logo_img = $("input[name='logo_img']")[0].files[0];
        // const subscriptionPlan = $("#subscriptionPlan").val();
        // const staffLimit = $("#staffLimit").val();
    
        $("#alertStep4").addClass("d-none");
        $("#successAlertStep4").addClass("d-none");
        $('#loaderStep4').show();
        let isValid = true;
    
        // Validation
        if (adminUsername == "") {
            $("#adminUsername").addClass("is-invalid");
            isValid = false;
        } else {
            $("#adminUsername").removeClass("is-invalid");
        }
    
        if (adminEmail == "" || !/^\S+@\S+\.\S+$/.test(adminEmail)) {
            $("#adminEmail").addClass("is-invalid");
            isValid = false;
        } else {
            $("#adminEmail").removeClass("is-invalid");
        }
    
        if (adminPassword == "") {
            $("#adminPassword").addClass("is-invalid");
            isValid = false;
        } else {
            $("#adminPassword").removeClass("is-invalid");
        }
    
        if (companyname == "") {
            $("#companyname").addClass("is-invalid");
            isValid = false;
        } else {
            $("#companyname").removeClass("is-invalid");
        }
    
        if (logo_img == "") {
            $("#logo_img").addClass("is-invalid");
            isValid = false;
        } else {
            $("#logo_img").removeClass("is-invalid");
        }
    
        if (!isValid) {
            $("#alertStep4").removeClass("d-none").text("Please fill all required fields correctly.");
            $('#loaderStep4').hide();
            return;
        }
        // const logoFile = $("#logo_img")[0].files[0];
        // Create FormData object
        var formData = new FormData();
        formData.append("adminUsername", adminUsername);
        formData.append("adminEmail", adminEmail);
        formData.append("adminPassword", adminPassword);
        formData.append("companyname", companyname);
        formData.append("logo_img", logo_img);
    
        // AJAX Request to Add Admin using FormData
        $.ajax({
            url: 'installer-config/add_admin.php',
            type: 'POST',
            data: formData,
            processData: false,  // Prevent jQuery from automatically converting the data
            contentType: false,  // Let jQuery handle the content type for FormData
            success: function (response) {
                console.log(response);
                try {
                    const res = JSON.parse(response);
    
                    if (res.status == 'success') {
                        $("#successAlertStep4").removeClass("d-none").text(res.message);
                        location.href = res.url;
                    } else {
                        $('#loaderStep4').hide();
                        $("#alertStep4").removeClass("d-none").text(res.message);
                    }
                } catch (e) {
                    $('#loaderStep4').hide();
                    $("#alertStep4").removeClass("d-none").text("Invalid server response.");
                    console.error("Error parsing response:", e);
                }
            },
            error: function () {
                $('#loaderStep4').hide();
                $("#alertStep4").removeClass("d-none").text("An unexpected error occurred.");
            }
        });
    });
    

    // Initialize Step 1
    showStep(currentStep);
});