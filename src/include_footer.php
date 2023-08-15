<!-- footer -->
<footer class="bg-light py-4">
    <div class="container">
        <div class="row mb-3">
            <div class="text-center text-dark">

                <img class="mb-2" src="src/img/favicon/logo.svg" alt="footer logo" style="height:30px; filter: filter: contrast(10%);">
                <div class="fw-bold text-uppercase fs-6">
                    <a class="text-dark text-decoration-none" href="<?php echo $siteInfo->rootUrl; ?>">
                        <?php echo htmlspecialchars($siteInfo->sitename); ?>
                    </a>
                </div>
                <div class="fw-medium">
                    <?php echo htmlspecialchars($siteInfo->information['siteTitle']); ?>
                </div>
            </div>
        </div>
        <div class="row mb-1">
            <div class="d-flex justify-content-center gap-1 gap-md-3 text-secondary overflow-hidden">
                <div>
                    <a class="text-secondary text-decoration-none " href="<?php echo htmlspecialchars($siteInfo->information["myLinkedin"]); ?>" target="_blank">LinkedIn</a>
                </div>|
                <div>
                    <a class="text-secondary text-decoration-none " href="<?php echo htmlspecialchars($siteInfo->information["myGithub"]); ?>" target="_blank">GitHub</a>
                </div>|
                <div>
                    <a class="text-secondary text-decoration-none " href="<?php echo htmlspecialchars($siteInfo->information["myTwitter"]); ?>" target="_blank">Twitter</a>
                </div>|
                <div>
                    <a class="text-secondary text-decoration-none " href="mailto:<?php echo htmlspecialchars($siteInfo->information["myEmail"]); ?>">Email</a>
                </div>|
                <div>
                    <a class="text-secondary text-decoration-none " href="<?php echo $siteInfo->rootUrl . "?" . $siteInfo->pageKeys['archiveKey']; ?>">Archive</a>
                </div>|
                <div>
                    <a class="text-secondary text-decoration-none " href="<?php echo $siteInfo->rootUrl . '/sitemap.xml'; ?>">Sitemap</a>
                </div>
                
                
            </div>
        </div>
        <div class="row text-secondary">
            <div class="col">
                <div class="text-center">
                    Copyright &copy;
                    <?php echo htmlspecialchars($siteInfo->information["siteCopyright"]); ?>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>
<script src="src/js/main.js?v=4907"></script>

<?php
$allowed_hosts = ['zhouyiwork.com', 'yzuxd.com'];
$http_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
if (in_array($http_host, $allowed_hosts)): ?>
    <!-- Google tag (gtag.js) -->
    <script async src='https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars($siteInfo->information["siteGaTag"]); ?>'></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', '<?php echo htmlspecialchars($siteInfo->information["siteGaTag"]); ?>');
    </script>
    <?php
endif;
?>