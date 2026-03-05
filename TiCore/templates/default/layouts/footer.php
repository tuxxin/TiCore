<?php
$_tcVersion = file_exists(CORE_PATH . '/.v') ? trim(file_get_contents(CORE_PATH . '/.v')) : '?';
?>
</main><!-- /#main-content -->

<footer class="bg-dark text-secondary mt-5 py-3" role="contentinfo" aria-label="Site footer">
    <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
        <span>&copy; <?php echo date('Y'); ?> TiCore &mdash; <em>Tuxxin Integrated Core</em> &mdash;
            <a href="https://tuxxin.com" class="text-secondary" rel="noopener noreferrer" target="_blank">Tuxxin</a>
        </span>
        <a href="https://buymeacoffee.com/tuxxin"
           target="_blank"
           rel="noopener noreferrer"
           class="text-warning text-decoration-none small"
           aria-label="Support TiCore — Buy Me a Coffee">
            &#9749; Buy Me a Coffee
        </a>
        <button type="button"
                class="btn btn-link text-secondary text-decoration-none small p-0"
                data-bs-toggle="modal"
                data-bs-target="#tcGithubModal"
                aria-haspopup="dialog"
                aria-label="View TiCore on GitHub">
            &#128279; GitHub
        </button>
        <span class="small" aria-label="Framework version <?php echo htmlspecialchars($_tcVersion, ENT_QUOTES, 'UTF-8'); ?>">
            v<?php echo htmlspecialchars($_tcVersion, ENT_QUOTES, 'UTF-8'); ?>
        </span>
    </div>
</footer>

<!-- ── GitHub modal ─────────────────────────────────────────────────────────── -->
<div class="modal fade" id="tcGithubModal" tabindex="-1"
     aria-labelledby="tcGithubModalLabel" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h2 class="modal-title fs-5" id="tcGithubModalLabel">&#11088; Support TiCore</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="mb-4">
                    TiCore is <strong>free and open source</strong>. If it&rsquo;s saved you time,
                    consider supporting ongoing development &mdash; it keeps TiCore online and
                    actively maintained.
                </p>
                <a href="https://buymeacoffee.com/tuxxin"
                   target="_blank" rel="noopener noreferrer"
                   class="btn btn-warning btn-lg mb-4"
                   aria-label="Donate via Buy Me a Coffee (opens in new tab)">
                    &#9749; Buy Me a Coffee
                </a>
                <div class="progress mb-2" style="height:5px;"
                     role="progressbar" aria-label="Time until GitHub opens"
                     aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                    <div id="tcGithubProgress" class="progress-bar bg-secondary" style="width:100%;"></div>
                </div>
                <p class="text-muted small mb-0">
                    Opening GitHub in <strong id="tcGithubCountdown">10</strong> seconds&hellip;
                </p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                <button type="button" class="btn btn-dark" id="tcGithubNow">
                    Go to GitHub now
                </button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS (required for responsive navbar toggle) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>

<!-- ── GitHub modal countdown ───────────────────────────────────────────────── -->
<script>
(function () {
    var GITHUB_URL  = 'https://github.com/tuxxin/TiCore';
    var DURATION    = 10;
    var modalEl     = document.getElementById('tcGithubModal');
    var countEl     = document.getElementById('tcGithubCountdown');
    var progressEl  = document.getElementById('tcGithubProgress');
    var nowBtn      = document.getElementById('tcGithubNow');
    var timer       = null;

    function openGitHub() {
        window.open(GITHUB_URL, '_blank', 'noopener,noreferrer');
    }

    function stopTimer() {
        if (timer) { clearInterval(timer); timer = null; }
    }

    function resetModal() {
        countEl.textContent = DURATION;
        progressEl.style.width = '100%';
        progressEl.setAttribute('aria-valuenow', '100');
    }

    modalEl.addEventListener('shown.bs.modal', function () {
        var seconds = DURATION;
        resetModal();
        timer = setInterval(function () {
            seconds--;
            var pct = Math.round(seconds / DURATION * 100);
            countEl.textContent = seconds;
            progressEl.style.width = pct + '%';
            progressEl.setAttribute('aria-valuenow', pct);
            if (seconds <= 0) {
                stopTimer();
                openGitHub();
                bootstrap.Modal.getInstance(modalEl).hide();
            }
        }, 1000);
    });

    modalEl.addEventListener('hidden.bs.modal', function () {
        stopTimer();
        resetModal();
    });

    nowBtn.addEventListener('click', function () {
        stopTimer();
        openGitHub();
        bootstrap.Modal.getInstance(modalEl).hide();
    });
}());
</script>

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
