<?php
include_once __DIR__ . '/class_static_utility.php';
class ArticleContentRenderer
{
    private $project;
    private $projPath;
    private $colLeftRight = [" col-lg-3 ", " col-lg-9 "];
    private $innerColWidth = [" col-lg-8 offset-lg-2 ", " col-12 "];

    public function __construct($project)
    {
        $this->project = $project;
        $this->projPath = $project->path;
    }

    public function render()
    {
        foreach ($this->project->article as $section) {
            $this->renderLeadImg($section);
            $this->renderSection($section);
        }
    }

    // error //////////////////////////////////////////////////////////////////////

    private static function reportMediaTypeError($sectionName, $block, $index)
    {
        $r = null;
        $type = !empty($block['type']) ? strval($block['type']) : "empty";
        $r .= "<div class='media_block col-12}'><div class='alert alert-dark' role='alert'>";
        $r .= "<h5 class='mb-3'>Media Type Error - Wrong Media Type:</h5>";
        $r .= "<p class='lead'>{$sectionName} -> chunk [$index]</p>";
        $r .= "<p>[\"type\"] => \"$type\" is not a valid value</p><hr>";
        $r .= "<p>" . var_dump($block) . "</p></div></div>";
        return $r;
    }
    private static function reportDataError($sectionName, $block, $index)
    {
        $r = null;
        $mediaType = $block['type'] ?? 'empty';
        $data = $block['data'] ?? null;
        $dataType = gettype($data) ?? null;
        $r .= "<div class='media_block col-12}'><div class='alert alert-secondary' role='alert'>";
        $r .= "<h5 class='mb-3'>Data Type Error - " . ($data ? "Wrong Data Type:" : "Empty Data Field") . "</h5>";
        $r .= "<p class='lead'>{$sectionName} -> chunk [$index]</p>";
        if ($data) {
            $r .= "<p>For the [\"type\"] => \"$mediaType\" -- element [\"data\"] can't be \"$dataType\" type</p><hr>";
            $r .= "<p>" . var_dump($block) . "</p>";
        } else {
            $r .= "<hr>";
            $r .= "<p>[Empty Data]</p>";
        }
        $r .= "</div></div>";
        return $r;
    }

    ////////////////////////////////////////////////////////////////////

    private function fluidProcessor($data)
    {
        $narrow = $this->innerColWidth[0];
        $wide = $this->innerColWidth[1];

        if (empty($data['isFluid'])) {
            $containerGrid = $narrow;
            $innerFixerGrid = $wide;

        } else {
            $containerGrid = $wide;
            $innerFixerGrid = $narrow;

            if (!empty($data['isFluidHeadline'])) {
                $innerFixerGrid = $wide;
            }
        }
        return [$containerGrid, $innerFixerGrid];
    }
    private function renderSection($section)
    {
        // Extract values from the section array
        $headline = UtilityClass::sanitizeValue($section['headline'] ?? null);
        $headlineId = UtilityClass::sanitizeValue($section['headlineId'] ?? null);
        $subhead = UtilityClass::sanitizeValue($section['subhead'] ?? null);
        $subheadCaption = UtilityClass::sanitizeValue($section['subheadCaption'] ?? null);
        $subheadList = UtilityClass::sanitizeValue($section['subheadList'] ?? null);
        $leftCol = UtilityClass::sanitizeValue($section['leftCol'] ?? null);
        $rightCol = UtilityClass::sanitizeValue($section['rightCol'] ?? null);
        $leftColUnfluid = isset($section['leftColUnfluid']) ? $section['leftColUnfluid'] : null;

        // Ensure left and right column values don't exceed 12
        if ($leftCol + $rightCol > 12) {
            $leftCol = $rightCol = 12;
        }

        // Determine column classes based on left and right columns
        $leftColClass = $leftCol ? "col-lg-$leftCol" : $this->colLeftRight[0];
        $rightColClass = $rightCol ? "col-lg-$rightCol" : $this->colLeftRight[1];

        // Output section container and row
        echo "<section class='page_section article_section' id='{$headlineId}'>";
        echo "<div class='container'><div class='row gx-5'>";

        // Output left column
        echo "<div class='section_headline $leftColClass'>";
        if ($leftColUnfluid) {
            echo "<div class='row'><div class='{$this->innerColWidth[0]}'>";
        }


        echo "<h2 id='{$headlineId}' class='mb-3 text-black'>{$headline}</h2>";
        if (isset($subhead)) {
            echo "<h6 class='text-secondary'>$subhead</h6>";
        }
        if (isset($subheadCaption)) {
            echo "<p class='text-body-tertiary'>$subheadCaption</p>";
        }
        if (isset($subheadList)) {
            echo "<ul class='subhead_list mt-4'>";
            foreach ($subheadList as $i => $item) {
                echo "<li class='mt-3' id='$headlineId-list-$i'>$item</li>";
            }
            echo "</ul>";
        }

        if ($leftColUnfluid) {
            echo "</div></div>";
        }
        echo "</div>";

        // Output right column
        echo "<div class='$rightColClass sec_content_col'>";
        foreach ($section['content'] as $index => $block) {
            $type = !empty($block['type']) ? strval($block['type']) : null;
            if (empty($type) || $type == "1" || $type == "p") {
                $type = "paragraph";
            }

            echo "<div class='row sec_content_row'>";
            if (method_exists($this, "{$type}BlockFormatter")) {
                $this->{"{$type}BlockFormatter"}($section['headline'], $block, $index);
            } else {
                echo $this->reportMediaTypeError($section['headline'], $block, $index);
            }
            echo "</div>";
        }
        echo "</div>";

        // Close section container and row
        echo "</div></div>";
        echo "</section>";
    }

