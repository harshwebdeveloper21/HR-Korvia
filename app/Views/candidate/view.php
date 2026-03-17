<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
     @media (min-width: 767px) {
        .attendenceall {
            width: 193px !important;
        }
    }
    @media (max-width: 767px) {
        .attendenceall {
            font-size: 9px !important;
            padding: 5.2px !important;
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

        #candidates-Table_length label {
            display: flex;
            align-items: center;
        }

        /* Hide the text inside the label */
        #candidates-Table_length label::first-text,
        #candidates-Table_length label::before {
            display: none !important;
        }

        /* Or a simpler and reliable trick */
        #candidates-Table_length label {
            font-size: 0;
            /* hide text */
        }

        #candidates-Table_length label input {
            font-size: 10px;
            /* reset font size for input */
        }

   #candidates-Tabl_filter label {
    font-size: 0;
  }
  #candidates-Tabl_filter input {
    font-size: 14px; /* Keep input font size normal */
  }
        #candidates-Table_length label {
            font-size: 0;
            /* hide all text inside the label */
        }

        #candidates-Table_length label select {
            font-size: 14px;
            /* restore font size for the dropdown */
        }

        /* .form-control {
            height: 0px !important;
        } */
         div.dataTables_wrapper div.dataTables_filter input {
    margin-left: 0.5em;
    display: inline-block;
      width: 212px !important;
    height:29px !important

}
 .custom-select{
            height: 26px !important;
            width: 57px !important;
        }

    }
     .capitalize-text {
        text-transform: capitalize;
    }
</style>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">


                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Manage Candidates</h4>
                    <a href="<?= base_url(
                        "/candidate",
                    ) ?>" class="btn hr-btnbg attendenceall">
                        <i class="mdi mdi-plus iconfontsize"></i> Add Candidate
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped" id="candidates-Table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Job Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="candidates-Table-Body">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const token = localStorage.getItem('token'); // JWT token from login

        // Fetch candidates when the page loads
        fetch('/api/candidate', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
            })
            .then((response) => response.json())
            .then((responseData) => {
                if (responseData.status === 'success') {
                    const candidates = responseData.data; // Get candidate data
                    let tableRows = '';

                    candidates.forEach((candidate, index) => {
                        // Create a row for each candidate
                        tableRows += `
                        <tr data-id="${candidate.id}">
                            <td>${index + 1}</td>
                             <td>
                             <a href="/candidate/display/${candidate.id}" class="text-decoration-none text-dark capitalize-text">
                             ${candidate.candidate_name}
                             </a></td>
                              <td>${candidate.email}</td>
                               <td class="capitalize-text">${candidate.job_title}</td>


                            <td class="capitalize-text">${candidate.status}</td>
                            <td style="display: flex; align-items: center; gap: 8px;">
                                <a href="/candidate/display/${candidate.id}" class="text-primary fs-5" title="View"><i class="mdi mdi-eye"></i></a>
                                <a href="/candidate/${candidate.id}" class="text-warning fs-5" title="Edit"><i class="mdi mdi-pencil"></i></a>
                                <a href="#" class="text-danger fs-5" title="Delete" data-id="${candidate.id}" onclick="deleteCandidate(event)"><i class="mdi mdi-delete"></i></a>
                            </td>
                        </tr>
                    `;
                    });

                    // Insert rows into table body
                    $('#candidates-Table-Body').html(tableRows);
                      if ($.fn.DataTable.isDataTable('#candidates-Table')) {
                    $('#candidates-Table').DataTable().clear().destroy();
                }
                    $('#candidates-Table').DataTable({
                    language: {
                        search: "",
                        searchPlaceholder: "Search"
                    }
                });
                    // $('#candidates-Table').DataTable(); // Initialize DataTable
                } else {
                    console.error('Failed to fetch candidates:', responseData.message);
                }
            })
            .catch((error) => {
                console.error('Error fetching candidates:', error);
            });
    });

    // Function to handle delete action
    function deleteCandidate(event) {
        event.preventDefault(); // Prevent default link behavior
        const candidateId = event.target.closest('a').getAttribute('data-id'); // Get candidate ID

        // Show SweetAlert confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: 'This candidate might have interviews or onboarding data.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'hr-btnbg',
                cancelButton: 'hr-btnbg',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Perform the delete action (AJAX call)
                fetch(`/api/candidate/${candidateId}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('token')}`,
                            'Content-Type': 'application/json',
                        },
                    })
                    .then((response) => response.json())
                    .then((responseData) => {
                        if (responseData.status === 'success') {
                            // Dynamically remove the row from the table
                            const row = document.querySelector(`tr[data-id="${candidateId}"]`);
                            if (row) row.remove();

                            // Show success message with custom CSS for OK button
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'The candidate has been deleted successfully.',
                                icon: 'success',
                                customClass: {
                                    confirmButton: 'hr-btnbg', // Apply the custom class
                                },
                                confirmButtonText: 'OK',
                            });
                        } else {
                            // Show specific error message based on the response
                            if (responseData.messages && responseData.messages.error) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: responseData.messages.error,
                                    icon: 'error',
                                    customClass: {
                                        confirmButton: 'hr-btnbg', // Apply the custom class
                                    },
                                    confirmButtonText: 'OK',
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Failed to delete the candidate. Please try again later.',
                                    icon: 'error',
                                    customClass: {
                                        confirmButton: 'hr-btnbg', // Apply the custom class
                                    },
                                    confirmButtonText: 'OK',
                                });
                            }
                        }
                    })
                    .catch((error) => {
                        console.error('Error deleting candidate:', error);
                        Swal.fire('Error!', 'An error occurred while deleting the candidate.', 'error');
                    });

            }
        });
    }
</script>

<?= $this->endSection() ?>
