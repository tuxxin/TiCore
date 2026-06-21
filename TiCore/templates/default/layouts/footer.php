<?php
$_tcVersion = file_exists(CORE_PATH . '/.v') ? trim(file_get_contents(CORE_PATH . '/.v')) : '?';
?>
</main><!-- /#main-content -->

<footer class="bg-dark text-secondary mt-5 py-3" role="contentinfo" aria-label="Site footer">
    <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
        <span>&copy; <?php echo date('Y'); ?>
            <?php echo htmlspecialchars(defined('SITE_TITLE') ? SITE_TITLE : 'TiCore', ENT_QUOTES, 'UTF-8'); ?>
            &mdash; powered by
            <a href="https://github.com/tuxxin/TiCore" class="text-secondary" rel="noopener noreferrer" target="_blank">TiCore</a>
        </span>
        <a href="https://github.com/tuxxin/TiCore" target="_blank" rel="noopener noreferrer"
           class="text-secondary text-decoration-none small" aria-label="TiCore on GitHub">&#128279; GitHub</a>
        <span class="small" aria-label="Framework version <?php echo htmlspecialchars($_tcVersion, ENT_QUOTES, 'UTF-8'); ?>">
            v<?php echo htmlspecialchars($_tcVersion, ENT_QUOTES, 'UTF-8'); ?>
        </span>
    </div>
</footer>

<!-- Bootstrap JS (required for responsive navbar toggle) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
</body>
</html>
