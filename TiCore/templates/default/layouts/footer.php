<?php
$_tcVersion = file_exists(CORE_PATH . '/.v') ? trim(file_get_contents(CORE_PATH . '/.v')) : '?';
?>
</main><!-- /#main-content -->

<footer class="bg-dark text-secondary mt-5 py-3" role="contentinfo" aria-label="Site footer">
    <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
        <span>&copy; <?php echo date('Y'); ?> TiCore Framework &mdash;
            <a href="https://tuxxin.com" class="text-secondary" rel="noopener noreferrer" target="_blank">Tuxxin</a>
        </span>
        <a href="https://buymeacoffee.com/tuxxin"
           target="_blank"
           rel="noopener noreferrer"
           class="text-warning text-decoration-none small"
           aria-label="Support TiCore — Buy Me a Coffee">
            &#9749; Buy Me a Coffee
        </a>
        <span class="small" aria-label="Framework version <?php echo htmlspecialchars($_tcVersion, ENT_QUOTES, 'UTF-8'); ?>">
            v<?php echo htmlspecialchars($_tcVersion, ENT_QUOTES, 'UTF-8'); ?>
        </span>
    </div>
</footer>

<!-- Bootstrap JS (required for responsive navbar toggle) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>

<!-- ── Buy Me a Coffee floating widget ──────────────────────────────────────── -->
<script data-name="BMC-Widget"
        data-cfasync="false"
        src="https://cdnjs.buymeacoffee.com/1.0.0/widget.prod.min.js"
        data-id="tuxxin"
        data-description="Support TiCore development!"
        data-message=""
        data-color="#40DCA5"
        data-position="Right"
        data-x_margin="18"
        data-y_margin="18">
</script>
</body>
</html>
