<?php
class ArticleContentRenderer
{

    private $project;
    private $projPath;
    private $narrow = " col-lg-8 offset-lg-2 ";
    private $narrow2 = " col-lg-10 offset-lg-1 ";
    private $narrow3 = " col-lg-8 offset-lg-3 ";
    private $narrow4 = " col-lg-8 offset-lg-4 ";

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

    private function sanitizeValue($array, $key, $sanitize = true)
    {
        if (!empty($array[$key])) {
            if ($sanitize) {
                if (is_string($array[$key])) {
                    return htmlspecialchars($array[$key]);
                } elseif (is_array($array[$key])) {
                    foreach ($array[$key] as &$value) {
                        if (is_string($value)) {
                            $value = htmlspecialchars($value);
                        }
                    }
                    unset($value); // To break the reference with the last element
                    return $array[$key];
                }
            }
            return $array[$key];
        }
        return null;
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
        $projPath = $this->projPath;
        $headline = $this->sanitizeValue($section, 'headline');
        $leadingImg = $this->sanitizeValue($section, 'leadingImg');
        $leadingImgFixed = $this->sanitizeValue($section, 'leadingImgFixed');
        $leadingImgBgColor = $this->sanitizeValue($section, 'leadingImgBgColor');
        $leadingImgBgColor = !empty($leadingImgBgColor) ? "style='background-color: {$leadingImgBgColor}'" : null;

        if (!empty($leadingImgFixed)) {
            echo "<div style='background-image: url({$projPath}/{$leadingImgFixed})' class='article_section_leading bg_attach'></div>";
        } elseif (!empty($leadingImg)) {
            echo "<div class='article_section_leading none_select' $leadingImgBgColor>";
            echo "<div class='image_wrapper none_select'><img class='leading_image none_select' src='{$projPath}/{$leadingImg}' alt='{$headline} section leading image'>";
            echo "</div></div>";
        }
    }

    private function renderSection($section)
    {
        // logic for rendering the section
        // $projPath = $this->projPath . "/";
        $headline = $this->sanitizeValue($section, 'headline');
        $headlineId = $this->sanitizeValue($section, 'headlineId');
        $subhead = $this->sanitizeValue($section, 'subhead');
        $subheadCaption = $this->sanitizeValue($section, 'subheadCaption');
        // $subheadCaption = $section['subheadCaption'];
        $subheadList = $this->sanitizeValue($section, 'subheadList', false);

        echo "<section class='page_section article_section' id='{$headlineId}'>";
        echo "<div class='container'><div class='row'>";

        echo "<div class='section_headline col-lg-3 pe-lg-4'>";
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
        echo "<div class='col-lg-9'>";
        foreach ($section['content'] as $index => $contentItem) {
            $type = $contentItem['type'];
            if (method_exists($this, "{$type}Render")) {
                echo "<div class='row'>";
                $this->{"{$type}Render"}($contentItem);
                echo "</div>";
            } else {
                echo "<div class='media_block col'>";
                echo "<p>Error: section ({$section['headline']}): content index($index) missing ($type)render</p>";
                echo "<p>" . var_dump($contentItem) . "</p>";
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
        is_string($data) ? $data = array($data) : null;
        $headline = !empty($content['headline']) ? htmlspecialchars($content['headline']) : null;
        $iswide = !empty($content['wide']) ? " col " : $this->narrow;
        $lead = isset($content['lead']) && !empty($content['lead']) && !is_integer($content['lead']) ? 1 : ($content['lead'] ?? null);


        if (is_array($data)) {
            echo "<div class='media_block $iswide'>";
            echo !empty($headline) ? "<h5 class='paragraph_headline'>$headline</h5>" : null;
            foreach ($data as $index => $paragraph) {
                // echo $index . "-" . $lead;
                echo "<p class='article_paragraph ";
                if ($index < $lead) {
                    echo "lead ";
                }
                echo "p_group_index_$index'>" . htmlspecialchars($paragraph) . "</p>";
            }
            echo "</div>";
        } else {
            echo "<p>Empty data or none supported data:</p>";
            echo "<p>" . var_dump($data) . "</p>";
        }
    }

    private function imageRender($content)
    {
        $filename = !empty($content['data']) ? $content['data'] : null;
        $caption = !empty($content['caption']) ? htmlspecialchars($content['caption']) : null;
        $headline = !empty($content['headline']) ? htmlspecialchars($content['headline']) : null;
        $lightbox = !empty($content['lightbox']) ? $content['lightbox'] : null;
        $iswide = !empty($content['wide']) ? " col " : $this->narrow;
        $path = $this->projPath;

        if (!empty($filename)) {

            is_string($filename) ? $filename = array($filename) : null;
            // $multiCol = count($filename) > 1 ? true : false;

            echo "<figure class='media_block article_image $iswide'>";
            echo !empty($headline) ? "<h6 class='media_headline'>$headline</h6>" : null;
            echo "<div class='row gy-4'>";
            foreach ($filename as $image) {

                if (count($filename) % 2 == 0 && count($filename) <= 4) {
                    echo "<div class='col-6'>";
                } elseif (count($filename) == 3 || count($filename) > 4) {
                    echo "<div class='col-4'>";
                } else {
                    echo "<div class='col'>";
                }

                echo "<img src='{$path}/" . htmlspecialchars($image) . "' class='article_image rounded-2";
                if (isset($lightbox) && is_string($lightbox)) {
                    echo " lightbox-enabled' data-larger-src='" . htmlspecialchars($lightbox) . "'>";
                } elseif (isset($lightbox)) {
                    $largerImage = $this->findLargerImage($path, $image);
                    echo " lightbox-enabled' data-larger-src='" . htmlspecialchars($largerImage) . "'>";
                } else {
                    echo "'>";
                }

                echo "</div>";
            }
            echo "</div>";
            echo !empty($caption) ? "<figcaption class='media_caption text-body-tertiary fst-italic'>{$caption}</figcaption>" : null;
            echo "</figure>";
        } else {
            echo "<p>Empty data or none supported data:</p>";
            echo "<p>" . var_dump($filename) . "</p>";
        }


    }
    private function iframeRender($content)
    {
        $urlpath = !empty($content['data']) ? htmlspecialchars($content['data']) : null;
        $headline = !empty($content['headline']) ? htmlspecialchars($content['headline']) : null;
        $caption = !empty($content['caption']) ? htmlspecialchars($content['caption']) : null;
        $iswide = !empty($content['wide']) ? " col " : $this->narrow;

        if (isset($urlpath)) {
            $iFrameCrcId = crc32($urlpath);
            echo "<div class='media_block iframe_container {$iswide}'>";
            echo !empty($headline) ? "<h6 class='media_headline'>{$headline}</h6>" : null;
            echo "<div class='iframe_wrapper' id='wrapper_{$iFrameCrcId}'><iframe width='100%' height='100%' src='$urlpath' id='iframe_$iFrameCrcId'></iframe></div>";
            echo !empty($caption) ? "<div class='media_caption mt-0'>{$caption}</div></div>" : "</div>";
        } else {
            echo "<p>Empty data or none supported data:</p>";
            echo "<p>" . var_dump($$urlpath) . "</p>";
        }
    }

}

?>