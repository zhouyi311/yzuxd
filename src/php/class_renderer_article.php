<?php
class ArticleContentRenderer
{

    private $project;
    private $projPath;

    public function __construct($project)
    {
        $this->project = $project;
        $this->projPath = $project->path;
    }

    public function render()
    {
        foreach ($this->project->article as $section) {
            $this->renderLeadingImage($section);
            $this->renderSection($section);
        }
    }

    private function findLargerImage($path, $filename)
    {

        $dirPath = __DIR__ . '/../..' . $path;
        $info = pathinfo($filename);
        $baseName = $info['filename'];
        // $extension = $info['extension'];

        // Construct the pattern to search for
        $pattern = $dirPath . "/" . $baseName . '*.' . "*";

        // Use glob to find matching files
        $matchingFiles = glob($pattern);

        if (!$matchingFiles) {
            return null; // No matching files found
        }

        // If there are matching files, find the largest by file size
        $largestFile = '';
        $largestSize = 0;
        foreach ($matchingFiles as $file) {
            $size = filesize($file);
            if ($size > $largestSize) {
                $largestSize = $size;
                $largestFile = $file;
            }
        }
        return basename($largestFile);
        // return $largestFile;
    }


    private function renderLeadingImage($section)
    {
        $projPath = $this->projPath . "/";
        $headline = $section['headline'];
        $leadingImg = !empty($section['leadingImg']) ? htmlspecialchars($section['leadingImg']) : null;
        $leadingImgFixed = !empty($section['leadingImgFixed']) ? htmlspecialchars($section['leadingImgFixed']) : null;
        $leadingImgBg = !empty($section['leadingImgBgColor']) ? htmlspecialchars($section['leadingImgBgColor']) : null;
        if (!empty($leadingImgFixed)) {
            echo "<div style='background-image: url(" . $projPath . $leadingImgFixed . ")' class='article_section_leading bg_attach'></div>";
        } elseif (!empty($leadingImg)) {
            echo "<div class='article_section_leading none_select'" . (!empty($leadingImgBg) ? "style='background-color:{$leadingImgBg}'" : null) . ">";
            echo "<div class='image_wrapper none_select'><img class='leading_image none_select' src='" . $projPath . $leadingImg . "' alt='{$headline} section leading image'>";
            echo "</div></div>";
        }
    }

    private function renderSection($section)
    {
        // logic for rendering the section
        $projPath = $this->projPath . "/";
        $headline = $section['headline'];
        $headlineId = $section['headlineId'];
        $subhead = !empty($section['subhead']) ? htmlspecialchars($section['subhead']) : null;
        $subheadCaption = !empty($section['subheadCaption']) ? htmlspecialchars($section['subheadCaption']) : null;
        $subheadList = !empty($section['subheadList']) ? ($section['subheadList']) : null;

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
            if (method_exists($this, "{$type}Render")) {
                $this->{"{$type}Render"}($contentItem);
            }else{
                echo "<div class='media_block'>";
                echo "<p>".var_dump($contentItem)."</p>";
                echo "<p>Error: section ({$section['headline']}): content index($index) missing ($type)render</p>";
                echo "</div>";
            }
        }
        echo "</div>";
        echo "</div></div>";
        echo "</section>";
    }

    private function textRender($content)
    {
        $data = $content['data'];
        $iswide = !empty($content['wide']) ? "" : "col-lg-10 offset-lg-1";

        if (is_array($data)) {
            foreach ($data as $index => $paragraph){
                echo "<p class='article_paragraph p_group_index_$index'>" . htmlspecialchars($paragraph) . "</p>";
            }
        } elseif (is_string($data)) {
            echo "<div class='{$iswide} media_block'><p class='article_paragraph'>" . htmlspecialchars($content['data']) . "</p></div>";
        } else {
            echo "<p>Empty Data</p>";
        }
    }

    private function imageRender($content)
    {
        $filename = !empty($content['data']) ? htmlspecialchars($content['data']) : null;
        $caption = !empty($content['caption']) ? htmlspecialchars($content['caption']) : null;
        $headline = !empty($content['headline']) ? htmlspecialchars($content['headline']) : null;
        $lightbox = !empty($content['lightbox']) ? $content['lightbox'] : null;
        $iswide = !empty($content['wide']) ? "" : " col-lg-10 offset-lg-1 ";
        $path = $this->projPath;

        if (!empty($filename)) {
            echo "<figure class='media_block article_image text-center $iswide'>";
            echo !empty($headline) ? "<h6 class='media_headline $iswide'>$headline</h6>" : null;
            echo "<img src='{$path}/{$filename}' class='article_image rounded-2";
            if (isset($lightbox) && is_string($lightbox)) {
                echo " lightbox-enabled' data-larger-src='" . htmlspecialchars($lightbox) . "'>";
            } elseif (isset($lightbox)) {
                $largerImage = $this->findLargerImage($path, $filename);
                echo " lightbox-enabled' data-larger-src='" . htmlspecialchars($largerImage) . "'>";
            } else {
                echo "'>";
            }
            echo !empty($caption) ? "<figcaption class='media_caption text-body-tertiary fst-italic $iswide'>{$caption}</figcaption>" : null;
            echo "</figure>";
        }else{
            echo "<p>Empty Data</p>";
        }


    }
    private function iframeRender($content)
    {
        $urlpath = !empty($content['data']) ? htmlspecialchars($content['data']) : null;
        $headline = !empty($content['headline']) ? htmlspecialchars($content['headline']) : null;
        $caption = !empty($content['caption']) ? htmlspecialchars($content['caption']) : null;
        $iswide = !empty($content['wide']) ? "" : " col-lg-10 offset-lg-1 ";

        if (isset($urlpath)) {
            echo "<div class='media_block iframe_container text-center {$iswide}'>";
            echo !empty($headline) ? "<h6 class='media_headline'>{$headline}</h6>" : null;
            echo "<iframe width='100%' height='600px' src='$urlpath'></iframe>";
            echo !empty($caption) ? "<div class='media_caption mt-0'>{$caption}</div></div>" : "</div>";
        } else {
            echo "<p>Empty Data</p>";
        }
    }
}

?>