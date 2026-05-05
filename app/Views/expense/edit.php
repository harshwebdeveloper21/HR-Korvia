<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-8 grid-margin stretch-card mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Edit Expense</h4>
                        <label class="badge <?= $expense['status'] == 'Approved' ? 'badge-success' : ($expense['status'] == 'Pending' ? 'badge-warning' : 'badge-danger') ?>">
                            Status: <?= $expense['status'] ?>
                        </label>
                    </div>
                    
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('expenses/update/'.$expense['id']) ?>" method="POST" enctype="multipart/form-data" class="forms-sample">
                        <?= csrf_field() ?>
                        
                        <div class="form-group mb-3">
                            <label for="title">Expense Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="e.g. Office Stationery" value="<?= old('title', $expense['title']) ?>" required <?= $expense['status'] !== 'Pending' && $user->role === 'employee' ? 'disabled' : '' ?>>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="category_id">Category <span class="text-danger">*</span></label>
                                    <select class="form-select" id="category_id" name="category_id" required <?= $expense['status'] !== 'Pending' && $user->role === 'employee' ? 'disabled' : '' ?>>
                                        <?php foreach($categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>" <?= old('category_id', $expense['category_id']) == $cat['id'] ? 'selected' : '' ?>><?= $cat['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="amount">Amount (₹) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="<?= old('amount', $expense['amount']) ?>" required <?= $expense['status'] !== 'Pending' && $user->role === 'employee' ? 'disabled' : '' ?>>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                                    <select class="form-select" id="payment_method" name="payment_method" required <?= $expense['status'] !== 'Pending' && $user->role === 'employee' ? 'disabled' : '' ?>>
                                        <option value="Cash" <?= old('payment_method', $expense['payment_method']) == 'Cash' ? 'selected' : '' ?>>Cash</option>
                                        <option value="Bank Transfer" <?= old('payment_method', $expense['payment_method']) == 'Bank Transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                                        <option value="Card" <?= old('payment_method', $expense['payment_method']) == 'Card' ? 'selected' : '' ?>>Card</option>
                                        <option value="UPI" <?= old('payment_method', $expense['payment_method']) == 'UPI' ? 'selected' : '' ?>>UPI</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="paid_by">Paid By <span class="text-danger">*</span></label>
                                    <select class="form-select" id="paid_by" name="paid_by" required <?= $expense['status'] !== 'Pending' && $user->role === 'employee' ? 'disabled' : '' ?>>
                                        <?php if($user->role !== 'employee'): ?>
                                            <option value="Company" <?= old('paid_by', $expense['paid_by']) === null ? 'selected' : '' ?>>Company</option>
                                        <?php endif; ?>
                                        <?php foreach($employees as $emp): ?>
                                            <?php if($user->role === 'employee' && $emp['user_id'] != $user->sub) continue; ?>
                                            <option value="<?= $emp['user_id'] ?>" <?= (old('paid_by', $expense['paid_by']) == $emp['user_id']) ? 'selected' : '' ?>><?= $emp['firstname'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="expense_date">Expense Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="expense_date" name="expense_date" value="<?= old('expense_date', $expense['expense_date']) ?>" required <?= $expense['status'] !== 'Pending' && $user->role === 'employee' ? 'disabled' : '' ?>>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="attachment">Attachment (Bill/Receipt)</label>
                                    <input type="file" class="form-control" id="attachment" name="attachment" accept="image/*,application/pdf" <?= $expense['status'] !== 'Pending' && $user->role === 'employee' ? 'disabled' : '' ?>>
                                    <?php if(!empty($expense['attachment'])): ?>
                                        <div class="mt-2">
                                            <a href="<?= base_url('uploads/expenses/'.$expense['attachment']) ?>" target="_blank" class="text-primary small"><i class="mdi mdi-attachment"></i> View Current Attachment</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="description">Description / Notes</label>
                            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Any additional details..." <?= $expense['status'] !== 'Pending' && $user->role === 'employee' ? 'disabled' : '' ?>><?= old('description', $expense['description']) ?></textarea>
                        </div>

                        <div class="text-end">
                            <a href="<?= base_url('expenses') ?>" class="btn btn-light me-2">Back to List</a>
                            <?php if ($expense['status'] === 'Pending' || $user->role !== 'employee'): ?>
                                <button type="submit" class="btn hr-btnbg me-2">Update Expense</button>
                            <?php endif; ?>
                            
                            <?php if($user->role !== 'employee' && $expense['status'] === 'Pending'): ?>
                                <div class="mt-4 pt-4 border-top">
                                    <p class="text-muted small">Approval Actions:</p>
                                    <a href="<?= base_url('expenses/approve/'.$expense['id']) ?>" class="btn btn-success text-white me-2" onclick="return confirm('Approve this expense?')">Approve</a>
                                    <a href="<?= base_url('expenses/reject/'.$expense['id']) ?>" class="btn btn-danger text-white" onclick="return confirm('Reject this expense?')">Reject</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
