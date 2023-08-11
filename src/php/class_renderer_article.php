<?php
class ArticleContentRenderer
{

    private $project;
    private $projPath;
    public $narrowCol = " col-lg-8 offset-lg-2 ";

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
    private function fluidProcessor($data)
    {
        $mainGrid = $colWithGutter = $this->narrowCol;
        $fixerGrid = $colAdaptive = " col ";

        if (!empty($data['fluid'])) {
            $mainGrid = $colAdaptive;
            $fixerGrid = $colWithGutter;

            if (!empty($data['fluidHeadline'])) {
                $fixerGrid = $colAdaptive;
            }
        }
        return [$mainGrid, $fixerGrid];
    }


    private function findLargerImage($path, $filename)
    {

        $dirPath = __DIR__ . '/../..' . $path;
        $info = pathinfo($filename);
        $baseName = $info['filename'];
        // $extension = $info['extension'];

        // Construct the pattern to search for
        $pattern = $dirPath . '/' . $baseName . '*.*';
        $matchingFiles = glob($pattern);

        if (!$matchingFiles) {
            return null; // No matching files found
        }

        $largestFile = '';
        $largestSize = 0;
        foreach ($matchingFiles as $file) {
            $size = filesize($file);
            if ($size > $largestSize) {
                $largestSize = $size;
                $largestFile = $file;
            }
        }
        return htmlspecialchars(basename($largestFile));
    }

    private function reportMediaTypeError($section, $block, $index)
    {
        $type = !empty($block['type']) ? strval($block['type']) : "empty";
        echo "<div class='media_block $this->narrowCol'><div class='alert alert-dark' role='alert'>";
        echo "<h5 class='mb-4'>Media Type Error - Wrong Media Type:</h5>";
        echo "<p class='lead hidden'>Check block: {$section['headline']}->content[$index]</p>";
        echo "<p>[$type] media type is not valid for formating; dumping affected data fields:</p><hr>";
        echo "<p>" . var_dump($block) . "</p></div></div>";
    }
    private function reportDataError($block)
    {
        $mediaType = !empty($block['type']) ? $block['type'] : 'empty';
        $data = !empty($block['data']) ? $block['data'] : null;
        $dataType = $data ? gettype($data) : null;
        echo "<div class='media_block $this->narrowCol'><div class='alert alert-dark' role='alert'>";
        echo "<h5 class='mb-3'>Data Type Error - " . ($data ? "Wrong Data Type:" : "Empty Data Field") . "</h5>";
        if ($data) {
            echo "<p'>[$mediaType] media type is not supporting \"data\" field in type [$dataType]; dumping affected data fields:</p><hr>";
            echo var_dump($block);
        } else {
            echo "<hr>";
            echo "[Empty Data]";
        }
        echo "</div></div>";
    }

    private function textBlockFormatter($block)
    {
        $fluid = $this->fluidProcessor($block);
        $paragraphs = $this->sanitizeValue($block, 'data');
        is_string($paragraphs) ? $paragraphs = array($paragraphs) : null;
        $headline = $this->sanitizeValue($block, 'headline');
        $caption = $this->sanitizeValue($block, 'caption');
        $cite = $this->sanitizeValue($block, 'cite');
        $quote = !empty($block['quote']) ? true : null;
        $lead = !empty($block['lead']) && !is_integer($block['lead']) ? 1 : ($block['lead'] ?? null);

        if (is_array($paragraphs)) {
            echo "<div class='media_block article_paragraphs $fluid[0]'>";
            echo !empty($quote) ? "<blockquote class='blockquote quote_container text-body-secondary p-5 rounded-5'>" : null;
            echo !empty($headline) ? "<div class='row'><div class='$fluid[1]'><h5 class='paragraph_headline'>$headline</h5></div></div>" : null;

            foreach ($paragraphs as $index => $paragraph) {
                echo "<p class='article_paragraph ";
                if ($index < $lead) {
                    echo "lead ";
                }
                echo "p_group_index_$index'>" . htmlspecialchars($paragraph) . "</p>";
            }

            echo $caption ? "<footer class='block_footer paragraph_caption'>$caption<cite>$cite</cite></footer>" : null;
            echo $quote ? "</blockquote>" : null;
            echo "</div>";
        } else {
            $this->reportDataError($block);
        }

    }

