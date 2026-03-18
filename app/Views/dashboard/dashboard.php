<?= $this->extend('layout'); ?>

<?= $this->section('content'); ?>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://cdn.jsdelivr.net/npm/progressbar.js"></script>


<style>
    .capitalize-text {
        text-transform: capitalize;
    }

    .card .card-body {
        padding: 1rem 1rem;
    }

    .dropdown-button-wrapper {
        position: relative;
        display: inline-block;
    }

    .filter-icon-inside {
        position: absolute;
        left: 13px;
        top: 43%;
        transform: translateY(-50%);
        pointer-events: none;
        color: #E66136;
    }

    .dropdown-button-wrapper button {
        padding-left: 36px;
    }

    #departmentChartWrapper canvas {
        max-width: 220px;
        height: auto;
    }

    .legend-value {
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }

    .legend-label {
        font-size: 14px;
    }

    .bg-wrks {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }

    /* Optional: Ensure chart and legend have spacing on small screens */
    .thismonth {
        justify-content: end;
        display: flex;
    }

    .card .card-title.card-title-dash {
        font-size: 14px !important;
        font-weight: 600 !important;
    }

    #marketingOverview-legend ul li,
    #marketingOverviewPurple-legend ul li,
    #marketingOverviewPurple-dark-legend ul li {
        font-size: 9px;
    }

    .no-data-message {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 14px;
        color: rgb(107, 119, 140);
        font-weight: bold;
        text-align: center;
    }

    .todayleave {
        padding-right: 30px !important;
    }

    #workingFormats {
        width: 150px;
        height: 150px;
        margin: auto;
    }

    .home-tab .dropdown .btn {
        border: none;
        font-weight: bold;
        font-size: 13px;
        line-height: 18px;
        margin-top: -6px;
        margin-bottom: 0px;
        width: 150px;
        padding: 12px 36px;
    }

    .chartjs-bar-wrapper.mt-3 {
        height: 265px;
    }

    @media (min-width: 375px) and (max-width: 667px) {
        /* .doughnutChart{
            max-width: 250px !important;
            max-height: 250px !important;
        } */
    }

    @media (max-width: 767px) {

        #marketingOverview-legend ul li,
        #marketingOverviewPurple-legend ul li,
        #marketingOverviewPurple-dark-legend ul li {


            font-size: 9px !important;
        }

        #marketingOverview-legend ul li span,
        #marketingOverviewPurple-legend ul li span,
        #marketingOverviewPurple-dark-legend ul li span {
            width: 10px !important;
            height: 10px !important;

        }

        #departmentChartWrapper .col-12 {
            margin-bottom: 1rem;
        }

        .thismonth {
            justify-content: unset !important;
            display: block !important;
            margin-top: 0px !important;


        }

        .filter-icon-inside {
            position: absolute;
            left: 40% !important;
            top: 51%;
            transform: translate(-50%, -60%);
            pointer-events: none;
            /* Prevent icon from blocking button clicks */
            font-size: 10px !important;
            color: #E66136;
            /* Muted color */
        }

        .butwidth {
            width: 100% !important;
        }

        .thismonthres {
            width: 100% !important;
        }

        .card-title {
            font-size: 0.85rem;
        }

        .rate-percentage {
            font-size: 16px !important;
        }

        .icon i {
            font-size: 20px !important;
        }

        .smbox {
            display: block !important;
            justify-content: unset !important;
            flex-direction: row !important;
            height: 0% !important;
            padding-top: 16px !important;
        }

        .smheghit {
            height: 86% !important;
        }

        .cart-res {
            margin-bottom: 8px !important;
            font-size: 12px !important;
            font-weight: 600 !important;

        }

        .grid-margin {
            margin-bottom: 0px !important;
        }

        .iconsize {
            width: 35px !important;
            height: 35px !important;
        }

        .smmargin {
            margin-top: 0px !important;
        }

        .smtopmring {
            margin-top: 2px !important;
        }

        .margindes {
            margin-top: -26px !important;
            padding-top: 0px !important;
        }

        .margincon {
            padding-top: 4px !important;
        }

        .piechartsm {
            font-size: 17px !important;

        }

        .font-size-label {
            font-size: 11px !important;
        }

        .sm-grid-margin {
            margin-bottom: 1.5rem !important;
        }

        .sm-bar-chart-size {
            font-size: 13px !important;


        }

        .dataTables_length {
            margin-left: .1rem !important;
            margin-bottom: .5rem !important;
            font-size: 12px !important;
            float: left !important;
        }

        .dataTables_filter {
            font-size: 12px !important;
            /* margin-left: -3rem !important;  */
            float: left !important;
        }

        .col-sm-12.col-md-6 {
            flex: 0 0 25%;
            max-width: 26%;
        }

        .dataTables_filter label:before {
            content: "" !important;
        }

        .btn-res-sm-isze {
            width: 100% !important;
        }


        #order-listing_length label {
            display: flex;
            align-items: center;
        }

        #orders_length label {
            display: flex;
            align-items: center;
        }

        .custom-select {
            height: 26px !important;
            width: 57px !important;
        }

        /* Hide the text inside the label */
        #order-listing_length::first-text,
        #order-listing_length label::before {
            display: none !important;
        }

        #orders_length::first-text,
        #orders_length label::before {
            display: none !important;
        }

        /* Or a simpler and reliable trick */
        #order-listing_length label {
            font-size: 0;
            /* hide text */
        }

        #orders_length label {
            font-size: 0;
            /* hide text */
        }

        #order-listing_length label input {
            font-size: 10px;
            width: 160px !important;
            height: 20px !important;
            /* reset font size for input */
        }

        #orders_length label input {
            font-size: 10px;
            width: 160px !important;
            height: 20px !important;
            /* reset font size for input */
        }

        #order-listing_length label {
            font-size: 0;
            /* hide all text inside the label */
        }

        #orders_length label {
            font-size: 0;
            /* hide all text inside the label */
        }

        #order-listing_length label select {
            font-size: 14px;
            /* restore font size for the dropdown */
        }

        #orders_length label select {
            font-size: 14px;
            /* restore font size for the dropdown */
        }

        div.dataTables_wrapper div.dataTables_filter input {
            margin-left: 0.5em;
            display: inline-block;
            width: 212px !important;
            height: 26px !important
        }

        .home-tab .dropdown .btn {
            font-size: 12px !important;
        }
    }

    @media (min-width: 768px) and (max-width: 1023px) {

        .card.card-rounded.dotChart {
            height: 288px;
        }

        .chartjs-bar-wrapper.mt-3 {
            height: 241px;
        }

        .legend-label {
            font-size: 11px !important;
        }

        .card .card-title.card-title-dash {
            font-style: normal;
            /* font-weight: 800; */
            font-size: 11px !important;
            line-height: 22px;
            color: #1F1F1F;
        }

        a.btn.btn-sm.rounded.border-0 {
            padding: 5.2px !important;
            font-size: 9px;
        }

        img.bday {
            width: 10px !important;
        }

        .tab-content {
            text-align: left !important;
        }

        #marketingOverview-legend ul li,
        #marketingOverviewPurple-legend ul li,
        #marketingOverviewPurple-dark-legend ul li {
            list-style: none;
            color: #737F8B;
            font-size: 11px;
            display: inline-block;
            margin-left: 0.3rem;
        }

        div.dataTables_wrapper div.dataTables_filter input {
            margin-left: 0.5em;
            display: inline-block;
            width: 187px !important;
            height: 26px !important
        }

    }

    /* ==========================================
       Latest Announcements — Premium Design
    ========================================== */
    .ann-section-wrapper {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 20px rgba(0,0,0,0.06);
        padding: 24px;
        margin-bottom: 24px;
    }
    .ann-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .ann-section-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .ann-section-title .ann-icon-wrap {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #E66136, #f59e0b);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .ann-section-title .ann-icon-wrap i {
        color: #fff;
        font-size: 1.1rem;
    }
    .ann-view-all {
        color: #E66136;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 6px 14px;
        border: 1.5px solid rgba(230, 97, 54, 0.3);
        border-radius: 20px;
        transition: all 0.2s;
    }
    .ann-view-all:hover {
        background: #E66136;
        color: #fff;
        border-color: #E66136;
    }

    /* Horizontal scroll container */
    .ann-scroll-track {
        display: flex;
        gap: 16px;
        overflow-x: auto;
        padding-bottom: 8px;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
    }
    .ann-scroll-track::-webkit-scrollbar { height: 5px; }
    .ann-scroll-track::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
    .ann-scroll-track::-webkit-scrollbar-thumb { background: #E66136; border-radius: 10px; }

    /* Individual announcement card */
    .ann-card {
        flex: 0 0 220px;
        border-radius: 12px;
        padding: 14px 16px;
        position: relative;
        overflow: hidden;
        cursor: pointer;
        transition: transform 0.28s ease, box-shadow 0.28s ease;
        border: 1.5px solid transparent;
    }
    .ann-card::before {
        content: '';
        position: absolute;
        top: -30px;
        right: -30px;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        opacity: 0.15;
        transition: opacity 0.3s;
    }
    .ann-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.10);
    }
    .ann-card:hover::before { opacity: 0.25; }

    /* Color variants */
    .ann-info  { background: linear-gradient(135deg, #e0f2fe, #bae6fd); border-color: #7dd3fc; }
    .ann-info::before  { background: #0ea5e9; }
    .ann-info .ann-card-icon  { color: #0284c7; background: rgba(2,132,199,0.12); }
    .ann-info .ann-card-title { color: #075985; }
    .ann-info .ann-card-meta  { color: #0369a1; }
    .ann-info .ann-card-desc  { color: #0c4a6e; }
    .ann-info .ann-card-badge { background: rgba(2,132,199,0.15); color: #0284c7; }

    .ann-warning { background: linear-gradient(135deg, #fef9c3, #fef08a); border-color: #fde047; }
    .ann-warning::before { background: #eab308; }
    .ann-warning .ann-card-icon  { color: #ca8a04; background: rgba(202,138,4,0.12); }
    .ann-warning .ann-card-title { color: #713f12; }
    .ann-warning .ann-card-meta  { color: #92400e; }
    .ann-warning .ann-card-desc  { color: #78350f; }
    .ann-warning .ann-card-badge { background: rgba(202,138,4,0.15); color: #ca8a04; }

    .ann-success { background: linear-gradient(135deg, #dcfce7, #bbf7d0); border-color: #86efac; }
    .ann-success::before { background: #16a34a; }
    .ann-success .ann-card-icon  { color: #15803d; background: rgba(21,128,61,0.12); }
    .ann-success .ann-card-title { color: #14532d; }
    .ann-success .ann-card-meta  { color: #166534; }
    .ann-success .ann-card-desc  { color: #052e16; }
    .ann-success .ann-card-badge { background: rgba(21,128,61,0.15); color: #15803d; }

    .ann-urgent  { background: linear-gradient(135deg, #ffe4e6, #fecdd3); border-color: #fda4af; }
    .ann-urgent::before  { background: #e11d48; }
    .ann-urgent .ann-card-icon  { color: #be123c; background: rgba(190,18,60,0.12); }
    .ann-urgent .ann-card-title { color: #881337; }
    .ann-urgent .ann-card-meta  { color: #9f1239; }
    .ann-urgent .ann-card-desc  { color: #4c0519; }
    .ann-urgent .ann-card-badge { background: rgba(190,18,60,0.15); color: #be123c; }

    .ann-event  { background: linear-gradient(135deg, #ede9fe, #ddd6fe); border-color: #c4b5fd; }
    .ann-event::before  { background: #7c3aed; }
    .ann-event .ann-card-icon  { color: #6d28d9; background: rgba(109,40,217,0.12); }
    .ann-event .ann-card-title { color: #4c1d95; }
    .ann-event .ann-card-meta  { color: #5b21b6; }
    .ann-event .ann-card-desc  { color: #2e1065; }
    .ann-event .ann-card-badge { background: rgba(109,40,217,0.15); color: #6d28d9; }

    /* Card internals */
    .ann-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .ann-card-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .ann-card-badge {
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.04em;
        padding: 2px 7px;
        border-radius: 20px;
        text-transform: uppercase;
    }
    .ann-card-new {
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.04em;
        padding: 2px 7px;
        border-radius: 20px;
        background: #ef4444;
        color: #fff;
        animation: pulse-new 1.5s ease-in-out infinite;
    }
    @keyframes pulse-new {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.55; }
    }
    .ann-card-title {
        font-size: 0.83rem;
        font-weight: 700;
        line-height: 1.35;
        margin-bottom: 5px;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .ann-card-meta {
        font-size: 0.7rem;
        opacity: 0.75;
        display: flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 8px;
    }
    .ann-card-desc {
        font-size: 0.76rem;
        line-height: 1.45;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        opacity: 0.82;
    }
    .ann-card-footer {
        margin-top: 10px;
        padding-top: 8px;
        border-top: 1px solid rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.72rem;
        font-weight: 600;
    }
    .ann-card-footer-read {
        display: flex;
        align-items: center;
        gap: 4px;
        opacity: 0.7;
        transition: opacity 0.2s;
    }
    .ann-card:hover .ann-card-footer-read { opacity: 1; }

    /* ── Marquee ticker ── */
    .ann-marquee-outer {
        overflow: hidden;          /* clip cards outside viewport */
        position: relative;
        width: 100%;
    }
    /* Subtle fade on right edge */
    .ann-marquee-outer::after {
        content: '';
        position: absolute;
        top: 0; right: 0;
        width: 80px; height: 100%;
        background: linear-gradient(to left, #fff 30%, transparent);
        pointer-events: none;
        z-index: 3;
    }

    /* ── Announcement Cards Row ── */
    .ann-marquee-outer {
        position: relative;
        width: 100%;
    }
    /* Fade right edge when scrollable */
    .ann-marquee-outer::after {
        content: '';
        position: absolute;
        top: 0; right: 0;
        width: 80px; height: 100%;
        background: linear-gradient(to left, #fff 30%, transparent);
        pointer-events: none;
        z-index: 3;
    }
    /* Scrollable flex row — width:100% so overflow clips clones naturally */
    .ann-marquee-track {
        display: flex;
        gap: 14px;
        overflow-x: scroll;
        width: 100%;
        scroll-behavior: auto;   /* MUST be auto — smooth interferes with JS */
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    .ann-marquee-track::-webkit-scrollbar { display: none; }

    @media (max-width: 768px) {
        .ann-card { flex: 0 0 190px; }
    }
</style>

<div class="row">

    <div class="col-sm-12">

        <div class="home-tab">

            <!-- <div class="d-sm-flex align-items-center justify-content-between border-bottom">

            </div> -->

            <!-- Dropdown Filter for This Week / This Month -->

            <div class="thismonth mt-2">

                <div class="dropdown dropdown-button-wrapper btn-res-sm-isze">
                    <!-- Icon positioned absolutely inside the button -->
                    <i class="mdi mdi-filter-variant mdi-24px filter-icon-inside"></i>

                    <button class="btn border dropdown-toggle text-muted butwidth px-0 mx-0 ps-5 ps-lg-3"
                        type="button"
                        id="filterDropdownBtn"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        style="background-color: white;">
                        <span>This Month</span>
                    </button>

                    <ul class="dropdown-menu thismonthres" aria-labelledby="filterDropdownBtn">
                        <li><a class="dropdown-item" href="#" onclick="selectFilter('This Week', 'week')">This Week</a></li>
                        <li><a class="dropdown-item" href="#" onclick="selectFilter('This Month', 'month')">This Month</a></li>
                        <li><a class="dropdown-item" href="#" onclick="selectFilter('This Year', 'year')">This Year</a></li>
                    </ul>
                </div>

            </div>

        </div>

    </div>

</div>



<div class="row">

    <div class="col-sm-12">

        <div class="home-tab">

            <div class="tab-content tab-content-basic">

                <!-- Latest Announcements — Premium Section -->
                <?php if (!empty($activeAnnouncements)): ?>
                <div class="ann-section-wrapper">
                    <div class="ann-section-header">
                        <h3 class="ann-section-title">
                            <span class="ann-icon-wrap">
                                <i class="mdi mdi-bullhorn-variant"></i>
                            </span>
                            Latest Announcements
                            <span class="badge ms-2" style="background: rgba(230,97,54,0.12); color: #E66136; font-size: 11px; font-weight: 700; border-radius: 20px;">
                                <?= count($activeAnnouncements) ?>
                            </span>
                        </h3>
                        <a href="/announcements" class="ann-view-all">
                            View All <i class="mdi mdi-arrow-right"></i>
                        </a>
                    </div>

                    <!-- Infinite CSS marquee ticker -->
                    <div class="ann-marquee-outer">
                        <div class="ann-marquee-track" id="announcementContainer">
                            <?php foreach ($activeAnnouncements as $ann): ?>
                                <?php
                                    $t = strtolower($ann['type'] ?? 'info');
                                    if ($t === 'warning')     { $cls = 'ann-warning'; $ico = 'mdi-alert-circle-outline';  $label = 'Warning'; }
                                    elseif ($t === 'success') { $cls = 'ann-success'; $ico = 'mdi-check-circle-outline';  $label = 'Success'; }
                                    elseif ($t === 'urgent')  { $cls = 'ann-urgent';  $ico = 'mdi-alert-octagon-outline'; $label = 'Urgent'; }
                                    elseif ($t === 'event')   { $cls = 'ann-event';   $ico = 'mdi-calendar-star-outline'; $label = 'Event'; }
                                    else                      { $cls = 'ann-info';    $ico = 'mdi-information-outline';   $label = 'Info'; }
                                    $isNew = !in_array($ann['id'], $readIds ?? []);
                                ?>
                                <div class="ann-card <?= $cls ?>" onclick="showAnnouncement(<?= esc(json_encode($ann)) ?>)">
                                    <div class="ann-card-top">
                                        <div class="ann-card-icon"><i class="mdi <?= $ico ?> fs-20"></i></div>
                                        <div class="d-flex flex-column align-items-end gap-1">
                                            <span class="ann-card-badge"><?= $label ?></span>
                                            <?php if ($isNew): ?><span class="ann-card-new">NEW</span><?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="ann-card-title"><?= esc($ann['title']) ?></div>
                                    <div class="ann-card-meta">
                                        <i class="mdi mdi-calendar-clock-outline"></i>
                                        <?= date('d M Y', strtotime($ann['start_date'])) ?> &mdash; <?= date('d M Y', strtotime($ann['end_date'])) ?>
                                    </div>
                                    <p class="ann-card-desc"><?= esc(strip_tags($ann['description'])) ?></p>
                                    <div class="ann-card-footer">
                                        <span class="ann-card-footer-read"><i class="mdi mdi-eye-outline"></i> Click to read</span>
                                        <i class="mdi mdi-arrow-right"></i>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview">

                    <div id="weekSection">

                        <div class="row">

                            <div class="col-sm-12">

                                <div class="statistics-details mb-0">

                                    <div class="row g-3">

                                        <?php if ($role == 'admin' || $role == 'hr') : ?>

                                            <!-- Total Employees Section visible only to Admin and HR -->


                                            <div class="col-6 col-md-3 grid-margin smtopmring">
                                                <div class="card shadow-sm border-0 rounded-4 h-100 bg-inverse-warning smheghit" style="cursor: pointer;" onclick="window.location.href='/empview'">
                                                    <div class="card-body d-flex flex-column justify-content-between h-100 smbox">
                                                        <!-- First row: Title only -->
                                                        <p class="card-title card-title-dash fw-medium mb-3 text-start cart-res" style="font-size: 16px;">All Employees</p>

                                                        <!-- Second row: Count and icon side by side -->
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <h4 class="rate-percentage fw-bold mb-0" id="this-week-employees" style="font-size: 26px;">3</h4>
                                                            <div class="icon rounded-circle d-flex align-items-center justify-content-center iconsize"
                                                                style="width: 48px; height: 48px; background-color: #f4f5f7; color: #E66136;">
                                                                <i class="mdi mdi-account-group mdi-24px"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        <?php endif; ?>



                                        <?php
                                        $columnClass = ($role == 'employee') ? 'col-6 col-md-4' : 'col-6 col-md-3';
                                        ?>
                                        <?php
                                        $columnClassNew = ($role == 'employee') ? 'col-12 col-md-4' : 'col-6 col-md-3';
                                        ?>
                                        <!-- Number of Leaves Section -->

                                        <div class="<?= $columnClass ?> grid-margin smtopmring">

                                            <div class="card shadow-sm border-0 rounded-4 h-100 bg-danger-subtle smheghit" style="cursor: pointer;" onclick="window.location.href='/leaveview'">
                                                <div class="card-body d-flex flex-column justify-content-between h-100 smbox">
                                                    <!-- First row: Title only -->
                                                    <p class="card-title card-title-dash fw-medium mb-3 text-start cart-res" style="font-size: 16px;">All Leaves</p>

                                                    <!-- Second row: Count and icon side by side -->
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h4 class="rate-percentage fw-bold mb-0" id="this-week-leaves" style="font-size: 26px;">0</h4>
                                                        <div class="icon rounded-circle d-flex align-items-center justify-content-center iconsize"
                                                            style="width: 48px; height: 48px; background-color: #f4f5f7; color: #E66136;">
                                                            <i class="mdi mdi-calendar-check mdi-24px"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Attendance Today Section -->


                                        <div class="<?= $columnClass ?> grid-margin smmargin">

                                            <div class="card shadow-sm border-0 rounded-4 h-100 bg-success-subtle smheghit" style="cursor: pointer;" onclick="window.location.href='/view-calendar'">
                                                <div class="card-body d-flex flex-column justify-content-between h-100 smbox">
                                                    <!-- First row: Title only -->
                                                    <p class="card-title card-title-dash fw-medium mb-3 text-start cart-res" style="font-size: 16px;">All Attendance</p>

                                                    <!-- Second row: Count and icon side by side -->
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h4 class="rate-percentage fw-bold mb-0" id="this-week-attendance" style="font-size: 26px;">3</h4>
                                                        <div class="icon rounded-circle d-flex align-items-center justify-content-center iconsize"
                                                            style="width: 48px; height: 48px; background-color: #f4f5f7; color: #E66136;">
                                                            <i class="mdi mdi-account-check mdi-24px"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Total Tasks Section -->


                                        <div class="<?= $columnClassNew ?> grid-margin smmargin">

                                            <div class="card shadow-sm border-0 rounded-4 h-100 bg-inverse-success smheghit" style="cursor: pointer;" onclick="window.location.href='/taskview'">
                                                <div class="card-body d-flex flex-column justify-content-between h-100 smbox">
                                                    <!-- First row: Title only -->
                                                    <p class="card-title card-title-dash fw-medium mb-3 text-start cart-res" style="font-size: 16px;">All Tasks</p>

                                                    <!-- Second row: Count and icon side by side -->
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h4 class="rate-percentage fw-bold mb-0" id="this-week-task" style="font-size: 26px;">3</h4>
                                                        <div class="icon rounded-circle d-flex align-items-center justify-content-center iconsize"
                                                            style="width: 48px; height: 48px; background-color: #f4f5f7; color: #E66136;">
                                                            <i class="mdi mdi-format-list-checks mdi-24px"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- this month data -->

                    <div id="monthSection" style="display: none;">


                        <div class="row">

                            <div class="col-sm-12">

                                <div class="statistics-details mb-0">

                                    <div class="row g-3">

                                        <?php if ($role == 'admin' || $role == 'hr') : ?>

                                            <!-- Total Employees Section visible only to Admin and HR -->


                                            <div class="col-6 col-md-3 grid-margin smtopmring">
                                                <div class="card shadow-sm border-0 rounded-4 h-100 bg-inverse-warning smheghit" style="cursor: pointer;" onclick="window.location.href='/empview'">
                                                    <div class="card-body d-flex flex-column justify-content-between h-100 smbox">
                                                        <!-- First row: Title only -->
                                                        <p class="card-title card-title-dash fw-medium mb-3 text-start cart-res" style="font-size: 16px;">All Employees</p>

                                                        <!-- Second row: Count and icon side by side -->
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <h4 class="rate-percentage fw-bold mb-0" id="this-month-employees" style="font-size: 26px;">0</h4>
                                                            <div class="icon rounded-circle d-flex align-items-center justify-content-center iconsize"
                                                                style="width: 48px; height: 48px; background-color: #f4f5f7; color: #E66136;">
                                                                <i class="mdi mdi-account-group mdi-24px"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        <?php endif; ?>



                                        <?php
                                        $columnClass = ($role == 'employee') ? 'col-6 col-md-4' : 'col-6 col-md-3';
                                        ?>
                                        <?php
                                        $columnClassNew = ($role == 'employee') ? 'col-12 col-md-4' : 'col-6 col-md-3';
                                        ?>

                                        <div class="<?= $columnClass ?> grid-margin smtopmring">

                                            <div class="card shadow-sm border-0 rounded-4 h-100 bg-danger-subtle smheghit" style="cursor: pointer;" onclick="window.location.href='/view-calendar'">
                                                <div class="card-body d-flex flex-column justify-content-between h-100 smbox">
                                                    <!-- First row: Title only -->
                                                    <p class="card-title card-title-dash fw-medium mb-3 text-start cart-res" style="font-size: 16px;">All Leaves</p>

                                                    <!-- Second row: Count and icon side by side -->
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h4 class="rate-percentage fw-bold mb-0" id="this-month-leaves" style="font-size: 26px;">0</h4>
                                                        <div class="icon rounded-circle d-flex align-items-center justify-content-center iconsize"
                                                            style="width: 48px; height: 48px; background-color: #f4f5f7; color: #E66136;">
                                                            <i class="mdi mdi-calendar-check mdi-24px"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Attendance Today Section -->

                                        <div class="<?= $columnClass ?> grid-margin smmargin">

                                            <div class="card shadow-sm border-0 rounded-4 h-100 bg-success-subtle smheghit" style="cursor: pointer;" onclick="window.location.href='/view-calendar'">
                                                <div class="card-body d-flex flex-column justify-content-between h-100 smbox">
                                                    <!-- First row: Title only -->
                                                    <p class="card-title card-title-dash fw-medium mb-3 text-start cart-res" style="font-size: 16px;">All Attendance</p>

                                                    <!-- Second row: Count and icon side by side -->
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h4 class="rate-percentage fw-bold mb-0" id="this-month-attendance" style="font-size: 26px;">3</h4>
                                                        <div class="icon rounded-circle d-flex align-items-center justify-content-center iconsize"
                                                            style="width: 48px; height: 48px; background-color: #f4f5f7; color: #E66136;">
                                                            <i class="mdi mdi-account-check mdi-24px"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Total Tasks Section -->

                                        <div class="<?= $columnClassNew ?> grid-margin smmargin">

                                            <div class="card shadow-sm border-0 rounded-4 h-100 bg-inverse-success smheghit" style="cursor: pointer;" onclick="window.location.href='/taskview'">
                                                <div class="card-body d-flex flex-column justify-content-between h-100 smbox">
                                                    <!-- First row: Title only -->
                                                    <p class="card-title card-title-dash fw-medium mb-3 text-start cart-res" style="font-size: 16px;">All Tasks</p>

                                                    <!-- Second row: Count and icon side by side -->
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h4 class="rate-percentage fw-bold mb-0" id="this-month-task" style="font-size: 26px;">0</h4>
                                                        <div class="icon rounded-circle d-flex align-items-center justify-content-center iconsize"
                                                            style="width: 48px; height: 48px; background-color: #f4f5f7; color: #E66136;">
                                                            <i class="mdi mdi-format-list-checks mdi-24px"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                    <div id="yearSection" style="display: none;">



                        <div class="row">

                            <div class="col-sm-12">

                                <div class="statistics-details mb-0">

                                    <div class="row g-3">

                                        <?php if ($role == 'admin' || $role == 'hr') : ?>

                                            <!-- Total Employees Section visible only to Admin and HR -->


                                            <div class="col-6 col-md-3 grid-margin smtopmring">
                                                <div class="card shadow-sm border-0 rounded-4 h-100 bg-inverse-warning smheghit" style="cursor: pointer;" onclick="window.location.href='/empview'">
                                                    <div class="card-body d-flex flex-column justify-content-between h-100 smbox">
                                                        <!-- First row: Title only -->
                                                        <p class="card-title card-title-dash fw-medium mb-3 text-start cart-res" style="font-size: 16px;">All Employees</p>

                                                        <!-- Second row: Count and icon side by side -->
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <h4 class="rate-percentage fw-bold mb-0" id="this-year-employees" style="font-size: 26px;">0</h4>
                                                            <div class="icon rounded-circle d-flex align-items-center justify-content-center iconsize"
                                                                style="width: 48px; height: 48px; background-color: #f4f5f7; color: #E66136;">
                                                                <i class="mdi mdi-account-group mdi-24px"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        <?php endif; ?>



                                        <?php
                                        $columnClass = ($role == 'employee') ? 'col-6 col-md-4' : 'col-6 col-md-3';
                                        ?>
                                        <?php
                                        $columnClassNew = ($role == 'employee') ? 'col-12 col-md-4' : 'col-6 col-md-3';
                                        ?>


                                        <!-- Number of Leaves Section -->
                                        <div class="<?= $columnClass ?> grid-margin smtopmring">

                                            <div class="card shadow-sm border-0 rounded-4 h-100 bg-danger-subtle smheghit" style="cursor: pointer;" onclick="window.location.href='/leaveview'">
                                                <div class="card-body d-flex flex-column justify-content-between h-100 smbox">
                                                    <!-- First row: Title only -->
                                                    <p class="card-title card-title-dash fw-medium mb-3 text-start cart-res" style="font-size: 16px;">All Leaves</p>

                                                    <!-- Second row: Count and icon side by side -->
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h4 class="rate-percentage fw-bold mb-0" id="this-year-leaves" style="font-size: 26px;">0</h4>
                                                        <div class="icon rounded-circle d-flex align-items-center justify-content-center iconsize"
                                                            style="width: 48px; height: 48px; background-color: #f4f5f7; color: #E66136;">
                                                            <i class="mdi mdi-calendar-check mdi-24px"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Attendance Today Section -->


                                        <div class="<?= $columnClass ?> grid-margin smmargin">

                                            <div class="card shadow-sm border-0 rounded-4 h-100 bg-success-subtle smheghit" style="cursor: pointer;" onclick="window.location.href='/view-calendar'">
                                                <div class="card-body d-flex flex-column justify-content-between h-100 smbox">
                                                    <!-- First row: Title only -->
                                                    <p class="card-title card-title-dash fw-medium mb-3 text-start cart-res" style="font-size: 16px;">All Attendance</p>

                                                    <!-- Second row: Count and icon side by side -->
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h4 class="rate-percentage fw-bold mb-0" id="this-year-attendance" style="font-size: 26px;">0</h4>
                                                        <div class="icon rounded-circle d-flex align-items-center justify-content-center iconsize"
                                                            style="width: 48px; height: 48px; background-color: #f4f5f7; color: #E66136;">
                                                            <i class="mdi mdi-account-check mdi-24px"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Total Tasks Section -->


                                        <div class="<?= $columnClassNew ?> grid-margin smmargin">

                                            <div class="card shadow-sm border-0 rounded-4 h-100 bg-inverse-success smheghit" style="cursor: pointer;" onclick="window.location.href='/taskview'">
                                                <div class="card-body d-flex flex-column justify-content-between h-100 smbox">
                                                    <!-- First row: Title only -->
                                                    <p class="card-title card-title-dash fw-medium mb-3 text-start cart-res" style="font-size: 16px;">All Tasks</p>

                                                    <!-- Second row: Count and icon side by side -->
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h4 class="rate-percentage fw-bold mb-0" id="this-year-task" style="font-size: 26px;">0</h4>
                                                        <div class="icon rounded-circle d-flex align-items-center justify-content-center iconsize"
                                                            style="width: 48px; height: 48px; background-color: #f4f5f7; color: #E66136;">
                                                            <i class="mdi mdi-format-list-checks mdi-24px"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="row">



                        <?php if ($role == 'admin' || $role == 'hr') : ?>

                            <div class="col-xl-4 col-lg-6 d-flex flex-column grid-margin sm-grid-margin stretch-card">

                                <div class="card card-rounded todaybday">

                                    <div class="card-body">


                                        <div class="d-flex justify-content-between align-items-start align-items-sm-center">
                                            <h4 class="card-title card-title-dash sm-bar-chart-size mb-0">Today's Present (<span id="attendanceCount">0</span>)</h4>
                                            <a href="\view-calendar" class="btn btn-sm rounded border-0 mb-0" style="background:#E66136;color:white;white-space:nowrap;">View All</a>
                                        </div>

                                        <div id="todayAttendanceList" class="mt-3" style="max-height: 250px; overflow-y: auto;">

                                            <div class="d-flex align-items-center justify-content-center text-center p-4">

                                                <h5 class="text-muted txtclr">Loading...</h5>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <div class="col-xl-4 col-lg-6 d-flex flex-column grid-margin sm-grid-margin stretch-card">

                                <div class="card card-rounded todaybday">

                                    <div class="card-body">


                                        <div class="d-flex justify-content-between align-items-start align-items-sm-center">
                                            <h4 class="card-title card-title-dash sm-bar-chart-size mb-0">Today's Absent Or Leave (<span id="leaveCount">0</span>)</h4>
                                            <a href="\leaveview" class="btn btn-sm rounded border-0 mb-0" style="background:#E66136;color:white;white-space:nowrap;">View All</a>
                                        </div>

                                        <div id="todayLeaveList" class="mt-3" style="max-height: 250px; overflow-y: auto;">

                                            <div class="d-flex align-items-center justify-content-center text-center p-4">

                                                <h5 class="text-muted txtclr">Loading...</h5>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            

                            <div class="col-xl-4 col-lg-6 d-flex flex-column grid-margin sm-grid-margin stretch-card">

                                <div class="card card-rounded todaybday">

                                    <div class="card-body">


                                        <div class="d-flex justify-content-between align-items-start">
                                            <h4 class="card-title card-title-dash sm-bar-chart-size"> Today's Birthday (<span id="birthdayCount">0</span>)
                                                <img src="<?= base_url(env('ImagePath') . '/assets/images/dashboard/cakes.png') ?>" class="bday" alt="Birthday cake">
                                            </h4>
                                            <a href="\empview" class="btn btn-sm rounded border-0" style="background:#E66136;color:white;white-space:nowrap;">View All</a>
                                        </div>

                                        <div id="birthdayList" class="mt-3" style="max-height: 250px; overflow-y: auto;">

                                            <div class="d-flex align-items-center justify-content-center text-center p-4">

                                                <h5 class="text-muted txtclr">Loading...</h5>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        <?php endif; ?>


                        <?php if ($role == 'admin1' || $role == 'hr1') : ?>

                            <div class="col-lg-12 col-sm-12  col-xl-12 d-flex flex-column grid-margin sm-grid-margin stretch-card">

                                <div class="card card-rounded dotChart">

                                    <div class="card-body">

                                        <!-- <h4 class="card-title card-title-dash">Employees by Department</h4> -->
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h4 class="card-title card-title-dash sm-bar-chart-size">Employees by Department</h4>
                                            <a href="\empview" class="btn btn-sm rounded border-0" style="background:#E66136;color:white;white-space:nowrap;">View All</a>
                                        </div>
                                        <div class="pt-3">

                                            <div id="departmentChartWrapper">

                                                <div class="text-center p-5" id="loading-department">

                                                    <h5 class="text-muted txtclr">Loading department data...</h5>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        <?php endif; ?>

                        <?php if ($role != 'admin' && $role != 'hr') : ?>

                            <div class="col-xl-4 col-lg-6 col-sm-6 d-flex flex-column grid-margin sm-grid-margin stretch-card">
                                <div class="card card-rounded shadow-sm">
                                    <div class="card-body">

                                        <!-- Header -->
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h4 class="card-title card-title-dash mb-0">Today's Check-in / Check-out</h4>
                                            <a href="/attendence"
                                                class="btn btn-sm text-white mb-0"
                                                style="background:#E66136;">
                                                View All
                                            </a>
                                        </div>

                                        <!-- Content -->
                                        <div style="max-height:250px; overflow-y:auto;">
                                            <?php if (!empty($todayCheckinCheckoutHistory)) : ?>
                                                <?php foreach ($todayCheckinCheckoutHistory as $record) : ?>
                                                    <div class="border rounded p-3 mb-2 attendance-item">
                                                        <div class="small text-muted mb-1">
                                                            <?= date('d M Y', strtotime($record['date'])) ?>
                                                        </div>

                                                        <div class="d-flex justify-content-between">
                                                            <span class="badge bg-success">
                                                                Check-in:
                                                                <?= date('h:i A', strtotime($record['check_in_time'])) ?>
                                                            </span>

                                                            <span class="badge <?= $record['check_out_time'] ? 'bg-danger' : 'bg-secondary' ?>">
                                                                Check-out:
                                                                <?= $record['check_out_time']
                                                                    ? date('h:i A', strtotime($record['check_out_time']))
                                                                    : 'pending' ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <div class="text-center text-muted py-5">
                                                    <i class="mdi mdi-calendar-remove fs-3 d-block mb-2"></i>
                                                    No records available
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- Today's Hours Worked and Remaining Hours Card -->
                            <div class="col-xl-4 col-lg-6 col-sm-6 d-flex flex-column grid-margin sm-grid-margin stretch-card">
                                <div class="card card-rounded shadow-sm">
                                    <div class="card-body">
                                        <!-- Header -->
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h4 class="card-title card-title-dash mb-0">Today's Hours</h4>
                                            <i class="mdi mdi-clock-outline fs-4" style="color:#E66136;"></i>
                                        </div>

                                        <!-- Content -->
                                        <div class="mt-3">
                                            <?php if ($todayHoursData && $todayHoursData['is_checked_in']) : ?>
                                                <!-- Hours Worked -->
                                                <div class="mb-4">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="text-muted small">Hours Worked</span>
                                                        <span class="badge bg-info" id="hours-worked-badge">
                                                            <?= $todayHoursData['hours_worked'] ?>
                                                        </span>
                                                    </div>
                                                    <div class="progress" style="height: 8px;">
                                                        <?php
                                                        $progressPercent = $todayHoursData['standard_hours_seconds'] > 0
                                                            ? min(100, ($todayHoursData['hours_worked_seconds'] / ($todayHoursData['standard_hours_decimal'] * 3600)) * 100)
                                                            : 0;
                                                        ?>
                                                        <div class="progress-bar bg-info" role="progressbar"
                                                            style="width: <?= $progressPercent ?>%"
                                                            id="hours-worked-progress"
                                                            aria-valuenow="<?= $progressPercent ?>"
                                                            aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                </div>

                                                <!-- Remaining Hours -->
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="text-muted small">Remaining Hours</span>
                                                        <span class="badge <?= $todayHoursData['remaining_hours_seconds'] > 0 ? 'bg-warning' : 'bg-success' ?>" id="remaining-hours-badge">
                                                            <?= $todayHoursData['remaining_hours'] ?>
                                                        </span>
                                                    </div>
                                                    <div class="progress" style="height: 8px;">
                                                        <?php
                                                        $remainingPercent = $todayHoursData['standard_hours_seconds'] > 0
                                                            ? min(100, ($todayHoursData['remaining_hours_seconds'] / ($todayHoursData['standard_hours_decimal'] * 3600)) * 100)
                                                            : 0;
                                                        ?>
                                                        <div class="progress-bar <?= $todayHoursData['remaining_hours_seconds'] > 0 ? 'bg-warning' : 'bg-success' ?>"
                                                            role="progressbar"
                                                            style="width: <?= $remainingPercent ?>%"
                                                            id="remaining-hours-progress"
                                                            aria-valuenow="<?= $remainingPercent ?>"
                                                            aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                </div>

                                                <!-- Standard Hours Info -->
                                                <div class="text-center mt-3 pt-3 border-top">
                                                    <small class="text-muted">
                                                        Standard Hours: <strong><?= $todayHoursData['standard_hours'] ?></strong>
                                                    </small>
                                                </div>

                                                <?php if (!$todayHoursData['is_checked_out']) : ?>
                                                    <div class="text-center mt-2">
                                                        <small class="text-info">
                                                            <i class="mdi mdi-information"></i> Hours updating in real-time
                                                        </small>
                                                    </div>
                                                <?php endif; ?>
                                            <?php else : ?>
                                                <div class="text-center text-muted py-4">
                                                    <i class="mdi mdi-clock-outline fs-3 d-block mb-2"></i>
                                                    <p class="mb-0">Not checked in today</p>
                                                    <small>Check in to start tracking hours</small>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                    </div>
                                </div>
                            </div>


                            <div class="col-xl-4 col-lg-6  col-sm-6  d-flex flex-column grid-margin sm-grid-margin stretch-card">

                                <div class="card card-rounded todaybday">

                                    <div class="card-body">


                                        <div class="d-flex justify-content-between align-items-start">
                                            <h4 class="card-title card-title-dash sm-bar-chart-size"> Today's Birthday (<span id="birthdayCount">0</span>)
                                                <img src="<?= base_url(env('ImagePath') . '/assets/images/dashboard/cakes.png') ?>" class="bday" alt="Birthday cake">
                                            </h4>
                                            <!-- <a href="\empview" class="btn btn-sm rounded border-0" style="background:#E66136;color:white;white-space:nowrap;">View All</a> -->
                                        </div>

                                        <div id="birthdayList" class="mt-3" style="max-height: 250px; overflow-y: auto;">

                                            <div class="d-flex align-items-center justify-content-center text-center p-4">

                                                <h5 class="text-muted txtclr">Loading...</h5>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        <?php endif; ?>

                        <!-- Task Status Chart (for all roles) -->
                        <div class="<?= $role == 'employee' ? 'col-lg-12  col-sm-12  col-xl-12 col-md-12' : 'col-lg-6 col-xl-4 col-md-4' ?> d-flex d-none flex-column grid-margin sm-grid-margin stretch-card">

                            <div class="card card-rounded">

                                <div class="card-body">

                                    <div class="d-flex justify-content-between">

                                        <h4 class="card-title card-title-dash sm-bar-chart-size">Task Status</h4>

                                        <div id="marketingOverview-legend"></div>

                                    </div>

                                    <div class="chartjs-bar-wrapper mt-3">

                                        <canvas id="marketingOverview"></canvas>

                                    </div>

                                </div>

                            </div>

                        </div>


                    </div>

                    <?php if ($role == 'admin1' || $role == 'hr1') : ?>

                        <div class="row">

                            <!-- <div class="col-lg-8 d-flex flex-column"> -->

                            <div class="col-lg-8  col-sm-8 d-flex flex-column">

                                <div class="row flex-grow">

                                    <div class="col-12 grid-margin sm-grid-margin stretch-card">

                                        <div class="card card-rounded">

                                            <div class="card-body">

                                                <div class="align-items-start">

                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <h4 class="card-title card-title-dash sm-bar-chart-size">New Employees</h4>
                                                        <a href="\empview" class="btn btn-sm rounded border-0" style="background:#E66136;color:white;white-space:nowrap;">View All</a>
                                                    </div>


                                                </div>

                                                <div class="table-responsive">

                                                    <table class="table table-striped" id="order-listing">
                                                        <thead>
                                                            <tr>
                                                                <th>Employee Name</th>
                                                                <th>Designation</th>
                                                                <th>Department</th>
                                                                <th>Joining Date</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($employees as $employee) : ?>
                                                                <tr>
                                                                    <td class="py-1">
                                                                        <a href="<?= base_url(env('ImagePath') . 'employee/profile/' . $employee['id']) ?>" class="text-decoration-none text-dark">
                                                                            <div class="d-flex align-items-center">
                                                                                <img src="<?= !empty($employee['profile_image'])
                                                                                                ? base_url('upload/' . $employee['profile_image'])
                                                                                                : base_url(env('ImagePath') . 'upload/default-profile.jpg') ?>"
                                                                                    alt="image" width="40" height="40" class="rounded-circle me-2" />

                                                                                <span class="capitalize-text"><?= esc($employee['firstname'] . ' ' . $employee['lastname']) ?></span>
                                                                            </div>
                                                                        </a>
                                                                    </td>
                                                                    <td class="capitalize-text"><?= esc($employee['designation_name'] ?? 'N/A') ?></td>
                                                                    <td class="capitalize-text"><?= esc($employee['department_name'] ?? 'N/A') ?></td>
                                                                    <td><?= date('d M Y', strtotime($employee['joining_date'])) ?></td>
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



                            <div class="col-lg-4 d-flex flex-column">

                                <div class="row flex-grow">

                                    <!-- Completed Interview Applications -->

                                    <div class="col-12 col-lg-4 col-lg-12 grid-margin sm-grid-margin stretch-card">

                                        <div class="card card-rounded accepted">

                                            <div class="card-body">

                                                <div>

                                                    <h4 class="card-title card-title-dash text-white">Completed Interview Applications</h4>

                                                </div>

                                                <div class="row">

                                                    <div class="col-sm-6 col-6">

                                                        <div class="pt-4 mt-4 text-white">

                                                            <p class="fs-18">

                                                                <span class="fs-30 completed-count">0</span>/<span class="total-count">0</span>

                                                            </p>

                                                            <p class="fs-14">Completed</p>

                                                        </div>

                                                    </div>

                                                    <div class="col-sm-6 col-6 d-sm-block d-lg-block">

                                                        <div class="application-chart-height position-relative">

                                                            <div id="acceptedApplications" class="progressbar-js-circle rounded p-3">

                                                            </div>

                                                            <div class="no-data-overlay d-none">No records found</div>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>



                                    <!-- Scheduled Interview Applications -->

                                    <div class="col-12 col-lg-4 col-lg-12 grid-margin sm-grid-margin stretch-card">

                                        <div class="card card-rounded">

                                            <div class="card-body">

                                                <h4 class="card-title card-title-dash">Working Format</h4>

                                                <div class="progress-container">

                                                    <div id="workingFormats" class="progressbar-js-circle rounded p-3 cirle-bar-height-hr">

                                                        <!-- <h5 class="text-muted" style="font-size: 14px; color: rgb(107, 119, 140); font-weight: bold;">No records available</h5> -->

                                                    </div>

                                                </div>

                                                <div class="lenear-multiple-progress-legends d-flex justify-content-between mt-2" style="display:none;">

                                                    <div>

                                                        <h4 class="line-height-none"><span class="bg-wrk"></span>On-Site</h4>

                                                    </div>

                                                    <div>

                                                        <h4 class="line-height-none"><span class="bg-light"></span>Remote</h4>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    <?php endif; ?>

                    <?php if ($role == 'admin1' || $role == 'hr1') : ?>

                        <div class="row">

                            <div class="col-lg-12 d-flex flex-column">

                                <div class="row flex-grow">

                                    <div class="col-12 col-lg-12 grid-margin stretch-card">

                                        <div class="card card-rounded">

                                            <div class="card-body">

                                                <div class="d-flex justify-content-between align-items-start">
                                                    <h4 class="card-title card-title-dash sm-bar-chart-size">Recruitment Progress</h4>
                                                    <a href="\candidateview" class="btn btn-sm rounded border-0" style="background:#E66136;color:white;white-space:nowrap;">View All</a>
                                                </div>

                                                <div class="table-responsive">

                                                    <table class="table table-striped" id="orders">

                                                        <thead>

                                                            <tr>

                                                                <th>Candidate Name</th>

                                                                <th>Email</th>

                                                                <th>Contact No.</th>

                                                                <th>status</th>

                                                                <th>Job Title</th>

                                                            </tr>

                                                        </thead>

                                                        <tbody>

                                                            <?php foreach ($candidates as $candidate) : ?>

                                                                <tr>
                                                                    <td class="py-1">

                                                                        <a href="<?= base_url('candidate/display/' . $candidate['id']) ?>" class="text-decoration-none text-dark">
                                                                            <div class="d-flex align-items-center">

                                                                                <img src="<?= !empty($employee['profile_image'])
                                                                                                ? base_url('upload/' . $employee['profile_image'])
                                                                                                : base_url(env('ImagePath') . 'upload/default-profile.jpg') ?>"
                                                                                    alt="image" width="40" height="40" class="rounded-circle me-2" />

                                                                                <span class="capitalize-text"><?= esc($candidate['candidate_name']) ?></span>
                                                                            </div>
                                                                        </a>
                                                                    </td>

                                                                    <td><?= esc($candidate['email']) ?></td>

                                                                    <td class="capitalize-text"><?= esc($candidate['phone_number']) ?></td>

                                                                    <td class="capitalize-text"><?= esc(ucfirst($candidate['status'] ?? 'N/A')) ?></td>


                                                                    <td class="capitalize-text"><?= esc($candidate['job_title'] ?? 'N/A') ?></td>

                                                                </tr>

                                                            <?php endforeach; ?>

                                                            <?php if (empty($candidates)) : ?>

                                                                <!-- <tr>

                                                                    <td colspan="5" class="text-center">No records found</td>

                                                                </tr> -->

                                                            <?php endif; ?>

                                                        </tbody>

                                                    </table>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token'); // JWT token
        $.ajax({
            url: "<?= base_url('api/dashboardData') ?>",
            type: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`
            },
            dataType: 'json',
            success: function(response) {
                //this weeek
                $('#this-week-employees').text(response.totalThisWeekEmployees ?? '0');
                $('#this-week-leaves').text(response.totalLeavesThisWeek ?? '0');
                $('#this-week-attendance').text(response.attendanceCountThisWeek ?? '0');
                $('#this-week-task').text(response.totalTasksThisWeek ?? '0');
                //this month
                $('#this-month-employees').text(response.totalEmployeesThisMonth ?? '0');
                $('#this-month-leaves').text(response.totalLeaves ?? '0');
                $('#this-month-attendance').text(response.attendanceCountThisMonth ?? '0');
                $('#this-month-task').text(response.totalTasksThisMonth ?? '0');
                //this year
                $('#this-year-employees').text(response.totalEmployeesThisYear ?? '0');
                $('#this-year-leaves').text(response.totalLeavesThisYear ?? '0');
                $('#this-year-attendance').text(response.attendanceCountThisYear ?? '0');
                $('#this-year-task').text(response.totalTasksThisYear ?? '0');

                var birthdayUsers = response.birthdayUsers || [];
                $('#birthdayCount').text(birthdayUsers.length);

                var todayLeaves = response.totalLeavesToday || [];
                $('#leaveCount').text(todayLeaves.length);
                var leaveHTML = '';
                var baseImagePath = "<?= base_url(env('ImagePath')) ?>";
                if (todayLeaves.length > 0) {
                    todayLeaves.forEach(function(leave) {
                        var fullName = leave.username;
                        var profileImage = leave.profile_image ? `upload/${leave.profile_image}` : `${baseImagePath}upload/default-profile.jpg`;

                        leaveHTML += `
                           <a href="/leaveview" class="text-decoration-none text-dark"> 
                            <div class="wrapper d-flex align-items-center justify-content-between py-2 border-bottom">
                                <div class="d-flex">
                                    <img class="img-sm rounded" src="${profileImage}" alt="profile">
                                    <div class="wrapper ms-3">
                                        <p class="mb-1 fw-bold capitalize-text">${fullName}</p>
                                        <small class="text-muted mb-0">${leave.start_date} to ${leave.end_date}</small>
                                    </div>
                                </div>
                                <i class="mdi mdi-calendar-check fs-4" style="color:#E66136"></i>
                            </div>
                            </a>
                        `;
                    });
                } else {
                    leaveHTML = `
                        <div class="d-flex align-items-center justify-content-center text-center p-4" style="min-height: 172px;">
                            <h5 class="text-muted txtclr">No employees on leave today</h5>
                        </div>
                    `;
                }
                $('#todayLeaveList').html(leaveHTML);

                var todayAttendance = response.todayAttendance || [];
                $('#attendanceCount').text(todayAttendance.length);

                var attendanceHTML = '';
                var baseImagePath = "<?= base_url(env('ImagePath')) ?>";

                if (todayAttendance.length > 0) {
                    todayAttendance.forEach(function(att) {
                        var fullName = att.username;
                        var profileImage = att.profile_image 
                            ? `upload/${att.profile_image}` 
                            : `${baseImagePath}upload/default-profile.jpg`;
                        
                        // Generate unique ID for this employee's timer
                        var timerId = 'timer-' + att.username.replace(/\s+/g, '-');
                        var progressId = 'progress-' + att.username.replace(/\s+/g, '-');

                        attendanceHTML += `
                            <div class="wrapper d-flex align-items-center justify-content-between py-2 border-bottom">
                                <div class="d-flex flex-grow-1">
                                    <img class="img-sm rounded" src="${profileImage}" alt="profile">
                                    <div class="wrapper ms-3 flex-grow-1">
                                        <p class="mb-1 fw-bold capitalize-text">${fullName}</p>
                                        <small class="text-muted mb-0">
                                            Check-in: ${att.check_in_time}
                                        </small>
                                        <br>
                                        <small class="text-success fw-bold" id="${timerId}">
                                            Working: 00:00:00
                                        </small>
                                        <div class="progress mt-2" style="height: 6px;">
                                            <div class="progress-bar bg-success" id="${progressId}" role="progressbar" 
                                                 style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <i class="mdi mdi-login fs-4" style="color:#28a745"></i>
                            </div>
                        `;
                    });
                    
                    // Set timers after HTML is rendered
                    setTimeout(function() {
                        todayAttendance.forEach(function(att) {
                            var timerId = 'timer-' + att.username.replace(/\s+/g, '-');
                            var progressId = 'progress-' + att.username.replace(/\s+/g, '-');                            
                            startWorkTimer(timerId, progressId, att.check_in_time, att.check_out_time);
                        });
                    }, 100);
                } else {
                    attendanceHTML = `
                        <div class="d-flex align-items-center justify-content-center text-center p-4" style="min-height: 172px;">
                            <h5 class="text-muted txtclr">No attendance marked today</h5>
                        </div>
                    `;
                }

                $('#todayAttendanceList').html(attendanceHTML);


                var birthdayHTML = '';
                var baseImagePath = "<?= base_url(env('ImagePath')) ?>";
                if (birthdayUsers.length > 0) {

                    birthdayUsers.forEach(function(user) {
                        var fullName = `${user.firstname} ${user.lastname}`;
                        var dob = new Date(user.date_of_birth);
                        var dobFormatted = dob.toLocaleDateString('en-GB'); // dd/mm/yyyy
                        var profileImage = user.profile_image ? `upload/${user.profile_image}` : `${baseImagePath}upload/default-profile.jpg`;
                        birthdayHTML += `
                        <div class="wrapper d-flex align-items-center justify-content-between py-2 border-bottom">

                            <div class="d-flex">
                                <a href="employee/profile/${user.user_id}" class="text-decoration-none text-dark"> 
                                    <img class="img-sm rounded" src="${profileImage}" alt="profile">
                                </a>
                                <div class="wrapper ms-3">
                                    <p class="ms-1 mb-1 fw-bold capitalize-text">${fullName}</p>
                                    <small class="text-muted mb-0">${dobFormatted}</small>
                                </div>
                            </div>
                            <img src="<?= base_url(env('ImagePath') . '/assets/images/dashboard/cakes.png') ?>" class="bday" alt="Birthday cake">
                        </div>
                        `;
                    });
                } else {
                    birthdayHTML = `
                    <div class="d-flex align-items-center justify-content-center text-center p-4" style="min-height: 172px;">
                        <h5 class="text-muted txtclr">No birthdays today</h5>
                    </div>
                `;
                }


                $('#birthdayList').html(birthdayHTML);
                // if (response.departmentLabels && response.employeeCounts && response.departmentLabels.length > 0) {
                //     const departmentLabels = response.departmentLabels;
                //     const employeeCounts = response.employeeCounts;
                //     const staticColors = ['#E66136', '#000000'];
                //     const generateLightColor = (index) => {
                //         const hue = (index * 60) % 360;
                //         const saturation = 50;
                //         const lightness = 75;
                //         return `hsl(${hue}, ${saturation}%, ${lightness}%)`;
                //     };
                //     const backgroundColors = departmentLabels.map((_, i) => i < 2 ? staticColors[i] : generateLightColor(i));
                //     const borderColors = backgroundColors;

                //     $('#departmentChartWrapper').html(`
                //         <div class="row g-3 align-items-start">
                //             <div class="col-md-6 text-center">
                //             <canvas id="doughnutChart" style="width: 100%;height:100%"></canvas>
                //             </div>
                //             <div class="col-md-6 margincon">
                //             <div class="row" id="legend-content"></div>
                //             </div>
                //         </div>
                //         `);

                //     const ctx = document.getElementById('doughnutChart');

                //     new Chart(ctx, {
                //         type: "doughnut",
                //         data: {
                //             labels: departmentLabels,
                //             datasets: [{
                //                 data: employeeCounts,
                //                 backgroundColor: backgroundColors,
                //                 borderColor: borderColors,
                //                 borderWidth: 1,
                //             }],
                //         },
                //         options: {
                //             responsive: true,
                //             maintainAspectRatio: false,
                //             cutout: "70%",
                //             plugins: {
                //                 legend: {
                //                     display: false
                //                 },
                //                 tooltip: {
                //                     enabled: true
                //                 }
                //             },
                //         },
                //     });

                //     // Build 2x2 Legend
                //     departmentLabels.forEach((label, i) => {
                //         $('#legend-content').append(`
                //             <div class="col-6 mb-3">
                //             <p class="legend-value fw-bold mb-1 piechartsm" style="font-size:28px;gap:6px">${employeeCounts[i]}</p>
                //             <div class="legend-label d-flex align-items-start">
                //                 <span class="bg-wrks me-2 mt-1" style="background-color: ${backgroundColors[i]}"></span>
                //                 <span class="legend-text font-size-label">${label}</span>
                //             </div>
                //             </div>
                //         `);
                //     });
                // } else {
                //     $('#departmentChartWrapper').html(`
                //     <div class="text-center p-5">
                //         <h5 class="text-muted txtclr">No records available</h5>
                //     </div>
                // `);
                // }
                // if (response.workingFormatTotal > 0 && response.remotePercentage !== null && response.onSitePercentage !== null) {
                //     var circle = new ProgressBar.Circle("#workingFormats", {
                //         color: "black",
                //         trailColor: "#f4f4f4",
                //         strokeWidth: 9,
                //         trailWidth: 8,
                //         duration: 2000,
                //         text: {
                //             autoStyleContainer: false
                //         },
                //         from: {
                //             color: "#E66136",
                //             width: 8
                //         },
                //         to: {
                //             color: "#E66136",
                //             width: 8
                //         },
                //         step: function(state, circle) {
                //             circle.path.setAttribute("stroke", state.color);
                //             circle.path.setAttribute("stroke-width", state.width);
                //             var value = Math.round(circle.value() * 100);
                //             circle.setText(value > 0 ? value + "%" : "0%");
                //         }
                //     });

                //     circle.text.style.fontSize = "20px";
                //     circle.text.style.fontWeight = "bold";

                //     if (response.onSitePercentage > 0) {
                //         circle.animate(response.onSitePercentage);
                //     } else {
                //         circle.set(0);
                //         circle.setText("0%");
                //     }

                //     $('.lenear-multiple-progress-legends').show();
                // } else {
                //     $('#workingFormats').html(`
                //         <div class="d-flex align-items-center justify-content-center text-center" style="height: 150px;">
                //             <h5 class="text-muted mb-0" style="font-size: 14px; font-weight: bold;">No records available</h5>
                //         </div>
                //     `);
                //     $('.lenear-multiple-progress-legends').hide();
                // }
                const completed = response.completedInterviews || 0;
                const scheduled = response.scheduledInterviews || 0;
                const total = response.totalInterviews || 0;

                $(".completed-count").text(completed);
                $(".scheduled-count").text(scheduled);
                $(".total-count").text(total);

                const hasData = total > 0;
                // Toggle blur & no-data messages
                if (!hasData) {
                    $("#acceptedApplications, #rejectedApplications").addClass("blurred");
                    $(".no-data-overlay").removeClass("d-none");
                    return;
                }

                $(".no-data-overlay").addClass("d-none");
                $("#acceptedApplications, #rejectedApplications").removeClass("blurred");
                // ProgressBar: Completed
                const completedCircle = new ProgressBar.Circle("#acceptedApplications", {
                    color: "#fff",
                    trailColor: "#000000",
                    trailWidth: 15,
                    duration: 1400,
                    easing: "easeInOut",
                    strokeWidth: 15,
                    from: {
                        color: "#fff",
                        width: 15
                    },

                    to: {

                        color: "#fff",

                        width: 15

                    },

                    step: function(state, circle) {

                        const percentage = Math.round((completed / total) * 100);

                        circle.setText(`<tspan style="font-size: 16px">${percentage}</tspan><tspan style="font-size: 10px">%</tspan>`);

                    },

                });



                completedCircle.text.style.fontFamily = '"Arial", sans-serif';

                completedCircle.text.style.fontSize = "25px";

                completedCircle.text.style.color = "white";

                completedCircle.animate(completed / total);

            },

            error: function(xhr) {

                $('#departmentChartWrapper').html(`

                <div class="text-center p-5">

                    <h5 class="text-danger txtclr">Failed to load department data</h5>

                </div>

            `);

                $('#birthdayList').html(`
                    <div class="d-flex align-items-center justify-content-center text-center p-4">
                        <h5 class="text-muted txtclr">Failed to load birthdays</h5>
                    </div>
                    `);
                $('#this-week-employees').text('N/A');
                $('#this-week-leaves').text('N/A');
                console.error('Dashboard API error:', xhr.responseText);
            }
        });
    });

    // Global variable to store company working hours
    var companyWorkingHours = 8; // Default 8 hours
    
    // Fetch company rules to get working hours
    $.ajax({
        url: "<?= base_url('api/rules_get') ?>",
        type: 'GET',
        success: function(response) {
            if (response.status === 'success' && response.data) {
                companyWorkingHours = parseFloat(response.data.working_hours_per_day) || 8;
            }
        },
        error: function() {
            console.log('Using default working hours: 8 hours');
        }
    });

    // Function to start and update work timer for each employee
    function startWorkTimer(timerId, progressId, checkInTime, checkOutTime) {
        
        if (!checkInTime) return;

        var today = new Date();

        function parseTime(timeStr) {
            var p = timeStr.split(':');
            return new Date(
                today.getFullYear(),
                today.getMonth(),
                today.getDate(),
                parseInt(p[0]),
                parseInt(p[1]),
                parseInt(p[2] || 0)
            );
        }

        var checkInDate = parseTime(checkInTime);
        var checkOutDate = checkOutTime ? parseTime(checkOutTime) : null;

        var intervalId = null;

        function updateTimer() {
            var now = new Date();

            // 👉 Stop timer if checkout time reached
            if (checkOutDate && now >= checkOutDate) {
                now = checkOutDate;      // freeze at checkout
                clearInterval(intervalId);
            }

            var diff = now - checkInDate;
            if (diff < 0) diff = 0;

            var hours = Math.floor(diff / (1000 * 60 * 60));
            var minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((diff % (1000 * 60)) / 1000);

            var timeString =
                String(hours).padStart(2, '0') + ':' +
                String(minutes).padStart(2, '0') + ':' +
                String(seconds).padStart(2, '0');

            var element = document.getElementById(timerId);
            if (element) {
                element.textContent = checkOutDate && now >= checkOutDate
                    ? 'Worked: ' + timeString
                    : 'Working: ' + timeString;
            }

            // Progress bar
            var progressElement = document.getElementById(progressId);
            if (progressElement) {
                var totalWorkingSeconds = companyWorkingHours * 3600;
                var workedSeconds = Math.floor(diff / 1000);
                var percentage = Math.min((workedSeconds / totalWorkingSeconds) * 100, 100);

                progressElement.style.width = percentage + '%';
                progressElement.setAttribute('aria-valuenow', percentage);

                progressElement.className = 'progress-bar';
                if (percentage < 50) {
                    progressElement.classList.add('bg-warning');
                } else if (percentage < 100) {
                    progressElement.classList.add('bg-info');
                } else {
                    progressElement.classList.add('bg-success');
                }
            }
        }

        // Initial render
        updateTimer();

        // Start ticking only if not already checked out
        if (!checkOutDate || new Date() < checkOutDate) {
            intervalId = setInterval(updateTimer, 1000);
        }
    }


    function selectFilter(label, type) {
        document.getElementById('filterDropdownBtn').innerText = label;
        // Toggle the view
        showSection(type);
    }

    function showSection(type) {
        const weekSection = document.getElementById('weekSection');
        const monthSection = document.getElementById('monthSection');
        const yearSection = document.getElementById('yearSection');
        // Hide all first
        if (weekSection) weekSection.style.display = 'none';
        if (monthSection) monthSection.style.display = 'none';
        if (yearSection) yearSection.style.display = 'none';

        // Show the selected one


        if (type === 'week') {
            if (weekSection) weekSection.style.display = 'block';
        } else if (type === 'month') {
            if (monthSection) monthSection.style.display = 'block';
        } else if (type === 'year') {
            if (yearSection) yearSection.style.display = 'block';
        }

    }

    // Set default view on page load
    document.addEventListener("DOMContentLoaded", function() {
        selectFilter('This Month', 'month');
    });
    document.addEventListener("DOMContentLoaded", function() {
        if ($("#marketingOverview").length) {
            fetch('api/admin/getTaskData')
                .then(response => response.json())
                .then(data => {
                    const canvas = document.getElementById('marketingOverview');
                    const ctx = canvas.getContext('2d');
                    const chartContainer = document.querySelector('.chartjs-bar-wrapper');
                    // Check if the dataset is empty
                    if (data.labels.length === 0) {
                        // Apply blur effect
                        canvas.style.filter = "blur(8px)";
                        // Add "No records found" message
                        const noDataMessage = document.createElement('div');
                        noDataMessage.textContent = "No records available";
                        noDataMessage.style.position = "absolute";
                        noDataMessage.style.top = "50%";
                        noDataMessage.style.left = "50%";
                        noDataMessage.style.transform = "translate(-50%, -50%)";
                        noDataMessage.style.color = "#6B778C";
                        noDataMessage.style.fontSize = "14px";
                        noDataMessage.style.fontWeight = "bold";
                        // Ensure the parent container is positioned relative
                        chartContainer.style.position = "relative";
                        chartContainer.appendChild(noDataMessage);
                        return; // Stop execution as no data exists
                    }

                    // If data exists, render the chart

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                    label: 'Assigned Tasks',

                                    data: data.assigned,

                                    backgroundColor: "#E66136",

                                    borderColor: "#E66136",

                                    borderWidth: 0,

                                    barPercentage: 0.35,

                                    fill: true,

                                },

                                {

                                    label: 'Completed Tasks',

                                    data: data.completed,

                                    backgroundColor: "#000000",

                                    borderColor: "#000000",

                                    borderWidth: 0,

                                    barPercentage: 0.35,

                                    fill: true,

                                }

                            ]

                        },

                        options: {

                            responsive: true,

                            maintainAspectRatio: false,

                            scales: {

                                y: {

                                    grid: {

                                        display: true,

                                        color: "#F0F0F0",

                                    },

                                    ticks: {

                                        color: "#6B778C",

                                        font: {

                                            size: 10

                                        },

                                        stepSize: 1, // <-- Force step size to 1

                                        callback: function(value) {

                                            if (Number.isInteger(value)) {

                                                return value;

                                            }

                                            return ''; // Hide non-integer values

                                        }

                                    }

                                },

                                x: {

                                    grid: {

                                        display: false

                                    },

                                    ticks: {

                                        color: "#6B778C",

                                        font: {

                                            size: 10

                                        },

                                    }

                                }

                            },

                            plugins: {

                                legend: {

                                    display: false

                                }

                            }

                        },



                        plugins: [{

                            afterInit: function(chart) {

                                const chartId = chart.canvas.id;

                                const legendId = `${chartId}-legend`;

                                const legendContainer = document.getElementById(legendId);



                                if (!legendContainer) return;



                                const ul = document.createElement('ul');

                                chart.data.datasets.forEach(dataset => {

                                    const li = document.createElement('li');

                                    li.innerHTML = `<span style="background-color: ${dataset.borderColor}; width: 12px; height: 12px; display: inline-block; margin-right: 5px;"></span>${dataset.label}`;

                                    ul.appendChild(li);

                                });



                                legendContainer.innerHTML = "";

                                legendContainer.appendChild(ul);

                            }

                        }]

                    });

                })

                .catch(error => console.error("Error fetching task data:", error));

        }

    });
</script>

<script>
    $(document).ready(function() {

        const token = localStorage.getItem('token');

        // Fetch and render year-wise performance chart

        $.ajax({

            url: "<?= base_url('api/performance/yearly') ?>",

            type: "GET",

            headers: {

                'Authorization': `Bearer ${token}`

            },

            success: function(response) {

                if (!response.monthly_avg.length) {

                    $("#yearlyPerformanceLine").addClass("blur-chart");

                    $("#noYearDataMessage").show();

                    return;

                }



                renderYearlyChart(response.monthly_avg, response.year);

            },

            error: function(xhr) {

                console.error("Error fetching yearly performance data", xhr);

            }

        });



        function renderYearlyChart(monthlyData, year) {

            const ctx = document.getElementById('yearlyPerformanceLine').getContext('2d');

            const labels = [

                'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',

                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'

            ];

            const ratings = Array(12).fill(0);



            monthlyData.forEach(item => {

                ratings[item.month - 1] = parseFloat(item.avg_rating);

            });



            new Chart(ctx, {

                type: 'bar',

                data: {

                    labels: labels,

                    datasets: [{

                        label: `Avg Rating (${year})`,

                        data: ratings,

                        backgroundColor: '#E66136',

                        borderRadius: 4,

                    }]

                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false,

                    scales: {

                        y: {

                            beginAtZero: true,

                            ticks: {

                                stepSize: 1,

                                callback: function(value) {

                                    return Number.isInteger(value) ? value : '';

                                }

                            }

                        }

                    },

                    plugins: {

                        legend: {

                            display: true

                        }

                    }

                }

            });

        }

    });
</script>

<?php if ($role == 'employee' && isset($todayHoursData) && $todayHoursData && $todayHoursData['is_checked_in']) : ?>
    <script>
        // Update hours in real-time if employee is checked in
        (function() {
            'use strict';
            console.log('🕐 Real-time hours update script initialized');

            const checkInTime = '<?= $todayHoursData['check_in_time'] ?? '' ?>';
            const standardHoursDecimal = <?= $todayHoursData['standard_hours_decimal'] ?? 8.0 ?>;
            const standardHoursSeconds = standardHoursDecimal * 3600;
            const mealBreakSeconds = <?= $todayHoursData['meal_break_seconds'] ?? (30 * 60) ?>;
            const completedHoursSeconds = <?= $todayHoursData['completed_hours_seconds'] ?? 0 ?>;
            const isCheckedOut = <?= ($todayHoursData['is_checked_out'] ?? false) ? 'true' : 'false' ?>;

            console.log('📊 Initial values:', {
                checkInTime: checkInTime,
                completedHoursSeconds: completedHoursSeconds,
                completedHoursFormatted: Math.floor(completedHoursSeconds / 3600) + ':' +
                    Math.floor((completedHoursSeconds % 3600) / 60) + ':' +
                    (completedHoursSeconds % 60),
                mealBreakSeconds: mealBreakSeconds,
                standardHours: standardHoursDecimal,
                isCheckedOut: isCheckedOut
            });

            // If already checked out, don't run live updates
            if (isCheckedOut) {
                console.log('ℹ️ Employee already checked out, live updates disabled');
                return;
            }

            if (!checkInTime) {
                console.error('❌ No check-in time found');
                return;
            }

            // Parse check-in time
            const checkInParts = checkInTime.split(':');
            const checkInHours = parseInt(checkInParts[0]) || 0;
            const checkInMinutes = parseInt(checkInParts[1]) || 0;
            const checkInSecs = parseInt(checkInParts[2]) || 0;

            console.log('⏰ Parsed check-in time:', checkInHours + ':' + checkInMinutes + ':' + checkInSecs);

            // Create check-in Date object using today's date
            const today = new Date();
            const checkInDate = new Date(today.getFullYear(), today.getMonth(), today.getDate(), checkInHours, checkInMinutes, checkInSecs);

            console.log('📅 Check-in Date object:', checkInDate.toString());

            // Store initial completed hours from server
            let baseCompletedHours = completedHoursSeconds;
            let updateInterval = null;

            function updateHours() {
                try {
                    // Check if elements exist first
                    const hoursBadge = document.getElementById('hours-worked-badge');
                    const remainingBadge = document.getElementById('remaining-hours-badge');
                    const hoursProgress = document.getElementById('hours-worked-progress');
                    const remainingProgress = document.getElementById('remaining-hours-progress');

                    if (!hoursBadge || !remainingBadge || !hoursProgress || !remainingProgress) {
                        console.warn('⚠️ Elements not found yet, waiting...');
                        return;
                    }

                    const now = new Date();

                    // Use time-based calculation (more reliable than Date object difference)
                    const currentHours = now.getHours();
                    const currentMinutes = now.getMinutes();
                    const currentSecs = now.getSeconds();

                    const checkInTotalSeconds = (checkInHours * 3600) + (checkInMinutes * 60) + checkInSecs;
                    const currentTotalSeconds = (currentHours * 3600) + (currentMinutes * 60) + currentSecs;

                    let activeSessionSeconds = currentTotalSeconds - checkInTotalSeconds;

                    // Handle day rollover (if check-in was yesterday or if current time is before check-in)
                    if (activeSessionSeconds < 0) {
                        // Assume it's the same day, so if negative, it means we're still in the same session
                        // This shouldn't happen normally, but handle it gracefully
                        activeSessionSeconds = Math.max(0, activeSessionSeconds);
                    }

                    // Cap at 24 hours (86400 seconds) to prevent unrealistic values
                    activeSessionSeconds = Math.min(activeSessionSeconds, 86400);
                    activeSessionSeconds = Math.max(0, activeSessionSeconds);

                    // Store time before break deduction for logging
                    const activeSessionBeforeBreak = activeSessionSeconds;

                    // For active session (not checked out yet), show actual elapsed time
                    // Meal break will be deducted by the backend when they check out
                    // Don't subtract break from active session - show real-time elapsed time
                    let activeSessionAfterBreak = activeSessionSeconds;

                    // Add completed hours from previous check-in/check-out pairs
                    // For active session, use the actual elapsed time (break handled on check-out)
                    let workedSeconds = baseCompletedHours + activeSessionAfterBreak;

                    // Format hours worked
                    const hours = Math.floor(workedSeconds / 3600);
                    const minutes = Math.floor((workedSeconds % 3600) / 60);
                    const seconds = workedSeconds % 60;
                    const hoursWorkedFormatted = String(hours).padStart(2, '0') + ':' +
                        String(minutes).padStart(2, '0') + ':' +
                        String(seconds).padStart(2, '0');

                    // Calculate remaining hours
                    const remainingSeconds = Math.max(0, standardHoursSeconds - workedSeconds);
                    const remainingHours = Math.floor(remainingSeconds / 3600);
                    const remainingMinutes = Math.floor((remainingSeconds % 3600) / 60);
                    const remainingSecs = remainingSeconds % 60;
                    const remainingHoursFormatted = String(remainingHours).padStart(2, '0') + ':' +
                        String(remainingMinutes).padStart(2, '0') + ':' +
                        String(remainingSecs).padStart(2, '0');

                    // Debug logging (log first update and then every 10 seconds)
                    const shouldLog = (typeof updateHours.updateCount === 'undefined' || updateHours.updateCount === 0) ||
                        (Math.floor(Date.now() / 1000) % 10 === 0);

                    if (typeof updateHours.updateCount === 'undefined') {
                        updateHours.updateCount = 0;
                    }
                    updateHours.updateCount++;

                    // if (shouldLog) {
                    //     console.log('🔄 Update #' + updateHours.updateCount + ':', {
                    //         now: now.toLocaleTimeString(),
                    //         checkInTime: checkInHours + ':' + checkInMinutes + ':' + checkInSecs,
                    //         activeSessionSeconds: activeSessionSeconds,
                    //         activeSessionAfterBreak: activeSessionAfterBreak,
                    //         beforeBreak: activeSessionBeforeBreak,
                    //         completedHours: baseCompletedHours,
                    //         completedHoursFormatted: Math.floor(baseCompletedHours / 3600) + ':' + 
                    //                                 Math.floor((baseCompletedHours % 3600) / 60) + ':' + 
                    //                                 (baseCompletedHours % 60),
                    //         totalWorked: workedSeconds,
                    //         hoursWorkedFormatted: hoursWorkedFormatted,
                    //         remainingHoursFormatted: remainingHoursFormatted,
                    //         hoursBadgeExists: !!hoursBadge,
                    //         remainingBadgeExists: !!remainingBadge
                    //     });
                    // }

                    // Update badges - force update with multiple methods
                    if (hoursBadge) {
                        hoursBadge.textContent = hoursWorkedFormatted;
                        hoursBadge.innerHTML = hoursWorkedFormatted;
                        // Force a reflow to ensure update is visible
                        void hoursBadge.offsetHeight;
                    } else {
                        console.error('❌ hoursBadge element not found!');
                    }

                    if (remainingBadge) {
                        remainingBadge.textContent = remainingHoursFormatted;
                        remainingBadge.innerHTML = remainingHoursFormatted;
                        // Force a reflow to ensure update is visible
                        void remainingBadge.offsetHeight;
                    } else {
                        console.error('❌ remainingBadge element not found!');
                    }

                    // Update progress bars
                    const progressPercent = standardHoursSeconds > 0 ?
                        Math.min(100, (workedSeconds / standardHoursSeconds) * 100) :
                        0;
                    const remainingPercent = standardHoursSeconds > 0 ?
                        Math.min(100, (remainingSeconds / standardHoursSeconds) * 100) :
                        0;

                    if (hoursProgress) {
                        hoursProgress.style.width = progressPercent + '%';
                        hoursProgress.setAttribute('aria-valuenow', progressPercent);
                        hoursProgress.style.setProperty('width', progressPercent + '%', 'important');
                    }

                    if (remainingProgress) {
                        remainingProgress.style.width = remainingPercent + '%';
                        remainingProgress.setAttribute('aria-valuenow', remainingPercent);
                        remainingProgress.style.setProperty('width', remainingPercent + '%', 'important');
                    }

                    // Update badge color for remaining hours
                    if (remainingSeconds <= 0) {
                        remainingBadge.classList.remove('bg-warning');
                        remainingBadge.classList.add('bg-success');
                        remainingProgress.classList.remove('bg-warning');
                        remainingProgress.classList.add('bg-success');
                    } else {
                        remainingBadge.classList.remove('bg-success');
                        remainingBadge.classList.add('bg-warning');
                        remainingProgress.classList.remove('bg-success');
                        remainingProgress.classList.add('bg-warning');
                    }
                } catch (error) {
                    console.error('Error updating hours:', error);
                }
            }

            // Function to start the interval
            function startUpdates() {
                // Check if elements exist before starting
                const hoursBadge = document.getElementById('hours-worked-badge');
                const remainingBadge = document.getElementById('remaining-hours-badge');
                const hoursProgress = document.getElementById('hours-worked-progress');
                const remainingProgress = document.getElementById('remaining-hours-progress');

                if (!hoursBadge || !remainingBadge || !hoursProgress || !remainingProgress) {
                    console.warn('⚠️ Elements not found, retrying in 100ms...', {
                        hoursBadge: !!hoursBadge,
                        remainingBadge: !!remainingBadge,
                        hoursProgress: !!hoursProgress,
                        remainingProgress: !!remainingProgress
                    });
                    // Retry up to 50 times (5 seconds)
                    if (typeof startUpdates.retryCount === 'undefined') {
                        startUpdates.retryCount = 0;
                    }
                    startUpdates.retryCount++;
                    if (startUpdates.retryCount < 50) {
                        setTimeout(startUpdates, 100);
                    } else {
                        console.error('❌ Failed to find elements after 50 retries');
                    }
                    return;
                }

                console.log('🚀 Starting real-time updates');
                updateHours(); // Initial update

                // Clear any existing interval
                if (updateInterval) {
                    clearInterval(updateInterval);
                }

                // Update every second for live real-time updates
                updateInterval = setInterval(function() {
                    updateHours();
                }, 1000);

                // Store interval in window for debugging
                window.hoursUpdateInterval = updateInterval;

                console.log('✅ Real-time update interval started');
            }

            // Multiple ways to ensure script runs
            function initScript() {
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function() {
                        setTimeout(startUpdates, 200);
                    });
                } else if (document.readyState === 'interactive' || document.readyState === 'complete') {
                    // DOM is already ready or loading
                    setTimeout(startUpdates, 200);
                } else {
                    // Fallback
                    setTimeout(startUpdates, 500);
                }
            }

            // Start immediately
            initScript();

            // Also try after a short delay as fallback
            setTimeout(function() {
                if (!window.hoursUpdateInterval) {
                    console.log('🔄 Fallback: Retrying to start updates...');
                    startUpdates();
                }
            }, 1000);

            // Stop updating if page becomes hidden (optional optimization)
            // Resume when page becomes visible again
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    if (updateInterval) {
                        clearInterval(updateInterval);
                        updateInterval = null;
                        console.log('⏸️ Updates paused (page hidden)');
                    }
                } else {
                    console.log('▶️ Updates resumed (page visible)');
                    updateHours(); // Update immediately when page becomes visible
                    if (!updateInterval) {
                        updateInterval = setInterval(function() {
                            updateHours();
                        }, 1000);
                    }
                }
            });

            // Also update on window focus
            window.addEventListener('focus', function() {
                console.log('👁️ Window focused, updating hours');
                updateHours();
            });
        })();
    </script>
<?php endif; ?>

<script>
    /**
     * Show Announcement Details in a Bootstrap/Premium Styled Modal
     */
    function showAnnouncement(announcement) {
        Swal.fire({
            title: '',
            html: `
                <div class="text-start">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <h4 class="mb-0 fw-bold" style="color: #1e293b; font-size: 1.2rem;">Announcement Details</h4>
                        <button type="button" class="btn-close" onclick="Swal.close()" aria-label="Close"></button>
                    </div>
                    <div class="mb-3">
                        <h5 class="fw-bold mb-1" style="color: #334155;">${announcement.title}</h5>
                        <p class="text-muted" style="font-size: 0.9rem;">${announcement.description}</p>
                    </div>
                    <div class="d-flex justify-content-end mt-4 pt-3">
                        <button type="button" class="btn btn-info text-white px-4" style="background-color: #00c4ff; border: none; font-weight: 600;" onclick="Swal.close()">Close</button>
                    </div>
                </div>
            `,
            showConfirmButton: false,
            width: '500px',
            padding: '1.5rem',
            customClass: {
                popup: 'rounded-3 shadow-lg border-0'
            },
            showCloseButton: false,
            backdrop: `rgba(0,0,0,0.4)`
        });
    }

    // ================================================
    // Announcement Cards: infinite left-to-right via scrollLeft
    // ================================================
    (function() {
        var track = document.getElementById('announcementContainer');
        if (!track) return;

        // Capture real cards before any cloning
        var realCards = Array.prototype.slice.call(track.querySelectorAll('.ann-card'));
        if (!realCards.length) return;

        // Wait one frame so widths are computed
        requestAnimationFrame(function() {

            var containerW = track.offsetWidth;  // visible width of track
            var cardW      = (realCards[0].offsetWidth || 220) + 14; // card + gap
            var origW      = realCards.length * cardW;               // total original set width

            // Clone enough sets so total content > 3x container width (ensures seamless snap)
            var setsNeeded = Math.max(3, Math.ceil((containerW * 3) / origW));
            for (var s = 0; s < setsNeeded; s++) {
                realCards.forEach(function(card) {
                    var clone = card.cloneNode(true);
                    clone.setAttribute('aria-hidden', 'true');
                    track.appendChild(clone);
                });
            }

            var speed  = 1.2;    // px per frame — tweak for faster/slower
            var paused = false;

            function tick() {
                if (!paused) {
                    track.scrollLeft += speed;
                    // Seamless snap: when we've scrolled one full original set
                    if (track.scrollLeft >= origW) {
                        track.scrollLeft -= origW;
                    }
                }
                requestAnimationFrame(tick);
            }
            requestAnimationFrame(tick);

            // Pause on hover
            track.addEventListener('mouseenter', function() { paused = true; });
            track.addEventListener('mouseleave', function() { paused = false; });
            // Pause on touch, resume after 1 s
            track.addEventListener('touchstart', function() { paused = true; }, { passive: true });
            track.addEventListener('touchend',   function() {
                setTimeout(function() { paused = false; }, 1000);
            });
        });
    })();




</script>
<?= $this->endSection(); ?>