<?php
class ArticleContentRenderer
{

    private $project;
    private $projPath;
    public $colFixer = [" col-lg-8 offset-lg-2 ", " col-12 "];

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
        $gutter = $this->colFixer[0];
        $full = $this->colFixer[1];

        if (empty($data['isFluid'])) {
            $containerGrid = $gutter;
            $innerFixerGrid = $full;

        } else {
            $containerGrid = $full;
            $innerFixerGrid = $gutter;

            if (!empty($data['isFluidHeadline'])) {
                $innerFixerGrid = $full;
            }
        }
        return [$containerGrid, $innerFixerGrid];
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
        echo "<div class='media_block {$this->colFixer[0]}'><div class='alert alert-dark' role='alert'>";
        echo "<h5 class='mb-4'>Media Type Error - Wrong Media Type:</h5>";
        echo "<p class='lead'>{$section['headline']} -> content[$index]</p>";
        echo "<p>type->\"$type\" is not a valid type</p><hr>";
        echo "<p>" . var_dump($block) . "</p></div></div>";
    }
    private function reportDataError($section, $block, $index)
    {
        $mediaType = $block['type'] ?? 'empty';
        $data = $block['data'] ?? null;
        $dataType = gettype($data) ?? null;
        echo "<div class='media_block {$this->colFixer[0]}'><div class='alert alert-secondary' role='alert'>";
        echo "<h5 class='mb-3'>Data Type Error - " . ($data ? "Wrong Data Type:" : "Empty Data Field") . "</h5>";
        echo "<p class='lead'>{$section['headline']} -> content[$index]</p>";
        if ($data) {
            echo "<p'>for type->\"$mediaType\": \"data\" can't be \"$dataType\"</p><hr>";
            echo var_dump($block);
        } else {
            echo "<hr>";
            echo "[Empty Data]";
        }
        echo "</div></div>";
    }

    // formater ////////////////////////////////////////////////

    private function writeHeadline($headline, $isFluid, $htag = "h6")
    {
        $isFluid ?? $isFluid = [$this->colFixer[0], $this->colFixer[1]];
        if (!empty($headline)) {
            echo "<div class='row'><div class='$isFluid[1]'>";
            echo "<$htag class='block_headline'>$headline</$htag>";
            echo "</div></div>";
        }
    }
    private function writeCaption($caption, $isFluid, $cite)
    {
        if (!empty($caption)) {
            echo "<div class='block_caption $isFluid[1]'>";
            echo "<div>$caption<cite>$cite</cite></div>";
            echo "</div>";
        }
    }

    private function writefigCaption($fitureCaption, $index)
    {
    }

    private function paragraphBlockFormatter($section, $block, $index)
    {
        $isFluid = $this->fluidProcessor($block);
        $mainData = $this->sanitizeValue($block, 'data');
        is_string($mainData) ? $mainData = array($mainData) : null;

        $headline = $this->sanitizeValue($block, 'headline');
        $caption = $this->sanitizeValue($block, 'caption');
        $cite = $this->sanitizeValue($block, 'cite');
        $isQuote = !empty($block['isQuote']) ? true : null;
        $leadCount = !empty($block['leadCount']) && !is_integer($block['leadCount']) ? 1 : ($block['leadCount'] ?? null);

        if (is_array($mainData)) {
            echo "<div class='media_block article_paragraph {$isFluid[0]}'>";
            echo $isQuote ? "<blockquote class='blockquote quote_container text-body-secondary p-5 rounded-5'>" : null;

            $this->writeHeadline($headline, $isFluid, 'h5');

            foreach ($mainData as $index => $paragraph) {
                echo "<p class='article_paragraph " . ($index < $leadCount ? "lead" : null) . "'>$paragraph</p>";
            }

            $this->writeCaption($caption, $isFluid, $cite);

            echo $isQuote ? "</blockquote>" : null;
            echo "</div>";
        } else {
            $this->reportDataError($section, $block, $index);
        }

    }

    private function imageBlockFormatter($section, $block, $index)
    {
        $projPath = $this->projPath;
        $isFluid = $this->fluidProcessor($block);
        $mainData = $this->sanitizeValue($block, 'data');
        is_string($mainData) ? $mainData = array($mainData) : null;
        $figCaption = $this->sanitizeValue($block, 'figCaption');
        is_string($figCaption) ? $figCaption = array($figCaption) : null;

        $headline = $this->sanitizeValue($block, 'headline');
        $caption = $this->sanitizeValue($block, 'caption');
        $cite = $this->sanitizeValue($block, 'cite');
        $isLightbox = $this->sanitizeValue($block, 'isLightbox');
        $isQuote = !empty($block['isQuote']) ? true : null;
        $isMaintainSize = !empty($block['isMaintainSize']) ? 'maintain_size' : null;

        if (!empty($mainData)) {

            echo "<div class='media_block article_images $isFluid[0]'>";
            echo $isQuote ? "<blockquote class='blockquote quote_container text-body-secondary p-5 rounded-5'>" : null;
            $this->writeHeadline($headline, $isFluid);
            echo "<div class='row g-3'>";
            foreach ($mainData as $index => $image) {
                if (count($mainData) % 2 == 0 && count($mainData) <= 4) {
                    echo "<div class='image_wrapper col-6'>";
                } elseif (count($mainData) == 3 || count($mainData) > 4) {
                    echo "<div class='image_wrapper col-6 col-lg-4 '>";
                } else {
                    echo "<div class='image_wrapper col-12'>";
                }
                echo "<figure class='figure $isMaintainSize'>";
                echo "<img src='{$projPath}/{$image}' alt='article image: ";
                echo ($headline ?? "showcase") . " - " . ($caption ?? "project") . ($figCaption ?? "image");
                echo "' class='article_image rounded  ";
                if (isset($isLightbox) && is_string($isLightbox)) {
                    echo " isLightbox-enabled' data-larger-src='$isLightbox'>";
                } elseif (isset($isLightbox)) {
                    $largerImage = $this->findLargerImage($projPath, $image);
                    echo " lightbox-enabled' data-larger-src='$largerImage'>";
                } else {
                    echo "'>";
                }
                echo !empty($figCaption[$index]) ? "<figcaption class='figure-caption mt-1'>{$figCaption[$index]}</figcaption>" : null;
                echo "</figure>";
                echo "</div>";
            }
            echo "</div>";
            $this->writeCaption($caption, $isFluid, $cite);
            echo $isQuote ? "</blockquote>" : null;
            echo "</div>";
        } else {
            $this->reportDataError($section, $block, $index);
        }

    }

    private function videoBlockFormatter($section, $block, $index)
    {
        $projPath = $this->projPath;
        $isFluid = $this->fluidProcessor($block);
        $mainData = $this->sanitizeValue($block, 'data');
        is_string($mainData) ? $mainData = array($mainData) : null;
        $figCaption = $this->sanitizeValue($block, 'figCaption');
        is_string($figCaption) ? $figCaption = array($figCaption) : null;

        $headline = $this->sanitizeValue($block, 'headline');
        $caption = $this->sanitizeValue($block, 'caption');
        $cite = $this->sanitizeValue($block, 'cite');
        $isQuote = !empty($block['isQuote']) ? true : null;
        $isAutoPlay = !empty($block['isAutoPlay']) ? "muted data-autoplay-on-scroll" : null;
        $isControls = !empty($block['isControls']) ? "controls" : null;
        $isLoop = !empty($block['isLoop']) ? "loop" : null;

        if (!empty($mainData)) {
            echo "<div class='media_block article_videos $isFluid[0]'>";
            echo !empty($isQuote) ? "<blockquote class='blockquote quote_container text-body-secondary p-5 rounded-5'>" : null;
            $this->writeHeadline($headline, $isFluid);
            echo "<div class='row g-3'>";
            foreach ($mainData as $index => $video) {
                echo "<div class='col-12 video_wrapper'>";
                $videoCrcId = crc32($video);
                echo "<figure class='figure'><video preload='auto' class='article_video rounded' id='article_video_{$videoCrcId}' $isAutoPlay $isControls $isLoop>";
                echo "<source src='{$projPath}/{$video}' type='video/mp4'> Please Update Your Browser.</video>";
                echo !empty($figCaption[$index]) ? "<figcaption class='figure-caption mt-1'>{$figCaption[$index]}</figcaption>" : null;
                echo "</figure>";
                echo "</div>";
            }
            echo "</div>";
            $this->writeCaption($caption, $isFluid, $cite);
            echo $isQuote ? "</blockquote>" : null;
            echo "</div>";
        } else {
            $this->reportDataError($section, $block, $index);
        }
    }

    private function iframeBlockFormatter($section, $block, $index)
    {
        $dirPath = __DIR__ . '/../..' . $this->projPath;
        $isFluid = $this->fluidProcessor($block);
        $mainData = $this->sanitizeValue($block, 'data');
        $headline = $this->sanitizeValue($block, 'headline');
        $caption = $this->sanitizeValue($block, 'caption');
        $cite = $this->sanitizeValue($block, 'cite');
        $isEmbedVideo = $this->sanitizeValue($block, 'isEmbedVideo');

        if (isset($mainData)) {
            $iFrameCrcId = crc32($mainData);
            echo "<div class='media_block iframe_container {$isFluid[0]}'>";
            $this->writeHeadline($headline, $isFluid, 'h6');
            echo "<div class='iframe_wrapper rounded' id='wrapper_{$iFrameCrcId}'>";

            if ($isEmbedVideo) {
                @include $dirPath . '/' . $mainData;
            } else {
                echo "<iframe class='' width='100%' height='100%' src='$mainData' id='iframe_$iFrameCrcId'></iframe>";
            }

            echo "</div>";
            $this->writeCaption($caption, $isFluid, $cite);
            echo "</div>";
        } else {
            $this->reportDataError($section, $block, $index);
        }
    }
    private function htmlBlockFormatter($section, $block, $index)
    {
        $dirPath = __DIR__ . '/../..' . $this->projPath;
        $isFluid = $this->fluidProcessor($block);
        $mainData = $this->sanitizeValue($block, 'data');
        $headline = $this->sanitizeValue($block, 'headline');
        $caption = $this->sanitizeValue($block, 'caption');
        $cite = $this->sanitizeValue($block, 'cite');


        if (isset($mainData)) {
            $htmlBlockCrcId = crc32($mainData);
            echo "<div class='media_block html_container {$isFluid[0]}'>";
            $this->writeHeadline($headline, $isFluid);

            echo "<div class='html_wrapper rounded' id='wrapper_{$htmlBlockCrcId}'>";
            @include $dirPath . '/' . $mainData;
            echo "</div>";

            $this->writeCaption($caption, $isFluid, $cite);
            echo "</div>";
        } else {
            $this->reportDataError($section, $block, $index);
        }
    }

    // content renderer //////////////////////////////////////////////////////////////////////
    private function renderLeadingImage($section)
    {
        $projPath = $this->projPath;
        $headline = $this->sanitizeValue($section, 'headline');
        $leadImg = $this->sanitizeValue($section, 'leadImg');
        $leadImgBgColor = $this->sanitizeValue($section, 'leadImgBgColor');
        $leadImgBgColor = $leadImgBgColor ? "style='background-color: {$leadImgBgColor}'" : null;
        $leadImgFixed = $this->sanitizeValue($section, 'leadImgFixed');
        $leadImgFixedHeight = $this->sanitizeValue($section, 'leadImgFixedHeight');

        if (!empty($leadImgFixed)) {
            echo "<div style='background-image: url({$projPath}/{$leadImgFixed}); height: $leadImgFixedHeight'";
            echo " class='article_section_leading bg_attach'></div>";
        } elseif (!empty($leadImg)) {
            echo "<div class='article_section_leading none_select' $leadImgBgColor>";
            echo "<div class='image_wrapper none_select'><img class='leading_image none_select' src='{$projPath}/{$leadImg}' alt='{$headline} section leading image'>";
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
        $rightCol = $this->sanitizeValue($section, 'rightCol');
        $leftColUnfluid = isset($section['leftColUnfluid']) ? $section['leftColUnfluid'] : null;
        $leftCol + $rightCol > 12 ? $leftCol = $rightCol = 12 : null;
        // $leftCol > 0 && $leftCol < 12 ? $leftCol .= " pe-lg-4" : null;
        $leftCol = $leftCol ? "col-lg-$leftCol" : "col-lg-3";
        $rightCol = $rightCol ? "col-lg-$rightCol" : "col-lg-9";

        // section container
        echo "<section class='page_section article_section' id='{$headlineId}'>";
        echo "<div class='container'><div class='row gx-5'>";

        // left col --
        echo "<div class='section_headline $leftCol'>";
        echo $leftColUnfluid ? "<div class='row'><div class='{$this->colFixer[0]}'>" : null;

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

        echo $leftColUnfluid ? "</div></div>" : null;
        echo "</div>";

        // Right Col --
        echo "<div class='$rightCol'>";
        // Depending on the content type, call the respective method
        foreach ($section['content'] as $index => $block) {
            $type = !empty($block['type']) ? strval($block['type']) : null;
            if (method_exists($this, "{$type}BlockFormatter")) {
                echo "<div class='row'>";
                $this->{"{$type}BlockFormatter"}($section, $block, $index);
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