    private function imageBlockFormatter($block)
    {
        $fluid = $this->fluidProcessor($block);
        $filename = $this->sanitizeValue($block, 'data');
        is_string($filename) ? $filename = array($filename) : null;
        $headline = $this->sanitizeValue($block, 'headline');
        $caption = $this->sanitizeValue($block, 'caption');
        $cite = $this->sanitizeValue($block, 'cite');
        $lightbox = $this->sanitizeValue($block, 'lightbox');
        $quote = !empty($block['quote']) ? true : null;
        $path = $this->projPath;
        $maintainSize = !empty($block['maintainSize']) ? 'maintain_size' : null;

        if (!empty($filename)) {

            echo "<figure class='media_block article_images $fluid[0]'>";
            echo !empty($quote) ? "<blockquote class='blockquote quote_container text-body-secondary p-5 rounded-5'>" : null;
            echo !empty($headline) ? "<div class='row'><div class='$fluid[1]'><h6 class='media_headline'>$headline</h6></div></div>" : null;
            echo "<div class='row g-3 gy-3'>";
            foreach ($filename as $image) {
                if (count($filename) % 2 == 0 && count($filename) <= 4) {
                    echo "<div class='col-6 image_cell'>";
                } elseif (count($filename) == 3 || count($filename) > 4) {
                    echo "<div class='col-6 col-lg-4 image_cell'>";
                } else {
                    echo "<div class='col image_cell'>";
                }
                echo "<img src='{$path}/{$image}' class='article_image rounded-2 $maintainSize ";
                if (isset($lightbox) && is_string($lightbox)) {
                    echo " lightbox-enabled' data-larger-src='$lightbox'>";
                } elseif (isset($lightbox)) {
                    $largerImage = $this->findLargerImage($path, $image);
                    echo " lightbox-enabled' data-larger-src='$largerImage'>";
                } else {
                    echo "'>";
                }
                echo "</div>";
            }
            echo "</div>";
            echo !empty($caption) ? "<footer class='block_footer media_caption $fluid[1]'><figcaption class=''>{$caption}<cite>$cite</cite></figcaption></footer>" : null;
            echo $quote ? "</blockquote>" : null;
            echo "</figure>";
        } else {
            $this->reportDataError($block);
        }

    }

    private function videoBlockFormatter($block)
    {
        $fluid = $this->fluidProcessor($block);
        $filename = $this->sanitizeValue($block, 'data');
        is_string($filename) ? $filename = array($filename) : null;
        $headline = $this->sanitizeValue($block, 'headline');
        $caption = $this->sanitizeValue($block, 'caption');
        $cite = $this->sanitizeValue($block, 'cite');
        $quote = !empty($block['quote']) ? true : null;
        $path = $this->projPath;
        $autoPlay = !empty($block['autoPlay']) ? "muted data-autoplay-on-scroll" : null;
        $controls = !empty($block['controls']) ? "controls" : null;
        $loop = !empty($block['loop']) ? "loop" : null;

        if (!empty($filename)) {
            echo "<figure class='media_block article_videos $fluid[0]'>";
            echo !empty($quote) ? "<blockquote class='blockquote quote_container text-body-secondary p-5 rounded-5'>" : null;
            echo !empty($headline) ? "<div class='row'><div class='$fluid[1]'><h6 class='media_headline'>$headline</h6></div></div>" : null;
            foreach ($filename as $video) {
                $videoCrcId = crc32($video);
                echo "<video preload='auto' class='article_video rounded-2' id='article_video_{$videoCrcId}' $autoPlay $controls $loop>";
                echo "<source src='{$path}/{$video}' type='video/mp4'>";
                echo "Your browser does not support the video tag.";
                echo "</video>";
            }
            echo !empty($caption) ? "<footer class='block_footer media_caption $fluid[1]'><figcaption class=''>{$caption}<cite>$cite</cite></figcaption></footer>" : null;
            echo $quote ? "</blockquote>" : null;
            echo "</figure>";
        } else {
            $this->reportDataError($block);
        }
    }

