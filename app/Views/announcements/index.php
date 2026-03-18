<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<div class="main-container container-fluid py-4">
    <div class="d-md-flex d-block align-items-center justify-content-between mb-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-bold fs-24 mb-1" style="color: #1e293b;">Notice Board</h1>
            <p class="text-muted mb-0">Stay updated with the latest company news and events</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-primary">Dashboard</a></li>
                    <li class="breadcrumb-item active text-muted" aria-current="page">Announcements</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Filter Row -->
    <div class="row mb-5">
        <div class="col-xl-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <form method="GET" action="/announcements" class="row g-3 align-items-end">
                        <div class="col-xl-4 col-lg-5">
                            <label class="form-label fw-bold text-dark fs-13">Filter by Type</label>
                            <div class="input-group input-filter">
                                <span class="input-group-text bg-white border-end-0"><i class="mdi mdi-filter-variant text-muted"></i></span>
                                <select name="type" class="form-select border-start-0 ps-0">
                                    <option value="">All Categories</option>
                                    <option value="Info" <?= request()->getGet('type') == 'Info' ? 'selected' : '' ?>>ℹ️ Information</option>
                                    <option value="Warning" <?= request()->getGet('type') == 'Warning' ? 'selected' : '' ?>>⚠️ Warning</option>
                                    <option value="Success" <?= request()->getGet('type') == 'Success' ? 'selected' : '' ?>>✅ Success</option>
                                    <option value="Event" <?= request()->getGet('type') == 'Event' ? 'selected' : '' ?>>📅 Event</option>
                                    <option value="Urgent" <?= request()->getGet('type') == 'Urgent' ? 'selected' : '' ?>>🚨 Urgent</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-5">
                            <label class="form-label fw-bold text-dark fs-13">Filter by Date</label>
                            <div class="input-group input-filter">
                                <span class="input-group-text bg-white border-end-0"><i class="mdi mdi-calendar-search text-muted"></i></span>
                                <input type="date" name="date" class="form-control border-start-0 ps-0" value="<?= request()->getGet('date') ?>">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm flex-grow-1" style="border-radius: 10px; background-color: #E66136; border: none;">Apply</button>
                            <a href="/announcements" class="btn btn-light px-3" style="border-radius: 10px;"><i class="mdi mdi-refresh"></i></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcements Cards -->
    <div class="row g-4">
        <?php if (empty($announcements)): ?>
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="mdi mdi-email-open-outline fs-64 text-muted opacity-25"></i>
                        </div>
                        <h4 class="fw-bold" style="color: #64748b;">All Clear!</h4>
                        <p class="text-muted fs-15">There are no announcements for you at this time.</p>
                        <a href="/dashboard" class="btn btn-outline-primary rounded-pill px-4 mt-2">Back to Home</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($announcements as $a): ?>
                <?php 
                    $accentColor = '#E66136';
                    $iconClass = 'mdi mdi-information';
                    $bgClass = 'bg-info-subtle';
                    $textClass = 'text-info';
                    
                    switch($a['type']) {
                        case 'Info': 
                            $accentColor = '#3b82f6'; $iconClass = 'mdi mdi-information-outline'; 
                            $bgClass = 'bg-primary-subtle'; $textClass = 'text-primary'; break;
                        case 'Warning': 
                            $accentColor = '#f59e0b'; $iconClass = 'mdi mdi-alert-circle-outline'; 
                            $bgClass = 'bg-warning-subtle'; $textClass = 'text-warning'; break;
                        case 'Success': 
                            $accentColor = '#10b981'; $iconClass = 'mdi mdi-check-circle-outline'; 
                            $bgClass = 'bg-success-subtle'; $textClass = 'text-success'; break;
                        case 'Event': 
                            $accentColor = '#6366f1'; $iconClass = 'mdi mdi-calendar-star-outline'; 
                            $bgClass = 'bg-indigo-subtle'; $textClass = 'text-indigo'; break;
                        case 'Urgent': 
                            $accentColor = '#ef4444'; $iconClass = 'mdi mdi-alert-octagon-outline'; 
                            $bgClass = 'bg-danger-subtle'; $textClass = 'text-danger'; break;
                    }
                    $isRead = in_array($a['id'], $readIds);
                ?>
                <div class="col-xl-4 col-md-6">
                    <div class="card announcement-user-card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative">
                        <?php if (!$isRead): ?>
                            <span class="badge position-absolute top-0 end-0 m-3 bg-danger rounded-pill shadow-sm" style="z-index: 2; padding: 5px 12px; font-size: 10px;">NEW</span>
                        <?php endif; ?>
                        
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-icon-wrap <?= $bgClass ?> <?= $textClass ?> rounded-3 p-2 me-3">
                                    <i class="<?= $iconClass ?> fs-24"></i>
                                </div>
                                <div>
                                    <span class="text-uppercase fw-bold fs-10 tracking-wider text-muted"><?= $a['type'] ?></span>
                                    <p class="mb-0 text-muted fs-12"><?= date('M d, Y', strtotime($a['start_date'])) ?></p>
                                </div>
                            </div>
                            
                            <h6 class="fw-bold mb-3 announcement-title text-dark-blue"><?= esc($a['title']) ?></h6>
                            
                            <p class="text-muted fs-14 mb-4 description-preview">
                                <?= esc(substr($a['description'], 0, 120)) ?><?= strlen($a['description']) > 120 ? '...' : '' ?>
                            </p>
                            
                            <div class="d-flex align-items-center justify-content-between mt-auto">
                                <span class="text-muted fs-12"><i class="mdi mdi-eye-outline me-1"></i> Read by members</span>
                                <button class="btn btn-read-more read-more-btn px-3 py-1 fw-bold stretched-link" 
                                        data-title="<?= esc($a['title']) ?>" 
                                        data-desc="<?= esc($a['description']) ?>" 
                                        data-id="<?= $a['id'] ?>"
                                        data-attachment="<?= $a['attachment'] ? '/uploads/announcements/'.$a['attachment'] : '' ?>"
                                        data-bs-toggle="modal" data-bs-target="#announcementModal">
                                    Continue <i class="mdi mdi-arrow-right-thin fs-18 align-middle"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Read More Modal -->
