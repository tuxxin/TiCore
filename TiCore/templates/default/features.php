<?php include 'layouts/header.php'; ?>

<!-- ── Page header ─────────────────────────────────────────────────────────── -->
<section aria-labelledby="page-heading" class="mb-5">
    <h1 id="page-heading" class="display-6 fw-bold">
        Features Supported
        <small class="fs-5 fw-normal text-muted d-block d-sm-inline mt-1 mt-sm-0">
            <?php echo e($server_software); ?>
        </small>
    </h1>
    <p class="lead text-muted">
        Live capabilities of this TiCore installation — runtime versions, active log level,
        database connectivity, and every PHP extension probed at request time.
    </p>
</section>

<!-- ── Runtime summary cards ───────────────────────────────────────────────── -->
<section aria-labelledby="runtime-heading" class="mb-5">
    <h2 id="runtime-heading" class="mb-3">Runtime</h2>
    <div class="row g-3">

        <div class="col-sm-6 col-lg-3">
            <div class="card text-center border-0 bg-light h-100">
                <div class="card-body">
                    <div class="fs-3 mb-1" aria-hidden="true">&#128011;</div>
                    <div class="text-muted small" id="php-label">PHP Version</div>
                    <div class="fw-bold fs-5" aria-labelledby="php-label">
                        <?php echo e($php_version); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card text-center border-0 bg-light h-100">
                <div class="card-body">
                    <div class="fs-3 mb-1" aria-hidden="true">&#127760;</div>
                    <div class="text-muted small" id="server-label">Web Server</div>
                    <div class="fw-bold" style="font-size:.88rem;" aria-labelledby="server-label">
                        <?php echo e($server_software); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card text-center border-0 bg-light h-100">
                <div class="card-body">
                    <div class="fs-3 mb-1" aria-hidden="true">&#128200;</div>
                    <div class="text-muted small" id="db-label">Database</div>
                    <div class="fw-bold" aria-labelledby="db-label">
                        <?php if ($db_status === 'Connected'): ?>
                            <span class="text-success">Connected</span>
                            <div class="small text-muted"><?php echo e($db_version); ?></div>
                        <?php else: ?>
                            <span class="text-secondary">Disabled</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card text-center border-0 bg-light h-100">
                <div class="card-body">
                    <div class="fs-3 mb-1" aria-hidden="true">&#128196;</div>
                    <div class="text-muted small" id="log-label">Log Level</div>
                    <div class="fw-bold" aria-labelledby="log-label">
                        <?php echo e($log_level_label); ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- ── PHP Extensions ──────────────────────────────────────────────────────── -->
<section aria-labelledby="ext-heading" class="mb-5">
    <h2 id="ext-heading" class="mb-1">PHP Extensions</h2>
    <p class="text-muted mb-3">
        Probed live on every request.
        <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle ms-1">&#9989; Loaded</span>
        <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle ms-1">&#9940; Not loaded</span>
    </p>

    <?php
    $loaded    = array_filter($extensions, fn($e) => $e['loaded']);
    $notLoaded = array_filter($extensions, fn($e) => !$e['loaded']);
    ?>

    <?php if (!empty($loaded)): ?>
    <h3 class="h6 text-success fw-semibold mb-2">Loaded (<?php echo count($loaded); ?>)</h3>
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-2 mb-4"
         role="list" aria-label="Loaded PHP extensions">
        <?php foreach ($loaded as $ext): ?>
        <div class="col" role="listitem">
            <div class="d-flex align-items-center gap-2 p-2 rounded border border-success-subtle bg-success-subtle">
                <span aria-hidden="true">&#9989;</span>
                <span class="small fw-medium"><?php echo e($ext['name']); ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($notLoaded)): ?>
    <h3 class="h6 text-secondary fw-semibold mb-2">Not Loaded (<?php echo count($notLoaded); ?>)</h3>
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-2"
         role="list" aria-label="PHP extensions not loaded">
        <?php foreach ($notLoaded as $ext): ?>
        <div class="col" role="listitem">
            <div class="d-flex align-items-center gap-2 p-2 rounded border border-secondary-subtle bg-light text-muted">
                <span aria-hidden="true">&#9940;</span>
                <span class="small fw-medium"><?php echo e($ext['name']); ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</section>

<?php include 'layouts/footer.php'; ?>
