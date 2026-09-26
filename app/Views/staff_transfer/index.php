<?php $this->extend('layout'); ?>
<?php $this->section('content'); ?>

<div class="row">
  <div class="col-lg-7">
    <div class="card mb-4">
      <div class="card-body">
        <h4 class="card-title mb-1">
          <i class="mdi mdi-swap-horizontal text-primary me-2"></i>Transfer Employee
        </h4>
        <p class="text-muted mb-4">Move an employee member to a different branch.</p>

        <div id="alertBox"></div>

        <form id="transferForm">
          <div class="mb-3">
            <label class="form-label fw-semibold">Employee Member <span class="text-danger">*</span></label>
            <select id="staffSelect" name="staffSelect" class="form-select" required>
              <option value="">Select employee member</option>
              <?php if (!empty($staffList)): ?>
                <?php foreach ($staffList as $s): ?>
                  <option value="<?= $s['id'] ?>"
                    data-branch="<?= htmlspecialchars($s['branch_id'] ?? '') ?>"
                    data-branchname="<?= htmlspecialchars($s['branch_name'] ?? 'Unassigned') ?>">
                    <?= htmlspecialchars(trim($s['firstname'] . ' ' . $s['lastname'])) ?>
                    - <?= htmlspecialchars($s['branch_name'] ?? 'Unassigned') ?>
                  </option>
                <?php endforeach; ?>
              <?php else: ?>
                <option value="" disabled>No employees found</option>
              <?php endif; ?>
            </select>
            <div id="currentBranch" class="form-text text-muted mt-1"></div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Transfer To Branch <span class="text-danger">*</span></label>
            <select id="toBranchSelect" class="form-select" required>
              <option value="">Select target branch</option>
              <?php foreach ($branches as $b): ?>
                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?> (<?= $b['code'] ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Effective Date</label>
            <input type="date" id="effectiveDate" class="form-control" value="<?= date('Y-m-d') ?>">
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold">Reason (optional)</label>
            <textarea id="reason" class="form-control" rows="2" placeholder="Reason for transfer..."></textarea>
          </div>

          <button type="submit" class="btn hr-btnbg" id="transferBtn">
            <i class="mdi mdi-swap-horizontal me-1"></i>Transfer Staff
          </button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-3"><i class="mdi mdi-history me-2"></i>Transfer History</h5>
        <div id="historyList">
          <div class="text-center py-3"><div class="spinner-border spinner-border-sm text-secondary"></div></div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
const token = localStorage.getItem('token') || '';
const headers = {
  'Authorization': 'Bearer ' + token,
  'Content-Type': 'application/json'
};

// Initialize Select2 on dropdowns for searchable experience
$(document).ready(function() {
  if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
    $('#staffSelect').select2({
      placeholder: 'Search employee member...',
      allowClear: true,
      width: '100%'
    });
    $('#toBranchSelect').select2({
      placeholder: 'Select target branch',
      allowClear: true,
      width: '100%'
    });
  }
});

// Show current branch when staff is selected
$(document).on('change', '#staffSelect', function() {
  const opt = this.options[this.selectedIndex];
  if (opt && this.value) {
    const info = document.getElementById('currentBranch');
    info.textContent = 'Current Branch: ' + (opt.dataset.branchname || 'Unassigned');
  } else {
    document.getElementById('currentBranch').textContent = '';
  }
});

// Load history
function loadHistory() {
  fetch('/api/staff-transfer/history', { headers })
    .then(r => r.json())
    .then(d => {
      const el = document.getElementById('historyList');
      if (!d.data || !d.data.length) {
        el.innerHTML = '<p class="text-muted text-center py-2">No transfers yet.</p>';
        return;
      }
      el.innerHTML = d.data.slice(0, 20).map(t => `
        <div class="border-start border-primary ps-3 mb-3">
          <div class="fw-semibold">${escHtml(t.firstname + ' ' + t.lastname)}</div>
          <div class="small text-muted">
            ${escHtml(t.from_branch_name || '-')} &rarr; ${escHtml(t.to_branch_name || '-')}
          </div>
          <div class="small text-muted">
            ${t.effective_date || ''} &bull;
            By: ${escHtml(t.transferred_by_firstname + ' ' + t.transferred_by_lastname)}
          </div>
          ${t.reason ? `<div class="small fst-italic">${escHtml(t.reason)}</div>` : ''}
        </div>`).join('');
    })
    .catch(() => {
      document.getElementById('historyList').innerHTML = '<p class="text-muted text-center py-2">No transfers yet.</p>';
    });
}

// Transfer form submit
document.getElementById('transferForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn = document.getElementById('transferBtn');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Transferring...';

  const payload = {
    user_id:        document.getElementById('staffSelect').value,
    to_branch_id:   document.getElementById('toBranchSelect').value,
    effective_date: document.getElementById('effectiveDate').value,
    reason:         document.getElementById('reason').value.trim(),
  };

  try {
    const res  = await fetch('/api/staff-transfer/initiate', {
      method: 'POST',
      headers: headers,
      body: JSON.stringify(payload)
    });
    const data = await res.json();

    document.getElementById('alertBox').innerHTML =
      `<div class="alert alert-${data.status === 'success' ? 'success' : 'danger'} alert-dismissible fade show">
        ${data.message || 'Done.'}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>`;

    if (data.status === 'success') {
      document.getElementById('transferForm').reset();
      document.getElementById('effectiveDate').value = new Date().toISOString().split('T')[0];
      if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
        $('#staffSelect').val(null).trigger('change');
        $('#toBranchSelect').val(null).trigger('change');
      }
      loadHistory();
    }
  } catch {
    document.getElementById('alertBox').innerHTML = '<div class="alert alert-danger">Network error.</div>';
  }

  btn.disabled = false;
  btn.innerHTML = '<i class="mdi mdi-swap-horizontal me-1"></i>Transfer Staff';
});

function escHtml(str) {
  return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

loadHistory();
</script>
<?php $this->endSection(); ?>