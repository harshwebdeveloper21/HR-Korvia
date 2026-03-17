<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
      @media (max-width: 767px) {
    .interviewsmbtn{
     font-size: 10px !important;
    padding: 8px !important;
    margin-top: 10px !important;
}
 .cart-sm-titles{
            font-size: 15px !important;
            margin-bottom: 5px !important;
        }
        .iconfontsize{
    font-size: 11px !important;
}
    }
</style>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">

                <!-- Header: Title + Back Button -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-1">Template</h4>
                    </div>

                    <a href="/exprience-templates-view" class="btn hr-btnbg interviewsmbtn" style="white-space:nowrap;">
                        <i class="mdi mdi-arrow-left iconfontsize"></i> All Templates
                    </a>
                </div>

                <?php if (!empty($templates)): ?>
                    <!-- Template Title -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark">
                            <i class="mdi mdi-file-document-outline me-1"></i>
                            <?= esc($templates["title"]) ?>
                        </h5>
                    </div>

                    <!-- Template Content -->
                    <div class="template-preview border rounded shadow-sm p-4 bg-white">
                        <div class="template-content">
                            <?= $templates[
                                "content"
                            ] ?> <!-- Render as raw HTML -->
                        </div>
                    </div>
                <?php else: ?>
                    <p>No templates found.</p>
                <?php endif; ?>

                <div id="responseMessage" class="mt-3"></div>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
