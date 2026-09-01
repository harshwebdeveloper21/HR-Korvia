<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8 grid-margin stretch-card mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="card-title mb-1">Add New Expense</h4>
                        <p class="card-description mb-0 text-muted">Enter details for the company expense or reimbursement.</p>
                    </div>
                    <a href="<?= base_url('expenses') ?>" class="btn btn-secondary btn-sm">
                        <i class="mdi mdi-arrow-left me-1"></i> Back
                    </a>
                </div>
                
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('expenses/store') ?>" method="POST" enctype="multipart/form-data" class="forms-sample mt-3">
                    <?= csrf_field() ?>
                    
                    <div class="form-group mb-3">
                        <label for="title">Expense Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="e.g. Office Stationery, Client Meeting Lunch" value="<?= old('title') ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="category_id">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= old('category_id') == $cat['id'] ? 'selected' : '' ?>><?= $cat['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="amount">Amount (&#8377;) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">&#8377;</span>
                                    <input type="number" step="0.01" class="form-control" id="amount" name="amount" placeholder="0.00" value="<?= old('amount') ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="Cash" <?= old('payment_method') == 'Cash' ? 'selected' : '' ?>>Cash</option>
                                    <option value="Bank Transfer" <?= old('payment_method') == 'Bank Transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                                    <option value="Card" <?= old('payment_method') == 'Card' ? 'selected' : '' ?>>Card</option>
                                    <option value="UPI" <?= old('payment_method') == 'UPI' ? 'selected' : '' ?>>UPI</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="paid_by">Paid By <span class="text-danger">*</span></label>
                                <select class="form-select" id="paid_by" name="paid_by" required>
                                    <?php if($user->role !== 'employee'): ?>
                                        <option value="Company" <?= old('paid_by') == 'Company' ? 'selected' : '' ?>>Company</option>
                                    <?php endif; ?>
                                    <?php foreach($employees as $emp): ?>
                                        <?php if($user->role === 'employee' && $emp['user_id'] != $user->sub) continue; ?>
                                        <option value="<?= $emp['user_id'] ?>" <?= (old('paid_by') == $emp['user_id'] || ($user->role === 'employee' && $emp['user_id'] == $user->sub)) ? 'selected' : '' ?>><?= $emp['firstname'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="expense_date">Expense Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="expense_date" name="expense_date" value="<?= old('expense_date', date('Y-m-d')) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="attachment">Attachment (Bill/Receipt)</label>
                                <input type="file" class="form-control" id="attachment" name="attachment" accept="image/*,application/pdf">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="description">Description / Notes</label>
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Any additional details..."><?= old('description') ?></textarea>
                    </div>

                    <div class="text-end">
                        <a href="<?= base_url('expenses') ?>" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn hr-btnbg">Save Expense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