    // block content writer ////////////////////////////////////////////////
    private static function writeHeadline($headline, $isFluid, $htag = "h4")
    {
        $r = null;
        if (!empty($headline)) {
            $r .= "<div class='row'>";
            $r .= "<div class='text-body $isFluid[1]'>";
            $r .= "<$htag class='block_headline'>$headline</$htag>";
            $r .= "</div>";
            $r .= "</div>";
        }
        return $r;
    }

    private static function writeCaption($caption, $isFluid, $cite)
    {
        $r = null;
        if (!empty($caption) || !empty($cite)) {
            $r .= "<div class='block_caption text-body-secondary $isFluid[1]'>";
            $r .= "<p class='markdown'>$caption<cite>$cite</cite></p>";
            $r .= "</div>";
        }
        return $r;
    }

    private static function writefigCaption($figCaption, $index)
    {
        $r = null;
        if (!empty($figCaption[$index])) {
            $r .= "<figcaption class='figure-caption mt-2'>";
            $r .= "<p class='markdown'>{$figCaption[$index]}</p>";
            $r .= "</figcaption>";
        }
        return $r;
    }

    private function renderLeadImg($section)
    {
        $projPath = $this->projPath;
        $headline = UtilityClass::sanitizeValue($section['headline'] ?? null);
        $leadImg = UtilityClass::sanitizeValue($section['leadImg'] ?? null);
        $leadImgParallax = empty($section['leadImgParallax']) ? null : 'parallax';
        $leadImgBgColor = UtilityClass::sanitizeValue($section['leadImgBgColor'] ?? null);
        $leadImgBgColor = $leadImgBgColor ? $leadImgBgColor : null;
        $leadImgFixed = UtilityClass::sanitizeValue($section['leadImgFixed'] ?? null);
        $leadImgHeight = UtilityClass::sanitizeValue($section['leadImgHeight'] ?? null);

        if (!empty($leadImgFixed)) {
            echo "<div class='article_section_leading bg_attach none_select' style='background-color:{$leadImgBgColor}; height:{$leadImgHeight}'>";
            echo "<div class='bg_attached $leadImgParallax' style='background-image: url({$projPath}/{$leadImgFixed})'>";
            echo "</div></div>";
        } elseif (!empty($leadImg)) {
            echo "<div class='article_section_leading none_select' style='background-color:$leadImgBgColor; height:{$leadImgHeight}'><div class='image_wrapper none_select'>";
            echo "<img class='leading_image none_select $leadImgParallax' src='{$projPath}/{$leadImg}' alt='section: {$headline} - leading image'>";
            echo "</div></div>";
        }
    }