    private function iframeBlockFormatter($block)
    {
        $fluid = $this->fluidProcessor($block);
        $data = $this->sanitizeValue($block, 'data');
        $headline = $this->sanitizeValue($block, 'headline');
        $caption = $this->sanitizeValue($block, 'caption');
        $cite = $this->sanitizeValue($block, 'cite');
        $embedVideo = $this->sanitizeValue($block, 'embedVideo');

        if (isset($data)) {
            $iFrameCrcId = crc32($data);
            echo "<div class='media_block iframe_container {$fluid[0]}'>";
            echo !empty($headline) ? "<div class='$fluid[1]'><h6 class='media_headline'>{$headline}</h6></div>" : null;
            echo "<div class='iframe_wrapper' id='wrapper_{$iFrameCrcId}'>";

            if ($embedVideo) {
                include __DIR__ . "/../.." . $data;
            } else {
                echo "<iframe class='rounded-2' width='100%' height='100%' src='$data' id='iframe_$iFrameCrcId'></iframe>";
            }

            echo "</div>";
            echo !empty($caption) ? "<footer class='block_footer media_caption $fluid[1]'>{$caption}<cite>$cite</cite></footer>" : null;
            echo "</div>";
        } else {
            $this->reportDataError($block);
        }
    }

    private function renderLeadingImage($section)
    {
        $projPath = $this->projPath;
        $headline = $this->sanitizeValue($section, 'headline');
        $leadingImg = $this->sanitizeValue($section, 'leadingImg');
        $leadingImgFixed = $this->sanitizeValue($section, 'leadingImgFixed');
        $leadingImgBgColor = $this->sanitizeValue($section, 'leadingImgBgColor');
        $leadingImgBgColor = $leadingImgBgColor ? "style='background-color: {$leadingImgBgColor}'" : null;

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
        $subheadList = $this->sanitizeValue($section, 'subheadList');
        $leftCol = $this->sanitizeValue($section, 'leftCol');
        $leftColFix = $this->sanitizeValue($section, 'leftColFix');
        $rightCol = $this->sanitizeValue($section, 'rightCol');

        // section container
        echo "<section class='page_section article_section' id='{$headlineId}'>";
        echo "<div class='container'><div class='row'>";
        // left col --
        echo "<div class='pe-lg-4 section_headline ";
        echo ($leftCol ? "col-lg-$leftCol" : "col-lg-3") . "'>";
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
                echo $i === 0 ? "<li class='fw-bold'> $item</li>" : "<li class=''>$item</li>";
            }
            echo "</ul>";
        }
        echo "</div>";
        // Right Col --
        echo "<div class='" . ($rightCol ? "col-lg-$rightCol" : "col-lg-9") . "'>";
        // Depending on the content type, call the respective method
        foreach ($section['content'] as $index => $block) {
            $type = !empty($block['type']) ? strval($block['type']) : null;
            if (method_exists($this, "{$type}BlockFormatter")) {
                echo "<div class='row'>";
                $this->{"{$type}BlockFormatter"}($block);
                echo "</div>";
            } else {
                echo "<div class='row'>";
                $this->reportMediaTypeError($section, $block, $index);
                echo "</div>";
            }
        }
        echo "</div>";
        // end section container
        echo "</div></div>";
        echo "</section>";
    }


}

?>