<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Expense Categories</h4>
        <a href="<?= base_url('expenses') ?>" class="btn btn-secondary btn-sm">
            <i class="mdi mdi-arrow-left me-1"></i> Back to Expenses
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-5 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3" id="form-title">Add Expense Category</h5>
                <form action="<?= base_url('expenses/categories/store') ?>" method="POST" class="forms-sample">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" id="category_id">
                    <div class="form-group mb-3">
                        <label for="name" class="fw-semibold">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="e.g. Travel, Food, Office Supplies" required>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn hr-btnbg" id="submit-btn">Add Category</button>
                        <button type="button" class="btn btn-secondary" onclick="resetForm()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Existing Categories</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-hover w-100">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Category Name</th>
                                <th style="width: 100px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($categories as $index => $cat): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td class="fw-semibold text-dark"><?= esc($cat['name']) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-link text-primary p-0 me-2" title="Edit" onclick="editCategory(<?= $cat['id'] ?>, '<?= esc($cat['name'], 'js') ?>')">
                                            <i class="mdi mdi-pencil fs-5"></i>
                                        </button>
                                        <a href="javascript:void(0)" class="text-danger" title="Delete"
                                           onclick="confirmDeleteCategory('<?= base_url('expenses/categories/delete/'.$cat['id']) ?>')"
                                        >
                                            <i class="mdi mdi-delete fs-5"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($categories)): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted p-4">No categories created yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function editCategory(id, name) {
        document.getElementById('category_id').value = id;
        document.getElementById('name').value = name;
        document.getElementById('form-title').innerText = 'Edit Expense Category';
        document.getElementById('submit-btn').innerText = 'Update Category';
    }

    function resetForm() {
        document.getElementById('category_id').value = '';
        document.getElementById('name').value = '';
        document.getElementById('form-title').innerText = 'Add Expense Category';
        document.getElementById('submit-btn').innerText = 'Add Category';
    }

    function confirmDeleteCategory(url) {
        Swal.fire({
            icon: 'warning',
            title: 'Delete Category?',
            text: 'This may affect existing expenses associated with this category.',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary ms-2'
            },
            buttonsStyling: false
        }).then(result => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }
</script>
<?= $this->endSection() ?>
