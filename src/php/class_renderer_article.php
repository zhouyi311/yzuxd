<?php
class ArticleContentRenderer {

    private $project;
    private $projPath;

    public function __construct($project) {
        $this->project = $project;
        $this->projPath = $project->path;
    }

    public function render() {
        foreach ($this->project->article as $section) {
            $this->renderLeadingImage($section);
            $this->renderSection($section);
        }
    }

    private function renderLeadingImage($section) {
        $projPath = $this->projPath;
        $headline = $section['headline'];
        $leadingImg = isset($section['leadingImg']) ? htmlspecialchars($section['leadingImg']) : null;
        $leadingImgFixed = isset($section['leadingImgFixed']) ? htmlspecialchars($section['leadingImgFixed']) : null;
        $leadingImgBg = isset($section['leadingImgBgColor']) ? htmlspecialchars($section['leadingImgBgColor']) : null;
        if (isset($leadingImgFixed)) {
            echo "<div style='background-image: url(" . $projPath . $leadingImgFixed . ")' class='article_section_leading bg_attach'></div>";
        } elseif (isset($leadingImg)) {
            echo "<div class='article_section_leading none_select'" . (isset($leadingImgBg) ? "style='background-color:{$leadingImgBg}'" : null) . ">";
            echo "<div class='image_wrapper none_select'><img class='leading_image none_select' src='" . $projPath . $leadingImg . "' alt='{$headline} section leading image'>";
            echo "</div></div>";
        }
    }

    private function renderSection($section) {
        // logic for rendering the section
        // $projPath = $this->projPath;
        $headline = $section['headline'];
        $headlineId = $section['headlineId'];
        $subhead = isset($section['subhead']) ? htmlspecialchars($section['subhead']) : null;
        $subheadCaption = isset($section['subheadCaption']) ? htmlspecialchars($section['subheadCaption']) : null;
        $subheadList = isset($section['subheadList']) ? ($section['subheadList']) : null;

        echo "<section class='page_section article_section' id='{$headlineId}'>";
        echo "<div class='container'><div class='row'>";

        echo "<div class='section_headline col-lg-4 pe-lg-5'>";
        if (isset($subhead)) {
            echo "<h6 class='text-secondary'>$subhead</h6>";
        }
        echo "<h2 id='{$headlineId}' class='mb-3'>{$headline}</h2>";
        if (isset($subheadCaption)) {
            echo "<p class='text-body-tertiary'>$subheadCaption</p>";
        }
        if (isset($subheadList)) {
            echo "<ul class='subhead_list'>";
            foreach ($subheadList as $i => $item) {
                $item = htmlspecialchars($item);
                echo $i === 0 ? "<li class='fw-bold'> $item</li>" : "<li class=''>$item</li>";
            }
            echo "</ul>";
        }
        echo "</div>";

        // Depending on the content type, call the respective method
        echo "<div class='col-lg-8'>";
        foreach ($section['content'] as $index => $contentItem) {
            $type = $contentItem['type'];
            if (method_exists($this, "render{$type}")) {
                $this->{"render{$type}"}($contentItem['content']);
            }
        }
        echo "</div>";
        echo "</div></div>";
        echo "</section>";
    }

    private function renderText($content) {
        echo "<p>" . htmlspecialchars($content) . "</p>";
    }

    private function renderImage($content) {
        echo "<img class='article_image lightbox-enabled' src='" . $this->projPath . $content . "'>";
    }

}

?>