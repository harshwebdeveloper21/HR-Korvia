<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
     .capitalize-text {
        text-transform: capitalize;
    }
@media (max-width: 767px) {
.attendenceall{
    font-size: 9px !important;
    padding: 5.3px !important;
}
.iconfontsize{
    font-size: 11px !important;
}
.cart-sm-title{
    font-size: 12px !important;
    margin-bottom: 5px !important;
}
.dataTables_length{
        margin-left: .1rem !important;
    margin-bottom: .5rem !important;
    font-size: 12px !important;
float: left !important;
}
.dataTables_filter {
    font-size: 12px !important ;
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

        #templateTable_length label {
            display: flex;
            align-items: center;
        }

        /* Hide the text inside the label */
        #templateTable_length label::first-text,
        #templateTable_length label::before {
            display: none !important;
        }

        /* Or a simpler and reliable trick */
        #templateTable_length label {
            font-size: 0;
            /* hide text */
        }

        #templateTable_length label input {
            font-size: 10px;
            /* reset font size for input */
        }

        #templateTable_length label {
            font-size: 0;
            /* hide all text inside the label */
        }

        #templateTable_length label select {
            font-size: 14px;
            /* restore font size for the dropdown */
        }
        #templateTable_filter label {
            font-size: 0;
        }

        #templateTable_filter input {
            font-size: 14px;
            /* Keep input font size normal */
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

}
</style>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                         <h4 class="card-title">All Templates</h4>

                                <a href="<?= base_url(
                                    "/add-exprience-templates",
                                ) ?>" class="btn hr-btnbg attendenceall">
                        <i class="mdi mdi-plus iconfontsize"></i> Add Template
                                </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped" id="templateTable">
                        <thead class="table-dark">
                            <tr>
                                <th>No.</th>
                                <th>Title</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="templateTableBody">
                            <!-- Injected via JS -->
                        </tbody>
                    </table>
                </div>

                <div id="noTemplates" class="text-center text-muted mt-4" style="display: none;">
                    No templates found.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AJAX Script -->
<script>
    function loadTemplates() {
        $.ajax({
            url: '<?= site_url("api/exprience-template/exprience") ?>',
            type: 'GET',
            success: function(response) {
                if (response.status === 'success' && response.data.length > 0) {
                    let html = '';
                    response.data.forEach((template, index) => {
                        html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td class="capitalize-text">

                                ${template.title}</td>
                                <td style="display: flex; align-items: center; gap: 8px;">
                                    <a href="/exprience/view/${template.id}" class="text-primary fs-5" title="View"><i class="mdi mdi-eye"></i></a>
                                    <a href="edit/template/${template.id}" class="text-warning fs-5" title="Edit"><i class="mdi mdi-pencil"></i></a>
                                    <a href="#" class="text-danger fs-5 delete-template" data-id="${template.id}" title="Delete"><i class="mdi mdi-delete"></i></a>

                            </tr>
                        `;
                    });
                    $('#templateTableBody').html(html);

                    // Initialize DataTable
                    if ($.fn.DataTable.isDataTable('#templateTable')) {
                        $('#templateTable').DataTable().destroy();
                    }
                    $('#templateTable').DataTable({
                        responsive: true,
                        pageLength: 10,
                        language: {
                            searchPlaceholder: "Search",
                            search: ""
                        }
                    });

                    $('#noTemplates').hide();
                } else {
                    $('#templateTableBody').html('');
                    $('#noTemplates').show();
                }
            },
            error: function() {
                $('#templateTableBody').html('');
                $('#noTemplates').text('Error loading templates.').show();
            }
        });
    }
     $(document).ready(function() {
        loadTemplates();
    });
     $(document).on('click', '.delete-template', function(e) {
        e.preventDefault();
        const button = $(this); // store the button reference
        const templateId = button.data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
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
                $.ajax({
                    url: '<?= site_url(
                        "api/exprience-template/delete/",
                    ) ?>' + templateId,
                    type: 'DELETE',
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Deleted!', res.message, 'success');

                            // Remove the row directly without reloading the table
                            const row = button.closest('tr');
                            $('#templateTable').DataTable().row(row).remove().draw();

                            // If no rows left, show the "noTemplates" message
                            if ($('#templateTableBody tr').length === 0) {
                                $('#noTemplates').show();
                            }

                        } else {
                            Swal.fire('Error!', res.message || 'Something went wrong.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Failed to delete template.', 'error');
                    }
                });
            }
        });
    });
</script>

<?= $this->endSection() ?>
