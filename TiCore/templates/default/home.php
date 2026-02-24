<?php include 'layouts/header.php'; ?>

<div class="p-5 mb-4 bg-light rounded-3">
    <div class="container-fluid py-5">
        <h1 class="display-5 fw-bold"><?php echo $title; ?></h1>
        <p class="col-md-8 fs-4">This is your secure, custom framework.</p>
        <div class="alert alert-info">
            <strong>System Check:</strong> <?php echo $db_status; ?>
        </div>
        <button class="btn btn-primary btn-lg" type="button">Action</button>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>