<div class="modal fade" id="announcementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-light p-4">
                <div class="d-flex align-items-center">
                    <div class="avatar-md bg-primary-transparent rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                        <i class="mdi mdi-bullhorn fs-20 text-primary"></i>
                    </div>
                    <h6 class="modal-title fw-bold fs-18 mb-0" id="announcementModalLabel">Announcement Details</h6>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <h4 id="modalTitle" class="fw-bold text-dark-blue mb-2"></h4>
                    <span id="modalDate" class="text-muted fs-13"><i class="mdi mdi-calendar-clock-outline me-1"></i> Posted on: <span></span></span>
                </div>
                
                <div class="announcement-content-scroll bg-white p-3 rounded-3 border-light" style="max-height: 400px; overflow-y: auto;">
                    <div id="modalDescription" class="fs-15 text-muted lh-base" style="white-space: pre-wrap;"></div>
                </div>

                <div id="modalAttachmentDiv" class="mt-4 p-3 rounded-3 bg-light border-start border-primary border-4" style="display:none;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-paperclip fs-20 text-primary me-2"></i>
                            <div>
                                <p class="fw-bold mb-0 fs-14">Supporting Document</p>
                                <p class="text-muted mb-0 fs-12">Click the button to download or view</p>
                            </div>
                        </div>
                        <a id="modalAttachmentLink" href="#" target="_blank" class="btn btn-sm btn-primary px-3 rounded-pill">
                            <i class="mdi mdi-download me-1"></i> View File
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light px-4 fw-bold rounded-pill" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    .text-dark-blue { color: #1e293b; }
    .announcement-title { font-size: 1.1rem; line-height: 1.4; transition: color 0.3s ease; }
    .announcement-user-card { transition: all 0.3s ease; }
    .announcement-user-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important; }
    .announcement-user-card:hover .announcement-title { color: #E66136; }
    
    .avatar-icon-wrap { width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; }
    .tracking-wider { letter-spacing: 0.05em; }
    .fs-10 { font-size: 0.625rem; }
    
    .input-filter { border-radius: 10px; overflow: hidden; border: 1px solid #e2e8f0; }
    .input-filter .form-select, .input-filter .form-control { border: none; padding: 10px; }
    
    .btn-read-more { color: #E66136; padding: 0; background: transparent; transition: all 0.2s ease; border: none; font-size: 14px; }
    .btn-read-more:hover { color: #c2410c; padding-right: 5px; }
    
    .description-preview {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;  
        overflow: hidden;
        min-height: 3.6em;
    }
    
    .bg-indigo-subtle { background-color: rgba(99, 102, 241, 0.1); }
    .text-indigo { color: #6366f1; }
    
    /* Scrollbar for modal content */
    .announcement-content-scroll::-webkit-scrollbar { width: 6px; }
    .announcement-content-scroll::-webkit-scrollbar-track { background: #f1f1f1; }
    .announcement-content-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .announcement-content-scroll::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>

<script>
$(document).ready(function() {
    $(document).on('click', '.read-more-btn', function() {
        var btn = $(this);
        var id = btn.attr('data-id');
        var title = btn.attr('data-title');
        var desc = btn.attr('data-desc');
        var attachment = btn.attr('data-attachment');

        // Target modal elements
        var $modalTitle = $('#modalTitle');
        var $modalDesc = $('#modalDescription');
        var $modalAttachDiv = $('#modalAttachmentDiv');
        var $modalAttachLink = $('#modalAttachmentLink');

        // Clear previous content
        $modalTitle.text('');
        $modalDesc.text('');

        // Set title and description
        $modalTitle.text(title || 'No Title');
        $modalDesc.text(desc || 'No Description');

        // Handle attachment
        if (attachment && attachment.trim() !== '' && attachment !== '/uploads/announcements/') {
            $modalAttachLink.attr('href', attachment);
            $modalAttachDiv.fadeIn();
        } else {
            $modalAttachDiv.hide();
        }

        // Mark as read via AJAX
        $.ajax({
            url: '/announcements/mark-read/' + id,
            type: 'POST'
        });
    });
});
</script>

<?= $this->endSection(); ?>
