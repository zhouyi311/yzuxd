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
            echo "<div class='article_section_leading'" . (isset($leadingImgBg) ? "style='background-color:{$leadingImgBg}'" : null) . ">";
            echo "<div class='image_wrapper'><img class='leading_image' src='" . $projPath . $leadingImg . "' alt='{$headline} section leading image'>";
            echo "</div></div>";
        }
    }

    private function renderSection($section) {
        // logic for rendering the section
        $projPath = $this->projPath;
        $headline = $section['headline'];
        $subhead = isset($section['subhead']) ? htmlspecialchars($section['subhead']) : null;
        $subheadCaption = isset($section['subheadCaption']) ? htmlspecialchars($section['subheadCaption']) : null;
        $subheadList = isset($section['subheadList']) ? ($section['subheadList']) : null;

        

        // Depending on the content type, call the respective method
        foreach ($section['content'] as $index => $contentItem) {
            $type = $contentItem['type'];
            if (method_exists($this, "render{$type}")) {
                $this->{"render{$type}"}($contentItem['content']);
            }
        }
    }

    private function renderText($content) {
        echo "<p>" . htmlspecialchars($content) . "</p>";
    }

    private function renderImage($content) {
        echo "<img class='article_image' src='" . $this->projPath . $content . "'>";
    }

}

?>