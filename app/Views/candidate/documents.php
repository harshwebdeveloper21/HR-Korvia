<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
.doc-wizard { font-family: "Inter", sans-serif; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
.wizard-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
.progress-container { width: 200px; text-align: right; }
.progress-text { font-size: 14px; font-weight: 500; margin-bottom: 5px; display: flex; justify-content: space-between;}
.progress-bar-custom { height: 8px; background-color: #e9ecef; border-radius: 4px; overflow: hidden; display: flex; }
.progress-bar-fill { height: 100%; background-color: var(--hr-primary, #e75c25); transition: width 0.3s ease; }
.doc-item { padding: 20px 0; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
.doc-info { flex: 1; }
.doc-info h5 { margin-bottom: 5px; font-size: 16px; font-weight: 600; color: #212529;}
.doc-info h5 span.text-danger { color: var(--hr-primary, #e75c25) !important; }
.doc-info p { margin-bottom: 8px; font-size: 13px; color: #6c757d; }
.status-badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 500; margin-right: 10px; }
.status-approved { background-color: #d1e7dd; color: #0f5132; }
.status-rejected { background-color: #f8d7da; color: #842029; }
.status-not-uploaded { background-color: #f1f3f5; color: #6c757d; }
.file-name { font-size: 13px; color: #495057; }
.reject-reason { font-size: 13px; color: #dc3545; margin-top: 8px; }
.btn-upload { color: var(--hr-primary, #e75c25); border-color: var(--hr-primary, #e75c25); background: white; }
.btn-upload:hover { background-color: var(--hr-primary-dark, #e75c25); color: var(--hr-on-primary, #fff); }
.btn-replace { color: #495057; border-color: #ced4da; background: white; }
.btn-replace:hover { background-color: #f8f9fa; }
.btn-remove { color: #495057; border-color: #ced4da; background: white; }
.btn-remove:hover { background-color: #f8f9fa; }
.footer-buttons { padding-top: 20px; border-top: 1px solid #eee; }
.btn-primary-custom { background-color: var(--hr-primary, #e75c25); border-color: var(--hr-primary, #e75c25); color: var(--hr-on-primary, #fff); }
.btn-primary-custom:hover { background-color: var(--hr-primary-dark, #d05321); border-color: var(--hr-primary-dark, #d05321); color: var(--hr-on-primary, #fff); }
.preview-container { margin-top: 0; display: none; flex-shrink: 0; }
.preview-container img { max-height: 80px; max-width: 150px; border-radius: 5px; border: 1px solid #ddd; object-fit: cover; }
</style>

<div class="content-wrapper doc-wizard<?= isset($selectedCandidate) ? ' doc-wizard-detail' : '' ?>">
    <?php if (!isset($selectedCandidate)): ?>
<style>
    /* DataTable mobile styles */
    @media (max-width: 767px) {
        .dataTables_length, .dataTables_filter { font-size: 12px !important; float: left !important; }
        div.dataTables_wrapper div.dataTables_filter input { width: 212px !important; height: 29px !important; }
    }
</style>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
                    <h4 class="card-title mb-0">Candidate Documents</h4>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn hr-btnbg text-nowrap" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                            <i class="mdi mdi-plus iconfontsize"></i> Add Document
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped w-100" id="candidates-Table">
                        <thead class="table-light">
                            <tr>
                                <th>Candidate Name</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th style="width: 150px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($candidates) && is_array($candidates)): ?>
                                <?php foreach($candidates as $c): ?>
                                    <tr>
                                        <td class="capitalize-text fw-bold"><?= esc($c['candidate_name']) ?></td>
                                        <td><?= esc($c['email']) ?></td>
                                        <td><?= esc($c['phone_number'] ?? 'N/A') ?></td>
                                        <td>
                                            <a href="/candidate-documents/<?= $c['id'] ?>" class="btn btn-sm hr-btnbg"><i class="mdi mdi-eye"></i> View / Upload</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Document Modal -->
<div class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDocumentModalLabel">Select Candidate to Add Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label for="candidate_id_select_modal" class="form-label fw-bold">Select Employee / Candidate <span class="text-danger">*</span></label>
                    <select id="candidate_id_select_modal" class="form-select" onchange="if(this.value) window.location.href='/candidate-documents/'+this.value;">
                        <option value="">-- Select --</option>
                        <?php if (isset($candidates) && is_array($candidates)): ?>
                            <?php foreach($candidates as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= esc($c['candidate_name']) ?> (<?= esc($c['email']) ?>)</option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Initialize DataTable -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        if ($.fn.DataTable) {
            $('#candidates-Table').DataTable({
                language: {
                    search: "",
                    searchPlaceholder: "Search"
                }
            });
        }
    });
</script>
<?php else: ?>
        <style>
            :root {
                --brand-color: var(--hr-primary, #e8602c);
                --brand-hover: var(--hr-primary-dark, #d05321);
                --brand-on: var(--hr-on-primary, #fff);
                --text-color: #1c2230;
                --muted-color: #6b7385;
                --border-color: #e6e8ee;
                --bg-color: #f4f5f7;
                --success-bg: #d1e7dd;
                --success-text: #1d8a5b;
                --warning-bg: #fff3cd;
                --warning-text: #b7791f;
                --danger-bg: #f8d7da;
                --danger-text: #c53030;
            }

            .doc-wizard-card {
                background: #fff;
                border: 1px solid var(--border-color);
                border-radius: 12px;
                font-family: "Inter", sans-serif;
                color: var(--text-color);
            }

            .wizard-header {
                padding: 24px;
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                align-items: center;
                border-bottom: 1px solid var(--border-color);
            }

            .wizard-header h1 {
                font-size: 20px;
                font-weight: 700;
                margin-bottom: 4px;
            }

            .wizard-header .subtitle {
                font-size: 13px;
                color: var(--muted-color);
                margin-bottom: 0;
            }

            .progress-block {
                width: 240px;
                text-align: right;
            }

            .progress-text {
                font-size: 13px;
                font-weight: 600;
                display: flex;
                justify-content: space-between;
                margin-bottom: 6px;
            }

            .progress-bar-container {
                height: 8px;
                background-color: var(--border-color);
                border-radius: 4px;
                overflow: hidden;
            }

            .progress-bar-fill {
                height: 100%;
                background-color: var(--brand-color);
                transition: width 0.3s ease;
            }

            .tab-pill {
                font-size: 11px;
                font-weight: 700;
                background-color: var(--bg-color);
                color: var(--muted-color);
                padding: 2px 8px;
                border-radius: 12px;
                transition: background-color 0.3s, color 0.3s;
            }

            .tab-pill.completed {
                background-color: var(--success-bg);
                color: var(--success-text);
            }

            .wizard-content {
                padding: 24px;
            }

            .info-note {
                background-color: rgba(var(--hr-primary-rgb, 232, 96, 44), .08);
                color: var(--hr-primary-text, #5c3a21);
                font-size: 13px;
                padding: 12px 16px;
                border-radius: 8px;
                margin-bottom: 16px;
            }

            .doc-row {
                display: grid;
                grid-template-columns: 72px 1fr auto;
                gap: 16px;
                align-items: center;
                padding: 20px 0;
                border-bottom: 1px solid var(--border-color);
            }
            .doc-row:last-child {
                border-bottom: none;
            }

            .doc-thumbnail {
                width: 72px;
                height: 88px;
                border: 1px solid var(--border-color);
                border-radius: 8px;
                background-color: var(--bg-color);
                display: flex;
                justify-content: center;
                align-items: center;
                overflow: hidden;
            }

            .doc-thumbnail img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                cursor: pointer;
            }

            .doc-thumbnail.no-file {
                border-style: dashed;
                color: var(--muted-color);
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
            }

            .doc-thumbnail.pdf-icon {
                color: var(--danger-text);
                font-size: 12px;
                font-weight: 700;
            }

            .doc-title {
                font-size: 15px;
                font-weight: 700;
                margin-bottom: 8px;
                color: var(--text-color);
            }

            .doc-meta {
                display: flex;
                align-items: center;
                gap: 12px;
                flex-wrap: wrap;
            }

            .status-badge {
                font-size: 12px;
                font-weight: 600;
                padding: 4px 10px;
                border-radius: 12px;
            }
            .status-uploaded { background-color: var(--success-bg); color: var(--success-text); }
            .status-review { background-color: var(--warning-bg); color: var(--warning-text); }
            .status-missing { background-color: var(--danger-bg); color: var(--danger-text); }
            
            .file-name {
                font-size: 13px;
                color: var(--muted-color);
            }

            .month-picker-wrapper {
                display: flex;
                align-items: stretch;
                border: 1px solid var(--border-color);
                border-radius: 6px;
                overflow: hidden;
            }
            .month-picker-icon {
                background-color: var(--brand-color);
                color: var(--brand-on);
                padding: 4px 8px;
                display: flex;
                align-items: center;
            }
            .month-picker-input {
                border: none;
                font-size: 12px;
                padding: 4px 8px;
                outline: none;
                color: var(--text-color);
            }
            .month-picker-input:focus {
                outline: 2px solid var(--brand-color);
            }

            .doc-actions {
                display: flex;
                gap: 10px;
            }

            .btn-outline-action {
                background: white;
                border: 1px solid var(--border-color);
                color: var(--text-color);
                font-size: 13px;
                font-weight: 600;
                padding: 6px 16px;
                border-radius: 8px;
                transition: background-color 0.2s;
            }
            .btn-outline-action:hover {
                background: var(--bg-color);
            }

            .btn-brand {
                background: var(--brand-color);
                border: 1px solid var(--brand-color);
                color: var(--brand-on);
                font-size: 13px;
                font-weight: 600;
                padding: 8px 20px;
                border-radius: 8px;
                transition: background-color 0.2s;
            }
            .btn-brand:hover {
                background: var(--brand-hover);
                color: var(--brand-on);
            }
            .btn-brand:disabled {
                background: var(--brand-color);
                border-color: var(--brand-color);
                opacity: .55;
                cursor: not-allowed;
            }

            .wizard-footer {
                padding: 20px 24px;
                border-top: 1px solid var(--border-color);
                display: flex;
                justify-content: space-between;
            }

            .error-text {
                color: var(--danger-text);
                font-size: 12px;
                margin-top: 6px;
                display: none;
            }

            .wizard-card { margin-bottom: 24px; }
            .wizard-card:last-child { margin-bottom: 0; }
            .doc-section-title {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                font-size: 13px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .4px;
                color: var(--hr-primary-text, var(--hr-primary, var(--brand-color)));
                background: rgba(var(--hr-primary-rgb, 232, 96, 44), .08);
                border-left: 4px solid var(--hr-primary, var(--brand-color));
                border-radius: 6px;
                padding: 9px 14px;
            }
            .doc-section-title .title-left { display: flex; align-items: center; gap: 8px; }
            .doc-section-title .btn-outline-action { text-transform: none; letter-spacing: 0; padding: 4px 12px; font-size: 12px; }

            .header-right { display: flex; align-items: center; gap: 16px; }
            .doc-wizard.doc-wizard-detail { padding: 0; background: transparent; box-shadow: none; }
            .doc-wizard-detail .wizard-header { padding: 16px 20px; }
            .doc-wizard-detail .wizard-content { padding: 16px 20px; }
            .doc-wizard-detail .wizard-footer { padding: 14px 20px; }

            .doc-sections { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; }
            .doc-sections .wizard-card { margin-bottom: 0; min-width: 0; }
            .doc-sections .span-full { grid-column: 1 / -1; }
            .doc-sections .span-2 { grid-column: span 2; }
            .doc-list { display: grid; grid-template-columns: minmax(0, 1fr); gap: 12px; margin-top: 12px; }
            .doc-list.cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .doc-list.cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .doc-list .doc-row {
                grid-template-columns: 52px minmax(0, 1fr);
                gap: 12px;
                align-items: start;
                padding: 12px;
                border: 1px solid var(--border-color);
                border-radius: 10px;
                background: #fff;
            }
            .doc-list .doc-row:last-child { border-bottom: 1px solid var(--border-color); }
            .doc-list .doc-thumbnail { width: 52px; height: 64px; font-size: 9px; }
            .doc-list .doc-title { font-size: 13.5px; margin-bottom: 6px; }
            .doc-list .doc-meta { gap: 6px; }
            .doc-list .file-name { flex-basis: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 12px; }
            .doc-list .doc-actions { grid-column: 1 / -1; justify-content: flex-end; gap: 8px; }
            .doc-list .doc-actions .btn-brand, .doc-list .doc-actions .btn-outline-action { padding: 5px 14px; font-size: 12px; }

            @media (max-width: 1199px) {
                .doc-list.cols-3 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            }
            @media (max-width: 991px) {
                .doc-sections { grid-template-columns: minmax(0, 1fr); }
                .doc-sections .span-2 { grid-column: auto; }
            }
            @media (max-width: 575px) {
                .doc-list.cols-2, .doc-list.cols-3 { grid-template-columns: minmax(0, 1fr); }
            }

            /* Focus Styles */
            button:focus-visible, input:focus-visible {
                outline: 2px solid var(--brand-color);
                outline-offset: 2px;
            }

            @media (max-width: 640px) {
                .doc-row {
                    grid-template-columns: 56px 1fr;
                    grid-template-rows: auto auto;
                }
                .doc-thumbnail {
                    width: 56px;
                    height: 70px;
                }
                .doc-actions {
                    grid-column: 1 / -1;
                    justify-content: flex-start;
                    margin-top: 8px;
                }
                .wizard-header {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 16px;
                }
                .progress-block { width: 100%; }
                .header-right { width: 100%; }
            }
        </style>

        <?php
        $backUrl = service('request')->getGet('from') === 'employees' ? '/empview' : '/addinterview';
        $requiredDocs = ['salary_1', 'salary_2', 'salary_3', 'experience_letter', 'id_proof', 'edu_cert'];
        $tabsConfig = [
            1 => ['title' => 'Salary slips', 'keys' => ['salary_1', 'salary_2', 'salary_3']],
            2 => ['title' => 'Previous company', 'keys' => ['experience_letter']], // relieved letter not mandatory per original
            3 => ['title' => 'ID and education', 'keys' => ['id_proof', 'edu_cert']],
            4 => ['title' => 'Other documents', 'keys' => ['other_doc', 'other_doc_2']]
        ];

        // Ensure other docs from DB are included
        foreach ($docsMap as $k => $v) {
            if (strpos($k, 'other_doc') === 0 && !in_array($k, $tabsConfig[4]['keys'])) {
                $tabsConfig[4]['keys'][] = $k;
            }
        }
        ?>

        <div class="doc-wizard-card">
            <div class="wizard-header">
                <div>
                    <h1>Documents for <?= esc($selectedCandidate['candidate_name']) ?></h1>
                    <p class="subtitle">Upload salary slips, experience letter and other required documents.</p>
                </div>
                <div class="header-right">
                    <div class="progress-block">
                        <div class="progress-text">
                            <span id="progress-text-label">0 of 0 required</span>
                            <span id="progress-text-percent">0%</span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" id="progress-bar-fill" style="width: 0%;"></div>
                        </div>
                    </div>
                    <a href="<?= $backUrl ?>" class="btn-outline-action text-decoration-none text-nowrap"><i class="mdi mdi-arrow-left"></i> Back</a>
                </div>
            </div>

            <div class="wizard-content">
                <div class="info-note">
                    Allowed: PDF, JPG, PNG. Maximum 5 MB per file. HR will review each document.
                </div>

                <?php
                if (!function_exists('renderNewDocItem')) {
                    function renderNewDocItem($docKey, $defaultTitle, $defaultSubtitle, $isRequired, $docsMap, $isMonthPicker = false) {
                        $isUploaded = isset($docsMap[$docKey]) && !empty($docsMap[$docKey]['file_name']);
                        // Determine status based on DB or missing
                        $statusClass = 'status-missing';
                        $statusText = 'Missing';
                        if ($isUploaded) {
                            $dbStatus = strtolower($docsMap[$docKey]['status']);
                            if ($dbStatus === 'approved') {
                                $statusClass = 'status-uploaded';
                                $statusText = 'Uploaded';
                            } elseif ($dbStatus === 'pending') {
                                $statusClass = 'status-review';
                                $statusText = 'Under review';
                            } elseif ($dbStatus === 'rejected') {
                                $statusClass = 'status-missing'; // visually red
                                $statusText = 'Rejected';
                            } else {
                                $statusClass = 'status-uploaded';
                                $statusText = 'Uploaded';
                            }
                        }

                        $fileName = $isUploaded ? $docsMap[$docKey]['file_name'] : '';
                        $filePath = $isUploaded ? '/' . $docsMap[$docKey]['file_path'] : '';
                        $ext = $isUploaded ? strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) : '';
                        $isPdf = $ext === 'pdf';
                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);

                        $savedTitle = (isset($docsMap[$docKey]) && !empty($docsMap[$docKey]['doc_title'])) ? $docsMap[$docKey]['doc_title'] : $defaultTitle;
                        $savedSubtitle = (isset($docsMap[$docKey]) && !empty($docsMap[$docKey]['doc_subtitle'])) ? $docsMap[$docKey]['doc_subtitle'] : $defaultSubtitle;

                        $monthVal = '';
                        if ($isMonthPicker && !empty($savedSubtitle)) {
                            if (preg_match('/^\d{4}-\d{2}$/', trim($savedSubtitle))) {
                                $monthVal = trim($savedSubtitle);
                            } else {
                                $ts = strtotime($savedSubtitle);
                                if ($ts !== false) $monthVal = date('Y-m', $ts);
                            }
                        }

                        $reqData = $isRequired ? 'data-required="1"' : 'data-required="0"';

                        echo '<div class="doc-row" data-key="'.$docKey.'" '.$reqData.'>';
                        
                        // Thumbnail
                        echo '<div class="doc-thumbnail '.(!$isUploaded ? 'no-file' : ($isPdf ? 'pdf-icon' : '')).'">';
                        if (!$isUploaded) {
                            echo 'No file';
                        } elseif ($isPdf) {
                            echo 'PDF';
                        } elseif ($isImage) {
                            echo '<img src="'.$filePath.'" alt="Preview" class="zoomable-image">';
                        } else {
                            echo 'FILE';
                        }
                        echo '</div>';

                        // Info
                        echo '<div class="doc-info">';
                        echo '<div class="doc-title">'.esc($savedTitle).'</div>';
                        echo '<div class="doc-meta">';
                        echo '<span class="status-badge '.$statusClass.'">'.$statusText.'</span>';
                        if ($isUploaded) {
                            echo '<span class="file-name">'.esc($fileName).'</span>';
                        }
                        
                        if ($isMonthPicker) {
                            echo '<div class="month-picker-wrapper">
                                    <div class="month-picker-icon"><i class="mdi mdi-calendar"></i></div>
                                    <input type="month" class="month-picker-input row-month-input" value="'.esc($monthVal).'">
                                  </div>';
                        }
                        echo '</div>';
                        echo '<div class="error-text"></div>';
                        echo '</div>'; // End info

                        // Actions
                        echo '<div class="doc-actions">';
                        echo '<input type="file" class="d-none file-input" accept=".pdf,.jpg,.jpeg,.png">';
                        if ($isUploaded) {
                            echo '<button type="button" class="btn-outline-action btn-replace" aria-label="Replace '.esc($savedTitle).'">Replace</button>';
                            echo '<button type="button" class="btn-outline-action btn-remove" aria-label="Remove '.esc($savedTitle).'">Remove</button>';
                        } else {
                            echo '<button type="button" class="btn-brand btn-upload" aria-label="Upload '.esc($savedTitle).'">Upload file</button>';
                        }
                        echo '</div>';

                        echo '</div>';
                    }
                }
                ?>

                <?php
                $sectionTitle = static function (int $step, string $title, string $extra = ''): string {
                    return '<div class="doc-section-title"><span class="title-left">' . esc($title)
                        . ' <span class="tab-pill" id="tab-pill-' . $step . '">0/0</span></span>' . $extra . '</div>';
                };
                ?>

                <div class="doc-sections">
                    <div class="wizard-card span-full" id="step-1" data-keys="<?= implode(',', $tabsConfig[1]['keys']) ?>">
                        <?= $sectionTitle(1, $tabsConfig[1]['title']) ?>
                        <div class="doc-list cols-3">
                            <?php
                            renderNewDocItem('salary_1', 'Salary slip, month 1', 'August 2026', true, $docsMap, true);
                            renderNewDocItem('salary_2', 'Salary slip, month 2', 'July 2026', true, $docsMap, true);
                            renderNewDocItem('salary_3', 'Salary slip, month 3', 'June 2026', true, $docsMap, true);
                            ?>
                        </div>
                    </div>

                    <div class="wizard-card" id="step-2" data-keys="<?= implode(',', $tabsConfig[2]['keys']) ?>">
                        <?= $sectionTitle(2, $tabsConfig[2]['title']) ?>
                        <div class="doc-list">
                            <?php renderNewDocItem('experience_letter', 'Experience letter', 'From your previous company', true, $docsMap); ?>
                        </div>
                    </div>

                    <div class="wizard-card span-2" id="step-3" data-keys="<?= implode(',', $tabsConfig[3]['keys']) ?>">
                        <?= $sectionTitle(3, $tabsConfig[3]['title']) ?>
                        <div class="doc-list cols-2">
                            <?php
                            renderNewDocItem('id_proof', 'Aadhaar card', 'Aadhaar or PAN', true, $docsMap);
                            renderNewDocItem('edu_cert', 'Highest degree certificate', 'Highest qualification', true, $docsMap);
                            ?>
                        </div>
                    </div>

                    <div class="wizard-card span-full" id="step-4" data-keys="<?= implode(',', $tabsConfig[4]['keys']) ?>">
                        <?= $sectionTitle(4, $tabsConfig[4]['title'], '<button type="button" class="btn-outline-action" id="addMoreOtherDocBtn" style="color: var(--brand-color); border-color: var(--brand-color);">+ Add more document</button>') ?>
                        <div id="other-docs-container" class="doc-list cols-3">
                            <?php
                            foreach ($tabsConfig[4]['keys'] as $idx => $k) {
                                $title = ($idx == 0) ? 'Resume' : (($idx == 1) ? 'Passport photo' : 'Other document');
                                renderNewDocItem($k, $title, '', false, $docsMap);
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="wizard-footer">
                <button type="button" class="btn-brand ms-auto" id="doneBtn">Done</button>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Image Zoom Modal -->
<div class="modal fade" id="imageZoomModal" tabindex="-1" aria-labelledby="imageZoomModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="background: transparent; border: none; box-shadow: none;">
      <div class="modal-header border-0" style="padding: 0; position: absolute; right: 0; z-index: 1055;">
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="background-color: rgba(0,0,0,0.5); border-radius: 50%; padding: 10px; margin: 10px;"></button>
      </div>
      <div class="modal-body text-center p-0">
        <img id="zoomedImage" src="" alt="Zoomed Document" class="img-fluid rounded shadow" style="max-height: 90vh;">
      </div>
    </div>
  </div>
</div>

<script>
window.csrfName = '<?= csrf_token() ?>';
window.csrfHash = '<?= csrf_hash() ?>';

$(document).ready(function() {
    const candidateId = "<?= esc($selectedCandidate['id'] ?? '') ?>";
    const totalTabs = 4;
    const backUrl = <?= json_encode($backUrl ?? '/addinterview') ?>;
    const themeColor = getComputedStyle(document.documentElement).getPropertyValue('--hr-primary').trim() || '#e8602c';
    let missingRequired = 0;

    function updateProgress() {
        let totalReq = 0;
        let uploadedReq = 0;

        $('.doc-row').each(function() {
            if ($(this).data('required') == '1') {
                totalReq++;
                if ($(this).find('.status-badge').hasClass('status-uploaded') || $(this).find('.status-badge').hasClass('status-review')) {
                    uploadedReq++;
                }
            }
        });

        const pct = totalReq > 0 ? Math.round((uploadedReq / totalReq) * 100) : 0;
        $('#progress-text-label').text(`${uploadedReq} of ${totalReq} required`);
        $('#progress-text-percent').text(`${pct}%`);
        $('#progress-bar-fill').css('width', `${pct}%`);
        missingRequired = totalReq - uploadedReq;

        // Update pills
        for (let i = 1; i <= totalTabs; i++) {
            let tabTotal = 0;
            let tabUp = 0;
            $(`#step-${i} .doc-row`).each(function() {
                tabTotal++;
                if ($(this).find('.status-badge').hasClass('status-uploaded') || $(this).find('.status-badge').hasClass('status-review')) {
                    tabUp++;
                }
            });
            const pill = $(`#tab-pill-${i}`);
            pill.text(`${tabUp}/${tabTotal}`);
            if (tabUp === tabTotal && tabTotal > 0) {
                pill.addClass('completed');
            } else {
                pill.removeClass('completed');
            }
        }
    }

    updateProgress();

    $('#doneBtn').click(function(){
        if (missingRequired > 0) {
            Swal.fire({
                title: 'Documents pending',
                text: `${missingRequired} required document(s) are still missing. Leave this page anyway?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: themeColor,
                confirmButtonText: 'Yes, leave',
                cancelButtonText: 'Stay'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = backUrl;
            });
            return;
        }
        Swal.fire('Success', 'All required documents are uploaded!', 'success').then(() => {
            window.location.href = backUrl;
        });
    });

    // Add More Document Logic
    $('#addMoreOtherDocBtn').click(function() {
        const container = $('#other-docs-container');
        const count = container.find('.doc-row').length + 1;
        const key = 'other_doc_' + count;
        
        const newRowHtml = `
        <div class="doc-row" data-key="${key}" data-required="0">
            <div class="doc-thumbnail no-file">No file</div>
            <div class="doc-info">
                <div class="doc-title" contenteditable="true" style="border-bottom: 1px dashed #ccc; display: inline-block; min-width: 150px; padding-bottom: 2px;" title="Click to edit title">Other document ${count}</div>
                <div class="doc-meta">
                    <span class="status-badge status-missing">Missing</span>
                </div>
                <div class="error-text"></div>
            </div>
            <div class="doc-actions">
                <input type="file" class="d-none file-input" accept=".pdf,.jpg,.jpeg,.png">
                <button type="button" class="btn-brand btn-upload" aria-label="Upload Other document ${count}">Upload file</button>
            </div>
        </div>`;
        container.append(newRowHtml);
    });

    // Save edited title via AJAX when it loses focus (for dynamically added or existing ones)
    $(document).on('blur', '.doc-title[contenteditable="true"]', function() {
        const row = $(this).closest('.doc-row');
        const key = row.data('key');
        const val = $(this).text().trim();
        
        if (!val) return;
        
        const formData = new FormData();
        formData.append('candidate_id', candidateId);
        formData.append(`doc_title_${key}`, val);
        if(window.csrfName && window.csrfHash) {
            formData.append(window.csrfName, window.csrfHash);
        }
        
        $.ajax({
            url: '/candidate-documents/upload',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) { 
                if (response.csrf_hash) window.csrfHash = response.csrf_hash;
            }
        });
    });

    // Handle Upload/Replace click
    $(document).on('click', '.btn-upload, .btn-replace', function() {
        $(this).closest('.doc-actions').find('.file-input').click();
    });

    // Handle file selection and AJAX Upload
    $(document).on('change', '.file-input', function() {
        const file = this.files[0];
        if (!file) return;

        const row = $(this).closest('.doc-row');
        const key = row.data('key');
        const errObj = row.find('.error-text');
        
        // Validate type
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
        if (!allowedTypes.includes(file.type)) {
            errObj.text('Invalid file type. Only PDF, JPG, PNG allowed.').show();
            $(this).val('');
            return;
        }

        // Validate size
        if (file.size > 5 * 1024 * 1024) {
            errObj.text('File is too large. Maximum 5 MB.').show();
            $(this).val('');
            return;
        }

        errObj.hide();

        const formData = new FormData();
        formData.append(key, file);
        formData.append('candidate_id', candidateId);
        
        const titleText = row.find('.doc-title').text();
        formData.append(`doc_title_${key}`, titleText);
        
        const monthInput = row.find('.row-month-input');
        if (monthInput.length > 0) {
            formData.append(`doc_subtitle_${key}`, monthInput.val());
        }

        // Add CSRF token
        if(window.csrfName && window.csrfHash) {
            formData.append(window.csrfName, window.csrfHash);
        }

        // Visual loading
        const originalBtnText = $(this).siblings('button:visible').text();
        $(this).siblings('button:visible').text('Uploading...').prop('disabled', true);

        $.ajax({
            url: '/candidate-documents/upload',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.csrf_hash) window.csrfHash = response.csrf_hash;
                if(response.status === 'success') {
                    // Refresh row visually
                    row.find('.doc-thumbnail').removeClass('no-file pdf-icon').empty();
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            row.find('.doc-thumbnail').html(`<img src="${e.target.result}" alt="Preview" class="zoomable-image">`);
                        }
                        reader.readAsDataURL(file);
                    } else {
                        row.find('.doc-thumbnail').addClass('pdf-icon').text('PDF');
                    }
                    
                    row.find('.status-badge').removeClass('status-missing status-review').addClass('status-uploaded').text('Uploaded');
                    
                    let fileNameSpan = row.find('.file-name');
                    if(fileNameSpan.length === 0) {
                        row.find('.status-badge').after(` <span class="file-name">${file.name}</span>`);
                    } else {
                        fileNameSpan.text(file.name);
                    }

                    const actions = row.find('.doc-actions');
                    actions.html(`
                        <input type="file" class="d-none file-input" accept=".pdf,.jpg,.jpeg,.png">
                        <button type="button" class="btn-outline-action btn-replace" aria-label="Replace">Replace</button>
                        <button type="button" class="btn-outline-action btn-remove" aria-label="Remove">Remove</button>
                    `);

                    updateProgress();
                } else {
                    errObj.text(response.message || 'Upload failed.').show();
                    row.find('.doc-actions button').text(originalBtnText).prop('disabled', false);
                }
            },
            error: function() {
                errObj.text('Server error during upload.').show();
                row.find('.doc-actions button').text(originalBtnText).prop('disabled', false);
            }
        });
    });

    // Handle Remove
    $(document).on('click', '.btn-remove', function() {
        const row = $(this).closest('.doc-row');
        const key = row.data('key');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "This document will be deleted permanently.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: themeColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                let data = {
                    candidate_id: candidateId,
                    doc_key: key
                };
                if(window.csrfName && window.csrfHash) {
                    data[window.csrfName] = window.csrfHash;
                }

                $.post('/candidate-documents/remove', data, function(res) {
                    if (res.csrf_hash) window.csrfHash = res.csrf_hash;
                    if (res.status === 'success') {
                        // Reset visual
                        row.find('.doc-thumbnail').removeClass('pdf-icon').addClass('no-file').empty().text('No file');
                        row.find('.status-badge').removeClass('status-uploaded status-review').addClass('status-missing').text('Missing');
                        row.find('.file-name').remove();
                        
                        const actions = row.find('.doc-actions');
                        actions.html(`
                            <input type="file" class="d-none file-input" accept=".pdf,.jpg,.jpeg,.png">
                            <button type="button" class="btn-brand btn-upload" aria-label="Upload">Upload file</button>
                        `);
                        
                        updateProgress();
                    } else {
                        row.find('.error-text').text(res.message).show();
                    }
                });
            }
        });
    });

    // Image Zoom
    $(document).on('click', '.zoomable-image', function() {
        $('#zoomedImage').attr('src', $(this).attr('src'));
        $('#imageZoomModal').modal('show');
    });

    // Update month automatically via ajax
    $(document).on('change', '.row-month-input', function() {
        const row = $(this).closest('.doc-row');
        const key = row.data('key');
        const val = $(this).val();
        const formData = new FormData();
        formData.append('candidate_id', candidateId);
        formData.append(`doc_subtitle_${key}`, val);
        if(window.csrfName && window.csrfHash) {
            formData.append(window.csrfName, window.csrfHash);
        }
        
        $.ajax({
            url: '/candidate-documents/upload',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) { 
                if (response.csrf_hash) window.csrfHash = response.csrf_hash;
                console.log('Month saved'); 
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
