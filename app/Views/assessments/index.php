<?= $this->extend("layout") ?>
<?= $this->section("content") ?>

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card" style="border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h4 class="card-title mb-0">Manage Assessments</h4>
                    <div class="d-flex gap-2">
                        <a href="/api/assessment/export" class="btn btn-sm hr-btnbg">
                            <i class="mdi mdi-file-export"></i> Export
                        </a>
                        <a href="/assessment/create" class="btn btn-sm hr-btnbg">
                            <i class="mdi mdi-plus"></i> Add Assessment
                        </a>
                    </div>
                </div>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success py-2"><?= esc(session()->getFlashdata('success')) ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger py-2"><?= esc(session()->getFlashdata('error')) ?></div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover" id="assessmentTable">
                        <thead>
                            <tr>
                                <th>Candidate</th>
                                <th>Position Applied For</th>
                                <th>Round</th>
                                <th>Score</th>
                                <th>Recommendation</th>
                                <th class="no-sort">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assessments as $item): $score = $item['score']; ?>
                                <tr>
                                    <td><a href="/assessment/view/<?= $item['id'] ?>" class="text-decoration-none text-dark fw-bold"><?= esc($item['candidate_name'] ?? 'Unknown') ?></a></td>
                                    <td><?= esc($item['job_title'] ?? '') ?></td>
                                    <td><?= esc($item['interview_round'] ?? '') ?></td>
                                    <td data-order="<?= $score['max'] ? round($score['total'] / $score['max'], 4) : 0 ?>">
                                        <span class="badge" style="background: var(--hr-primary, #e66136); color: var(--hr-on-primary, #fff);">
                                            <?= $score['max'] ? $score['total'] . ' / ' . $score['max'] : '-' ?>
                                        </span>
                                    </td>
                                    <td><?= esc($item['recommendation'] ?? '-') ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="/assessment/view/<?= $item['id'] ?>" class="text-primary fs-5" title="View"><i class="mdi mdi-eye"></i></a>
                                            <a href="/assessment/pdf/<?= $item['id'] ?>" target="_blank" rel="noopener" class="text-secondary fs-5" title="Print"><i class="mdi mdi-printer"></i></a>
                                            <a href="/assessment/pdf/<?= $item['id'] ?>?download=1" class="text-info fs-5" title="Download PDF"><i class="mdi mdi-download"></i></a>
                                            <a href="/assessment/edit/<?= $item['id'] ?>" class="text-success fs-5" title="Edit"><i class="mdi mdi-pencil"></i></a>
                                            <a href="/assessment/delete/<?= $item['id'] ?>" class="text-danger fs-5" title="Delete" onclick="return confirm('Are you sure you want to delete this assessment?');"><i class="mdi mdi-delete"></i></a>
                                        </div>
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

<script>
$(function () {
    if ($.fn.DataTable) {
        $('#assessmentTable').DataTable({
            order: [],
            pageLength: 10,
            columnDefs: [{ targets: 'no-sort', orderable: false }],
            language: { emptyTable: 'No assessments found.' }
        });
    }
});
</script>

<?= $this->endSection() ?>