    ////////////////////////////////////////////////////////////////////
    private function paragraphBlockFormatter($sectionName, $block, $index)
    {
        $isFluid = $this->fluidProcessor($block);
        $mainData = UtilityClass::sanitizeValue($block['data'] ?? null);
        is_string($mainData) ? $mainData = array($mainData) : null;

        $headline = UtilityClass::sanitizeValue($block['headline'] ?? null);
        $caption = UtilityClass::sanitizeValue($block['caption'] ?? null);
        $cite = UtilityClass::sanitizeValue($block['cite'] ?? null);
        $isQuote = !empty($block['isQuote']);
        $leadCount = !empty($block['leadCount']) && !is_integer($block['leadCount']) ? 1 : ($block['leadCount'] ?? null);

        if (is_array($mainData)) {
            echo "<div class='media_block article_paragraphs {$isFluid[0]}'>";
            echo $isQuote ? "<blockquote class='blockquote quote_container text-body-secondary p-5 rounded-4'>" : null;

            echo $this->writeHeadline($headline, $isFluid, ($isQuote ? 'h5' : 'h4'));

            foreach ($mainData as $index => $paragraph) {
                if (is_array($paragraph)) {
                    echo UtilityClass::textWithNestingList($paragraph);
                } else {
                    echo "<p class='article_paragraph markdown" . ($index < $leadCount ? " lead" : null) . "'>$paragraph</p>";
                }
            }

            echo $this->writeCaption($caption, $isFluid, $cite);

            echo $isQuote ? "</blockquote>" : null;
            echo "</div>";
        } else {
            echo $this->reportDataError($sectionName, $block, $index);
        }

    }

    private function listBlockFormatter($sectionName, $block, $index)
    {
        $isFluid = $this->fluidProcessor($block);
        $mainData = UtilityClass::sanitizeValue($block['data'] ?? null);
        is_string($mainData) ? $mainData = array($mainData) : null;

        $headline = UtilityClass::sanitizeValue($block['headline'] ?? null);
        $caption = UtilityClass::sanitizeValue($block['caption'] ?? null);
        $cite = UtilityClass::sanitizeValue($block['cite'] ?? null);
        $isQuote = !empty($block['isQuote']);
        $leadCount = !empty($block['leadCount']) && !is_integer($block['leadCount']) ? 1 : ($block['leadCount'] ?? null);
        $isListGroup = !empty($block['isListGroup']) ? "list-group" : "regular";

        if (is_array($mainData)) {
            echo "<div class='media_block article_lists {$isFluid[0]}'>";
            echo $isQuote ? "<blockquote class='blockquote quote_container text-body-secondary p-5 rounded-4'>" : null;

            echo $this->writeHeadline($headline, $isFluid, ($isQuote ? 'h5' : 'h4'));
            echo "<ul class='article_list rounded-2 $isListGroup'>";
            foreach ($mainData as $index => $listChild) {
                if (is_array($listChild)) {
                    echo UtilityClass::textWithNestingList($listChild);
                } else {
                    echo "<li class='$isListGroup-item $isListGroup-item-light artile_list_item markdown " . ($index < $leadCount ? "lead " : null) . "'>$listChild</li>";
                }
            }
            echo "</ul>";

            echo $this->writeCaption($caption, $isFluid, $cite);

            echo $isQuote ? "</blockquote>" : null;
            echo "</div>";
        } else {
            echo $this->reportDataError($sectionName, $block, $index);
        }
    }

