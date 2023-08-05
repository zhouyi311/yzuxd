<!-- footer -->
<footer class="bg-light py-4">
    <div class="container">
        <div class="row mb-3">
            <div class="text-center text-dark">
                <a class="text-dark text-decoration-none"
                    href="<?php echo htmlspecialchars($site_info->information["siteAddress"]); ?>">
                    <img class="mb-2" src="src/img/favicon/logo.svg" alt="footer logo"
                        style="height:30px; filter: filter: contrast(10%);">
                    <div class="fw-bold text-uppercase fs-6">
                        <?php echo htmlspecialchars($site_info->sitename); ?>
                    </div>
                </a>
                <div class="fw-medium">
                    <?php echo htmlspecialchars($site_info->information['siteTitle']); ?>
                </div>
            </div>
        </div>
        <div class="row mb-1">
            <div class="d-flex justify-content-center gap-3 text-secondary">
                <div>
                    <a class="text-secondary text-decoration-none "
                        href="mailto:<?php echo htmlspecialchars($site_info->information["myEmail"]); ?>">Email</a>
                </div>|
                <div>
                    <a class="text-secondary text-decoration-none "
                        href="<?php echo htmlspecialchars($site_info->information["myLinkedin"]); ?>" target="_blank">LinkedIn</a>
                </div>|
                <div>
                    <a class="text-secondary text-decoration-none "
                        href="<?php echo htmlspecialchars($site_info->information["myGithub"]); ?>" target="_blank">GitHub</a>
                </div>|
                <div>
                    <a class="text-secondary text-decoration-none "
                        href="<?php echo htmlspecialchars($site_info->information["myTwitter"]); ?>" target="_blank">Twitter</a>
                </div>
            </div>
        </div>
        <div class="row text-secondary">
            <div class="col">
                <div class="text-center">
                    Copyright &copy;
                    <?php echo htmlspecialchars($site_info->information["siteCopyright"]); ?>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>

<script src="src/js/main.js?v=2"></script>

<?php
$allowed_hosts = ['zhouyiwork.com', 'yzuxd.com'];
$http_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

if (in_array($http_host, $allowed_hosts)): ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-S9WYGL5L78"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', 'G-S9WYGL5L78');
    </script>
<?php endif; ?>