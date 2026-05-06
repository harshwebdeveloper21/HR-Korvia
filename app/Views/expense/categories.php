<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-5 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title" id="form-title">Add Expense Category</h4>
                    <form action="<?= base_url('expenses/categories/store') ?>" method="POST" class="forms-sample">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" id="category_id">
                        <div class="form-group mb-3">
                            <label for="name">Category Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="e.g. Travel, Food" required>
                        </div>
                        <button type="submit" class="btn hr-btnbg me-2" id="submit-btn">Add Category</button>
                        <button type="button" class="btn btn-light" onclick="resetForm()">Cancel</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Expense Categories List</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($categories as $index => $cat): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= $cat['name'] ?></td>
                                        <td>
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="editCategory(<?= $cat['id'] ?>, '<?= esc($cat['name'], 'js') ?>')">
                                                <i class="mdi mdi-pencil"></i>
                                            </button>
                                            <a href="javascript:void(0)" class="btn btn-outline-danger btn-sm"
                                               onclick="confirmDeleteCategory('<?= base_url('expenses/categories/delete/'.$cat['id']) ?>')"
                                            >
                                                <i class="mdi mdi-delete"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
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
            text: 'This may affect existing expenses.',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#e05d2a',
            cancelButtonColor: '#6c757d',
            toast: false,
            position: 'center',
        }).then(result => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }
</script>
<?= $this->endSection() ?>