    private function imageBlockFormatter($sectionName, $block, $index)
    {
        $projPath = $this->projPath;
        $isFluid = $this->fluidProcessor($block);
        $mainData = UtilityClass::sanitizeValue($block['data'] ?? null);
        is_string($mainData) ? $mainData = array($mainData) : null;
        $figCaption = UtilityClass::sanitizeValue($block['figCaption'] ?? null);
        is_string($figCaption) ? $figCaption = array($figCaption) : null;

        $headline = UtilityClass::sanitizeValue($block['headline'] ?? null);
        $introduction = UtilityClass::sanitizeValue($block['introduction'] ?? null);
        $caption = UtilityClass::sanitizeValue($block['caption'] ?? null);
        $cite = UtilityClass::sanitizeValue($block['cite'] ?? null);
        $isLightbox = $block['isLightbox'] ?? null;
        $isQuote = !empty($block['isQuote']);
        $isMaintainSize = !empty($block['isMaintainSize']) ? 'maintain_size' : null;

        if (!empty($mainData)) {

            echo "<div class='media_block article_images $isFluid[0]'>";
            echo $isQuote ? "<blockquote class='blockquote quote_container text-body-secondary rounded-4'>" : null;
            echo $this->writeHeadline($headline, $isFluid, 'h5');
            if (is_string($introduction)) {
                echo "<p class='image_block_intro markdown'>$introduction</p>";
            } elseif (is_array($introduction)) {
                foreach ($introduction as $paragrah) {
                    echo "<p class='image_block_intro markdown'>";
                    echo UtilityClass::textWithNestingList($paragrah);
                    echo "</p>";
                }
            }
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
                echo "<div class='media_size_fixer'>";
                echo "<img src='{$projPath}/{$image}' alt='" . ($figCaption[$index] ?? "An article image") . ": ";
                echo ($headline ? "$headline - " : "$sectionName - ") . ($caption ? "$caption " : "showcase ") . "image";
                echo "' class='article_image rounded-2 $isMaintainSize ";
                if (!empty($isLightbox)) {
                    if (preg_match("/^[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/", $isLightbox)) {
                        echo " lightbox-enabled' data-larger-src='$isLightbox'>";
                    } else {
                        $largerImage = UtilityClass::findLargerImage($projPath, $image);
                        $largerImage = $largerImage ?? "original";
                        echo " lightbox-enabled' data-larger-src='" . $largerImage . "'>";
                    }
                } else {
                    echo "'>";
                }
                echo "</div>";

                echo $this->writefigCaption($figCaption, $index);
                echo "</figure>";
                echo "</div>";
            }
            echo "</div>";
            echo $this->writeCaption($caption, $isFluid, $cite);
            echo $isQuote ? "</blockquote>" : null;
            echo "</div>";
        } else {
            echo $this->reportDataError($sectionName, $block, $index);
        }

    }
    private function carouselBlockFormatter($sectionName, $block, $index)
    {
        $projPath = $this->projPath;
        $isFluid = $this->fluidProcessor($block);
        $mainData = UtilityClass::sanitizeValue($block['data'] ?? null);
        is_string($mainData) ? $mainData = array($mainData) : null;
        $imgHeadlines = UtilityClass::sanitizeValue($block['imgHeadlines'] ?? null);
        is_string($imgHeadlines) ? $imgHeadlines = array($imgHeadlines) : null;
        $imgCaptions = UtilityClass::sanitizeValue($block['imgCaptions'] ?? null);
        is_string($imgCaptions) ? $imgCaptions = array($imgCaptions) : null;

        $headline = UtilityClass::sanitizeValue($block['headline'] ?? null);
        $caption = UtilityClass::sanitizeValue($block['caption'] ?? null);
        $cite = UtilityClass::sanitizeValue($block['cite'] ?? null);
        $isLightbox = $block['isLightbox'] ?? null;
        $isQuote = !empty($block['isQuote']);
        $isIndicators = !empty($block['isIndicators']);
        $isControls = !empty($block['isControls']);
        $isAutoPlay = !empty($block['isAutoPlay']) ? "data-bs-ride='carousel' data-bs-interval='10000'" : null;

        if (!empty($mainData)) {

            $carouselCrcId = crc32($mainData[0]);

            echo "<div class='media_block article_carousel $isFluid[0]'>";
            echo $isQuote ? "<blockquote class='blockquote quote_container text-body-secondary p-5 rounded-4'>" : null;
            echo $this->writeHeadline($headline, $isFluid, 'h5');
            ///
            echo "<div id='carousel_$carouselCrcId' class='carousel slide' $isAutoPlay>";
            if ($isIndicators) {
                echo "<div class='carousel-indicators'>";
                foreach ($mainData as $index => $image) {
                    $count = $index + 1;
                    echo "<button type='button' data-bs-target='#carousel_$carouselCrcId' data-bs-slide-to='$index' ";
                    echo $index == 0 ? " class='active' aria-current='true' " : null;
                    echo " aria-label='Slide $count'></button>";
                }
                echo "</div>";
            }
            echo "<div class='carousel-inner rounded-2'>";
            foreach ($mainData as $index => $image) {
                echo "<div class='carousel-item" . ($index == 0 ? " active" : null) . "'>";

                echo "<img src='{$projPath}/{$image}' alt='" . ($imgCaptions[$index] ?? "A carousel image") . ": ";
                echo ($headline ? "$headline - " : "$sectionName - ") . ($imgCaptions[$index] ? null : ($caption ? "$caption " : "showcase ")) . "image";
                echo "' class='carousel_image d-block w-100 ";
                if (isset($isLightbox) && is_string($isLightbox)) {
                    echo " lightbox-enabled' data-larger-src='$isLightbox'>";
                } elseif (isset($isLightbox)) {
                    $largerImage = UtilityClass::findLargerImage($projPath, $image);
                    echo " lightbox-enabled' data-larger-src='$largerImage'>";
                } else {
                    echo " '>";
                }
                if (!empty($imgHeadlines[$index]) || !empty($imgCaptions[$index])) {
                    echo "<div class='carousel-caption d-none d-md-block'>";
                    echo !empty($imgHeadlines[$index]) ? "<h6 class='carousel_image_headline'>$imgHeadlines[$index]</h6>" : null;
                    echo !empty($imgCaptions[$index]) ? "<p class='carousel_image_caption'>$imgCaptions[$index]</p>" : null;
                    echo "</div>";
                }
                echo "</div>";
            }
            echo "</div>";
            if ($isControls) {
                echo <<<HTML
                <button class='carousel-control-prev' type='button' data-bs-target='#carousel_$carouselCrcId' data-bs-slide='prev'>
                    <span class='carousel-control-prev-icon' aria-hidden='true'></span>
                    <span class='visually-hidden'>Previous</span>
                </button>
                <button class='carousel-control-next' type='button' data-bs-target='#carousel_$carouselCrcId' data-bs-slide='next'>
                    <span class='carousel-control-next-icon' aria-hidden='true'></span>
                    <span class='visually-hidden'>Next</span>
                </button>
                HTML;
            }
            echo "</div>";
            ///
            echo $this->writeCaption($caption, $isFluid, $cite);
            echo $isQuote ? "</blockquote>" : null;
            echo "</div>";
        } else {
            echo $this->reportDataError($sectionName, $block, $index);
        }

    }

    private function videoBlockFormatter($sectionName, $block, $index)
    {
        $projPath = $this->projPath;
        $isFluid = $this->fluidProcessor($block);
        $mainData = UtilityClass::sanitizeValue($block['data'] ?? null);
        is_string($mainData) ? $mainData = array($mainData) : null;
        $figCaption = UtilityClass::sanitizeValue($block['figCaption'] ?? null);
        is_string($figCaption) ? $figCaption = array($figCaption) : null;

        $headline = UtilityClass::sanitizeValue($block['headline'] ?? null);
        $caption = UtilityClass::sanitizeValue($block['caption'] ?? null);
        $cite = UtilityClass::sanitizeValue($block['cite'] ?? null);
        $isQuote = !empty($block['isQuote']);
        $isAutoPlay = !empty($block['isAutoPlay']) ? "muted data-autoplay-on-scroll" : null;
        $isControls = !empty($block['isControls']) ? "controls" : null;
        $isLoop = !empty($block['isLoop']) ? "loop" : null;

        if (!empty($mainData)) {
            echo "<div class='media_block article_videos $isFluid[0]'>";
            echo !empty($isQuote) ? "<blockquote class='blockquote quote_container text-body-secondary p-5 rounded-4'>" : null;
            echo $this->writeHeadline($headline, $isFluid, ($isQuote ? 'h5' : 'h4'));
            echo "<div class='row g-3'>";
            foreach ($mainData as $index => $video) {
                echo "<div class='col-12 video_wrapper'>";
                $videoCrcId = crc32($video);
                echo "<figure class='figure'>";
                echo "<div class='media_size_fixer'><video preload='auto' class='article_video rounded-2' id='article_video_{$videoCrcId}' $isAutoPlay $isControls $isLoop>";
                echo "<source src='{$projPath}/{$video}' type='video/mp4'> Please Update Your Browser.</video></div>";
                echo $this->writefigCaption($figCaption, $index);
                echo "</figure>";
                echo "</div>";
            }
            echo "</div>";
            echo $this->writeCaption($caption, $isFluid, $cite);
            echo $isQuote ? "</blockquote>" : null;
            echo "</div>";
        } else {
            echo $this->reportDataError($sectionName, $block, $index);
        }
    }

    private function iframeBlockFormatter($sectionName, $block, $index)
    {
        $dirPath = $this->projPath;
        $isFluid = $this->fluidProcessor($block);
        $mainData = UtilityClass::sanitizeValue($block['data'] ?? null);
        $headline = UtilityClass::sanitizeValue($block['headline'] ?? null);
        $caption = UtilityClass::sanitizeValue($block['caption'] ?? null);
        $cite = UtilityClass::sanitizeValue($block['cite'] ?? null);
        $isOnDemand = empty($block['isOnDemand']) ? null : true;
        $isEmbedVideo = UtilityClass::sanitizeValue($block['isEmbedVideo'] ?? null);
        $iframeHeight = UtilityClass::sanitizeValue($block['iframeHeight'] ?? null);

        if (isset($mainData)) {
            $iFrameCrcId = crc32($mainData);
            echo "<div class='media_block iframe_container {$isFluid[0]}'>";
            echo $this->writeHeadline($headline, $isFluid, 'h4');
            echo "<div class='iframe_wrapper rounded-2 overflow-hidden' id='wrapper_{$iFrameCrcId}' ";
            echo $isOnDemand ? "data-target-height='$iframeHeight'>" : "style='height:$iframeHeight'>";

            if ($isEmbedVideo) {
                @include $dirPath . '/' . $mainData;
            } elseif ($isOnDemand) {
                echo "<button class='load_iframe btn btn-lg btn-danger px-4'>Load Content</button>";
                echo "<iframe class='on_demand' width='100%' height='100%' data-iframe-src='$mainData' src='' id='iframe_$iFrameCrcId'></iframe>";
            } else {
                echo "<iframe class='' width='100%' height='100%' src='$mainData' id='iframe_$iFrameCrcId'></iframe>";
            }

            echo "</div>";
            echo $this->writeCaption($caption, $isFluid, $cite);
            echo "</div>";
        } else {
            echo $this->reportDataError($sectionName, $block, $index);
        }
    }
    private function htmlBlockFormatter($sectionName, $block, $index)
    {
        $dirPath = $this->projPath;
        $isFluid = $this->fluidProcessor($block);
        $mainData = UtilityClass::sanitizeValue($block['data'] ?? null);
        $headline = UtilityClass::sanitizeValue($block['headline'] ?? null);
        $caption = UtilityClass::sanitizeValue($block['caption'] ?? null);
        $cite = UtilityClass::sanitizeValue($block['cite'] ?? null);


        if (isset($mainData)) {
            $htmlBlockCrcId = crc32($mainData);
            echo "<div class='media_block html_container {$isFluid[0]}'>";
            echo $this->writeHeadline($headline, $isFluid, 'h4');

            echo "<div class='html_wrapper rounded-2' id='wrapper_{$htmlBlockCrcId}'>";
            @include $dirPath . '/' . $mainData;
            echo "</div>";

            echo $this->writeCaption($caption, $isFluid, $cite);
            echo "</div>";
        } else {
            echo $this->reportDataError($sectionName, $block, $index);
        }
    }



}

?>