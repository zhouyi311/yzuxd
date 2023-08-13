<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once __DIR__ . '/class_info_site.php';
include_once __DIR__ . '/class_info_project.php';
include_once __DIR__ . '/class_renderer_header.php';
include_once __DIR__ . '/class_renderer_cards.php';

$siteInfo = SiteInfo::loadInfo();
$projects = ProjectInfo::loadAll();

$siteHeaderRenderer = new HeaderRenderer('home');
$projectCardsRenderer = new ProjectCardsRenderer();

?>

<!DOCTYPE html>
<html lang="en">
<?php include __DIR__ . '/include_head.php'; ?>

<body id="home_page_root">
    <div id="home_page_wrapper" class="page_wrapper">
        <!-- header -->
        <?php echo $siteHeaderRenderer->render(); ?>
        <!-- Main -->
        <main id="home_main" data-bs-spy="scroll" data-bs-target="#navbar_target" data-bs-root-margin="0px 0px -25%" data-bs-smooth-scroll="true">
            <!-- hero -->
            <section class="homepage_home page_section" id="home">
                <div class="container">
                    <div class="row">
                        <div class='col-12'>
                            <div class="hero_card px-4 py-4 px-md-0 py-md-0 rounded-5">
                                <!-- hero card -->
                                <div class="col-12">
                                    <h2 class="text-white display-4 mt-3">
                                        <?php echo htmlspecialchars($siteInfo->frontPageContent["heroGreeting"]); ?>
                                    </h2>
                                    <h1 class="text-white text_hero">
                                        <?php echo $siteInfo->frontPageContent["heroHeadline"]; ?>
                                    </h1>
                                </div>
                                <div class='col-md-7 col-lg-5'>
                                    <div class="text-white">
                                        <p class="lead text-light">
                                            <?php echo htmlspecialchars($siteInfo->frontPageContent["heroIntroduction"]); ?>
                                        </p>
                                    </div>
                                    <div class="text-white-50">
                                        <?php
                                        foreach ($siteInfo->frontPageContent["heroParagraphsArray"] as $heroParagraph) {
                                            $heroParagraph = htmlspecialchars($heroParagraph);
                                            echo "<p>$heroParagraph</p> ";
                                        }
                                        ?>
                                    </div>
                                    <div class="call_to_action_group mb-4 mt-5">
                                        <a class="hero_btn btn btn-dark btn-lg border-0 rounded-pill px-5" href="#projects">
                                            <?php echo htmlspecialchars($siteInfo->frontPageContent["heroCallToAction"]); ?>
                                            <i class="bi bi-arrow-down-short align-middle"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg_container">
                    <!-- hero decoration -->
                    <div class="hero_decoration_container">
                        <!-- <div id="particles-js"></div> -->

                        <canvas id="canvas-bg" width="800" height="800">
                            <!-- Your Browser Don't Support Canvas, Please Download Chrome ^_^ -->
                        </canvas>
                        <div class="extra_wrapper">
                            <div class="container-xxl">
                                <div class="row">
                                    <div class="col-12 col-md-7 offset-md-5 col-lg-8 offset-lg-4 col-xl-8 offset-xl-4 bg_positioner none_select">
                                        <div class="dec_bg">
                                        </div>
                                        <div class="dec_items">
                                            <div id="hero_moveable">
                                                <div class="hero_self_tilting">
                                                    <div class="hero_tilting_group">
                                                        <img class="hero_decoration ztrans_0" src="src/img/ui-dec-00.png?v=h_2" alt="banner decoration image"></img>
                                                        <img class="hero_decoration extra_layer ztrans_1" src="src/img/ui-dec-01.png?v=h_2" alt="banner decoration image" aria-hidden="true"></img>
                                                        <img class="hero_decoration extra_layer ztrans_2" src="src/img/ui-dec-02.png?v=h_2" alt="banner decoration image" aria-hidden="true"></img>
                                                        <img class="hero_decoration extra_layer ztrans_3" src="src/img/ui-dec-03.png?v=h_2" alt="banner decoration image" aria-hidden="true"></img>
                                                        <img class="hero_decoration extra_layer ztrans_4" src="src/img/ui-dec-04.png?v=h_2" alt="banner decoration image" aria-hidden="true"></img>
                                                        <img class="hero_decoration extra_layer ztrans_5" src="src/img/ui-dec-05.png?v=h_2" alt="banner decoration image" aria-hidden="true"></img>
                                                        <img class="hero_decoration extra_layer ztrans_6" src="src/img/ui-dec-06.png?v=h_2" alt="banner decoration image" aria-hidden="true"></img>
                                                        <img class="hero_decoration extra_layer ztrans_7" src="src/img/ui-dec-07.png?v=h_2" alt="banner decoration image" aria-hidden="true"></img>
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
            </section>
            <!-- projects -->
            <?php /////////////////////////////////////////////////////////////////////////////////////////////////////////////////// ?>
            <section class="page_section bg-white" id="projects">
                <div class="container">
                    <div class="row">
                        <div class="section_headline">
                            <h2 class=" text-uppercase text-black">Projects</h2>
                        </div>
                    </div>
                </div>
                <?php
                $projectCardsRenderer->render();
                ?>
            </section>
            <!-- contact -->
            <section class="page_section" id="contact">
                <div class="bg_container"></div>
                <div class="container">
                    <div class="row gy-5">
                        <div class="col-xl-6 offset-xl-1 col-lg-7 order-2 ">
                            <div class="contact_card rounded-5 text-dark p-5 shadow-lg-5">
                                <h2 class="text-black text_hero2 mb-3">What's up!</h2>
                                <h2 class="text-black">Let's Make Some Noise Together!</h2>
                                <form id="contactForm" class="contact_form my-5 text-dark fw-medium">
                                    <div class="form-floating mb-2 fw-light">
                                        <input id="msg_submit_email" class="form-control border-2 rounded-4 border-secondary fw-light" type="email" name="fromEmail" placeholder="Your email" required>
                                        <label class="floatingInput border-0 bg-transparent" for="msg_submit_email">Your
                                            Email</label>
                                    </div>
                                    <div class="form-floating fw-light">
                                        <textarea id="msg_submit_content" class="form-control border-2 rounded-4 border-secondary fw-light" name="message" placeholder="Your message" style="height: 6rem" required></textarea>
                                        <label class="floatingInput border-0 bg-transparent" for="msg_submit_content">Your
                                            Message</label>
                                    </div>

                                    <div id="formOutput" class="text-dark my-3"></div>

                                    <div class="d-grid gap-2">
                                        <button class="btn btn-dark rounded-pill fw-bold" type="submit">
                                            Send Message
                                        </button>
                                    </div>
                                </form>

                                <h3 class="section_headline text-uppercase visually-hidden">Contact</h3>
                                <div class="contact_sheet text-nowrap">

                                    <h4>EMAIL</h4>
                                    <div class="mb-4">
                                        <a class="text-dark link-offset-3 text-decoration-none" href="mailto:<?php echo htmlspecialchars($siteInfo->information['myEmail']); ?>">
                                            <?php echo htmlspecialchars($siteInfo->information['myEmail']); ?>
                                        </a>
                                    </div>

                                    <h4>LOCATION</h4>
                                    <div class="mb-4">
                                        <?php echo htmlspecialchars($siteInfo->information['myLocation']); ?>
                                    </div>

                                    <h4>SOCIAL NETWORK</h4>
                                    <div class="d-flex gap-4 mb-3">
                                        <div>
                                            <a class="text-dark link-offset-3" target="_blank" href="<?php echo htmlspecialchars($siteInfo->information['myLinkedin']); ?>">
                                                <i class=" bi bi-linkedin align-middle" style="font-size: 1.5rem;"></i>
                                            </a>
                                        </div>
                                        <div>
                                            <a class="text-dark link-offset-3" target="_blank" href="<?php echo htmlspecialchars($siteInfo->information['myGithub']); ?>">
                                                <i class=" bi bi-github align-middle" style="font-size: 1.5rem;"></i>
                                            </a>
                                        </div>
                                        <div>
                                            <a class="text-dark link-offset-3" target="_blank" href="<?php echo htmlspecialchars($siteInfo->information['myTwitter']); ?>">
                                                <i class=" bi bi-twitter align-middle" style="font-size: 1.5rem;"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="contact_dec_flex_wrap h-100 d-flex flex-column justify-content-center align-items-center">
                                <div class="contact_decoration_container none_select">
                                    <div id="contact_moveable">
                                        <div class="contact_self_tilting">
                                            <div class="contact_tilting_group">
                                                <img class="contact_decoration none_select base_layer" src="src/img/phone-base.png?v=z021" alt="image decoration" aria-hidden="true">
                                                <img class="contact_decoration none_select extra_layer extra_rise_btn" src="src/img/phone-btn.png?v=z021" alt="image decoration" aria-hidden="true">
                                                <img class="contact_decoration none_select extra_layer extra_rise_0" src="src/img/phone-face.svg?v=z021" alt="image decoration" aria-hidden="true">
                                                <img class="contact_decoration none_select extra_layer extra_rise_1 " src="src/img/phone-icon.png?v=z021" alt="image decoration" aria-hidden="true">
                                                <img class="contact_decoration none_select extra_layer extra_rise_2 " src="src/img/phone-icon-1.svg?v=z021" alt="image decoration" aria-hidden="true">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <?php include __DIR__ . '/include_footer.php'; ?>
        <script src="src/js/page_home.js?v=429" type="text/javascript"></script>
    </div>
</body>

</html>