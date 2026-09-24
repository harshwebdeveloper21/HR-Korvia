<?php $this->extend('layout'); ?>
<?php $this->section('content'); ?>

<div class="row">
  <div class="col-lg-7">
    <div class="card mb-4">
      <div class="card-body">
        <h4 class="card-title mb-1">
          <i class="mdi mdi-swap-horizontal text-primary me-2"></i>Transfer Staff
        </h4>
        <p class="text-muted mb-4">Move a staff member to a different branch.</p>

        <div id="alertBox"></div>

        <form id="transferForm">
          <div class="mb-3">
            <label class="form-label fw-semibold">Staff Member <span class="text-danger">*</span></label>
            <select id="staffSelect" class="form-select" required>
              <option value="">Loading staff...</option>
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
// Load eligible staff
fetch('/api/staff-transfer/eligible-staff')
  .then(r => r.json())
  .then(d => {
    const sel = document.getElementById('staffSelect');
    if (!d.data || !d.data.length) {
      sel.innerHTML = '<option value="">No eligible staff found</option>';
      return;
    }
    sel.innerHTML = '<option value="">Select staff member</option>' +
      d.data.map(s => `<option value="${s.id}" data-branch="${s.branch_id}" data-branchname="${escHtml(s.branch_name || 'Unassigned')}">
        ${escHtml(s.firstname + ' ' + s.lastname)} — ${escHtml(s.branch_name || 'Unassigned')}
      </option>`).join('');
  });

document.getElementById('staffSelect').addEventListener('change', function() {
  const opt = this.options[this.selectedIndex];
  const info = document.getElementById('currentBranch');
  info.textContent = this.value ? 'Current Branch: ' + (opt.dataset.branchname || 'Unassigned') : '';
});

// Load history
function loadHistory() {
  fetch('/api/staff-transfer/history')
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
            ${escHtml(t.from_branch_name || '—')} ? ${escHtml(t.to_branch_name || '—')}
          </div>
          <div class="small text-muted">
            ${t.effective_date || ''} &bull;
            By: ${escHtml(t.transferred_by_firstname + ' ' + t.transferred_by_lastname)}
          </div>
          ${t.reason ? `<div class="small fst-italic">${escHtml(t.reason)}</div>` : ''}
        </div>`).join('');
    });
}

// Transfer form
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
      headers: { 'Content-Type': 'application/json' },
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
