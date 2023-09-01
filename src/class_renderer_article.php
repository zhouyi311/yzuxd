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
            echo $this->renderLeadImg($section);
            echo $this->renderSection($section);
        }
    }

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

    // block content writer ////////////////////////////////////////////////
    private static function writeHeadline($headline, $isFluid, $htag = "h4")
    {
        $html = null;
        if (!empty($headline)) {
            $html .= "<div class='row'>";
            $html .= "<div class='text-body $isFluid[1]'>";
            $html .= "<$htag class='block_headline'>$headline</$htag>";
            $html .= "</div>";
            $html .= "</div>";
        }
        return $html;
    }

    private static function writeCaption($caption, $isFluid, $cite)
    {
        $html = null;
        if (!empty($caption) || !empty($cite)) {
            $html .= "<div class='block_caption text-body-secondary $isFluid[1]'>";
            $html .= "<p class='markdown'>$caption<cite>$cite</cite></p>";
            $html .= "</div>";
        }
        return $html;
    }

    private static function writefigCaption($figCaption, $index)
    {
        $html = null;
        if (!empty($figCaption[$index])) {
            $html .= "<figcaption class='figure-caption mt-2'>";
            $html .= "<p class='markdown'>{$figCaption[$index]}</p>";
            $html .= "</figcaption>";
        }
        return $html;
    }

    ////////////////////////////////////////////////////////////////////
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

        $html = '';

        if (!empty($leadImgFixed)) {
            $html .= "<div class='article_section_leading bg_attach none_select' style='background-color:{$leadImgBgColor}; height:{$leadImgHeight}'>";
            $html .= "<div class='bg_attached $leadImgParallax' style='background-image: url({$projPath}/{$leadImgFixed})'>";
            $html .= "</div></div>";
        } elseif (!empty($leadImg)) {
            $html .= "<div class='article_section_leading none_select' style='background-color:$leadImgBgColor; height:{$leadImgHeight}'><div class='image_wrapper none_select'>";
            $html .= "<img class='leading_image none_select $leadImgParallax' src='{$projPath}/{$leadImg}' alt='section: {$headline} - leading image'>";
            $html .= "</div></div>";
        }

        return $html;
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
        $isHidden = $section['isHidden'] ?? false;


        if ($isHidden) {
            $html = null;
        } else {
            
            // Ensure left and right column values don't exceed 12
            if ($leftCol + $rightCol > 12) {
                $leftCol = $rightCol = 12;
            }

            // Determine column classes based on left and right columns
            $leftColClass = $leftCol ? "col-lg-$leftCol" : $this->colLeftRight[0];
            $rightColClass = $rightCol ? "col-lg-$rightCol" : $this->colLeftRight[1];

            // Start building the HTML string
            $html = "<section class='page_section article_section' id='{$headlineId}'>";
            $html .= "<div class='container'><div class='row gx-5'>";

            // Left column
            $html .= "<div class='section_headline $leftColClass'>";
            if ($leftColUnfluid) {
                $html .= "<div class='row'><div class='{$this->innerColWidth[0]}'>";
            }

            $html .= "<h2 id='{$headlineId}' class='mb-3 text-black'>{$headline}</h2>";
            if (isset($subhead)) {
                $html .= "<h6 class='text-secondary'>$subhead</h6>";
            }
            if (isset($subheadCaption)) {
                $html .= "<p class='text-body-tertiary'>$subheadCaption</p>";
            }
            if (isset($subheadList)) {
                $html .= "<ul class='subhead_list mt-4'>";
                foreach ($subheadList as $i => $item) {
                    $html .= "<li class='mt-3' id='$headlineId-list-$i'>$item</li>";
                }
                $html .= "</ul>";
            }

            if ($leftColUnfluid) {
                $html .= "</div></div>";
            }
            $html .= "</div>";

            // Right column
            $html .= "<div class='$rightColClass sec_content_col'>";
            foreach ($section['content'] as $index => $block) {
                $type = !empty($block['type']) ? strval($block['type']) : null;
                if (empty($type) || $type == "1" || $type == "p") {
                    $type = "paragraph";
                }

                $html .= "<div class='row sec_content_row'>";
                if (method_exists($this, "{$type}BlockFormatter")) {
                    $html .= $this->{"{$type}BlockFormatter"}($section['headline'], $block, $index);
                } else {
                    $html .= $this->reportMediaTypeError($section['headline'], $block, $index);
                }
                $html .= "</div>";
            }
            $html .= "</div>";

            // Close section container and row
            $html .= "</div></div>";
            $html .= "</section>";

        }

        return $html;
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

        $html = '';

        if (is_array($mainData)) {
            $html .= "<div class='media_block article_paragraphs {$isFluid[0]}'>";
            $html .= $isQuote ? "<blockquote class='blockquote quote_container text-body-secondary p-5 rounded-4'>" : null;

            $html .= $this->writeHeadline($headline, $isFluid, ($isQuote ? 'h5' : 'h4'));

            foreach ($mainData as $index => $paragraph) {
                if (is_array($paragraph)) {
                    $html .= UtilityClass::textWithNestingList($paragraph);
                } else {
                    $html .= "<p class='article_paragraph markdown" . ($index < $leadCount ? " lead" : null) . "'>$paragraph</p>";
                }
            }

            $html .= $this->writeCaption($caption, $isFluid, $cite);

            $html .= $isQuote ? "</blockquote>" : null;
            $html .= "</div>";
        } else {
            $html .= $this->reportDataError($sectionName, $block, $index);
        }

        return $html;
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

        $html = '';

        if (is_array($mainData)) {
            $html .= "<div class='media_block article_lists {$isFluid[0]}'>";
            $html .= $isQuote ? "<blockquote class='blockquote quote_container text-body-secondary p-5 rounded-4'>" : null;

            $html .= $this->writeHeadline($headline, $isFluid, ($isQuote ? 'h5' : 'h4'));
            $html .= "<ul class='article_list rounded-2 $isListGroup'>";
            foreach ($mainData as $index => $listChild) {
                if (is_array($listChild)) {
                    $html .= UtilityClass::textWithNestingList($listChild);
                } else {
                    $html .= "<li class='$isListGroup-item $isListGroup-item-light artile_list_item markdown " . ($index < $leadCount ? "lead " : null) . "'>$listChild</li>";
                }
            }
            $html .= "</ul>";

            $html .= $this->writeCaption($caption, $isFluid, $cite);

            $html .= $isQuote ? "</blockquote>" : null;
            $html .= "</div>";
        } else {
            $html .= $this->reportDataError($sectionName, $block, $index);
        }

        return $html;
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
        $isCompact = !empty($block['isCompact']) ? 'compact_size' : null;

        $html = '';

        if (!empty($mainData)) {
            $html .= "<div class='media_block article_images $isFluid[0]'>";
            $html .= $isQuote ? "<blockquote class='blockquote quote_container text-body-secondary rounded-4'>" : null;
            $html .= $this->writeHeadline($headline, $isFluid, 'h5');
            if (is_string($introduction)) {
                $html .= "<p class='image_block_intro markdown'>$introduction</p>";
            } elseif (is_array($introduction)) {
                foreach ($introduction as $paragrah) {
                    $html .= "<p class='image_block_intro markdown'>";
                    $html .= UtilityClass::textWithNestingList($paragrah);
                    $html .= "</p>";
                }
            }
            $html .= "<div class='row g-3'>";
            foreach ($mainData as $index => $image) {
                if (count($mainData) % 2 == 0 && count($mainData) <= 4) {
                    $html .= "<div class='image_wrapper col-12 col-sm-6'>";
                } elseif (count($mainData) == 3 || count($mainData) > 4) {
                    $html .= "<div class='image_wrapper col-12 col-sm-4 '>";
                } else {
                    $html .= "<div class='image_wrapper col-12'>";
                }
                $html .= "<figure class='figure $isCompact'>";
                $html .= "<div class='media_size_fixer'>";
                $html .= "<img src='{$projPath}/{$image}' alt='" . ($figCaption[$index] ?? "An article image") . ": ";
                $html .= ($headline ? "$headline - " : "$sectionName - ") . ($caption ? "$caption " : "showcase ") . "image";
                $html .= "' class='article_image rounded-2 $isCompact ";
                if (!empty($isLightbox)) {
                    if (preg_match("/^[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/", $isLightbox)) {
                        $html .= " lightbox-enabled' data-larger-src='$isLightbox'>";
                    } else {
                        $largerImage = UtilityClass::findLargerImage($projPath, $image);
                        $largerImage = $largerImage ?? "original";
                        $html .= " lightbox-enabled' data-larger-src='" . $largerImage . "'>";
                    }
                } else {
                    $html .= "'>";
                }
                $html .= "</div>";

                $html .= $this->writefigCaption($figCaption, $index);
                $html .= "</figure>";
                $html .= "</div>";
            }
            $html .= "</div>";
            $html .= $this->writeCaption($caption, $isFluid, $cite);
            $html .= $isQuote ? "</blockquote>" : null;
            $html .= "</div>";
        } else {
            $html .= $this->reportDataError($sectionName, $block, $index);
        }

        return $html;
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

        $html = '';

        if (!empty($mainData)) {
            $carouselCrcId = crc32($mainData[0]);

            $html .= "<div class='media_block article_carousel $isFluid[0]'>";
            $html .= $isQuote ? "<blockquote class='blockquote quote_container text-body-secondary p-5 rounded-4'>" : null;
            $html .= $this->writeHeadline($headline, $isFluid, 'h5');

            $html .= "<div id='carousel_$carouselCrcId' class='carousel slide' $isAutoPlay>";
            if ($isIndicators) {
                $html .= "<div class='carousel-indicators'>";
                foreach ($mainData as $index => $image) {
                    $count = $index + 1;
                    $html .= "<button type='button' data-bs-target='#carousel_$carouselCrcId' data-bs-slide-to='$index' ";
                    $html .= $index == 0 ? " class='active' aria-current='true' " : null;
                    $html .= " aria-label='Slide $count'></button>";
                }
                $html .= "</div>";
            }
            $html .= "<div class='carousel-inner rounded-2'>";
            foreach ($mainData as $index => $image) {
                $html .= "<div class='carousel-item" . ($index == 0 ? " active" : null) . "'>";

                $html .= "<img src='{$projPath}/{$image}' alt='" . ($imgCaptions[$index] ?? "A carousel image") . ": ";
                $html .= ($headline ? "$headline - " : "$sectionName - ") . ($imgCaptions[$index] ? null : ($caption ? "$caption " : "showcase ")) . "image";
                $html .= "' class='carousel_image d-block w-100 ";
                if (isset($isLightbox) && is_string($isLightbox)) {
                    $html .= " lightbox-enabled' data-larger-src='$isLightbox'>";
                } elseif (isset($isLightbox)) {
                    $largerImage = UtilityClass::findLargerImage($projPath, $image);
                    $html .= " lightbox-enabled' data-larger-src='$largerImage'>";
                } else {
                    $html .= " '>";
                }

                if (!empty($imgHeadlines[$index]) || !empty($imgCaptions[$index])) {
                    $html .= "<div class='carousel-caption d-none d-md-block'>";
                    $html .= !empty($imgHeadlines[$index]) ? "<h6 class='carousel_image_headline'>$imgHeadlines[$index]</h6>" : null;
                    $html .= !empty($imgCaptions[$index]) ? "<p class='carousel_image_caption'>$imgCaptions[$index]</p>" : null;
                    $html .= "</div>";
                }
                $html .= "</div>";
            }
            $html .= "</div>";
            if ($isControls) {
                $html .= <<<HTML
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
            $html .= "</div>";

            $html .= $this->writeCaption($caption, $isFluid, $cite);
            $html .= $isQuote ? "</blockquote>" : null;
            $html .= "</div>";
        } else {
            $html .= $this->reportDataError($sectionName, $block, $index);
        }

        return $html;
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

        $html = '';

        if (!empty($mainData)) {
            $html .= "<div class='media_block article_videos $isFluid[0]'>";
            $html .= !empty($isQuote) ? "<blockquote class='blockquote quote_container text-body-secondary p-5 rounded-4'>" : null;
            $html .= $this->writeHeadline($headline, $isFluid, ($isQuote ? 'h5' : 'h4'));
            $html .= "<div class='row g-3'>";
            foreach ($mainData as $index => $video) {
                $html .= "<div class='col-12 video_wrapper'>";
                $videoCrcId = crc32($video);
                $html .= "<figure class='figure'>";
                $html .= "<div class='media_size_fixer'><video preload='auto' class='article_video rounded-2' id='article_video_{$videoCrcId}' $isAutoPlay $isControls $isLoop>";
                $html .= "<source src='{$projPath}/{$video}' type='video/mp4'> Please Update Your Browser.</video></div>";
                $html .= $this->writefigCaption($figCaption, $index);
                $html .= "</figure>";
                $html .= "</div>";
            }
            $html .= "</div>";
            $html .= $this->writeCaption($caption, $isFluid, $cite);
            $html .= $isQuote ? "</blockquote>" : null;
            $html .= "</div>";
        } else {
            $html .= $this->reportDataError($sectionName, $block, $index);
        }

        return $html;
    }

    private function iframeBlockFormatter($sectionName, $block, $index)
    {
        $isFluid = $this->fluidProcessor($block);
        $mainData = UtilityClass::sanitizeValue($block['data'] ?? null);
        $headline = UtilityClass::sanitizeValue($block['headline'] ?? null);
        $caption = UtilityClass::sanitizeValue($block['caption'] ?? null);
        $cite = UtilityClass::sanitizeValue($block['cite'] ?? null);
        $isOnDemand = empty($block['isOnDemand']) ? null : 'on_demand';
        $iframeHeight = UtilityClass::sanitizeValue($block['iframeHeight'] ?? null);

        $html = '';

        if (isset($mainData)) {
            $iFrameCrcId = crc32($mainData);
            $html .= "<div class='media_block iframe_container {$isFluid[0]}'>";
            $html .= $this->writeHeadline($headline, $isFluid, 'h4');
            $html .= "<div class='iframe_wrapper rounded-2 overflow-hidden $isOnDemand' id='wrapper_{$iFrameCrcId}' ";
            $html .= $isOnDemand ? "data-target-height='$iframeHeight' data-iframe-src='$mainData'>" : "style='height:$iframeHeight'>";
            $html .= $isOnDemand ? "<button class='load_iframe border-0 btn btn-danger bg_red2 px-4'>Load Content</button>" : null;

            if ($isOnDemand) {
                $html .= "<iframe class='' width='100%' height='100%' src='' id='iframe_$iFrameCrcId'></iframe>";
            } else {
                $html .= "<iframe class='' width='100%' height='100%' src='$mainData' id='iframe_$iFrameCrcId'></iframe>";
            }

            $html .= "</div>";
            $html .= $this->writeCaption($caption, $isFluid, $cite);
            $html .= "</div>";
        } else {
            $html .= $this->reportDataError($sectionName, $block, $index);
        }

        return $html;
    }
    private function iframeEmbedBlockFormatter($sectionName, $block, $index)
    {
        $dirPath = $this->projPath;
        $isFluid = $this->fluidProcessor($block);
        $mainData = UtilityClass::sanitizeValue($block['data'] ?? null);
        $headline = UtilityClass::sanitizeValue($block['headline'] ?? null);
        $caption = UtilityClass::sanitizeValue($block['caption'] ?? null);
        $cite = UtilityClass::sanitizeValue($block['cite'] ?? null);
        $isOnDemand = empty($block['isOnDemand']) ? null : 'on_demand';
        $iframeHeight = UtilityClass::sanitizeValue($block['iframeHeight'] ?? null);

        $html = '';

        if (isset($mainData)) {

            $iframeSrc = '';
            $htmlContent = file_get_contents($dirPath . '/' . $mainData);
            if ($isOnDemand) {
                $pattern = '/(<iframe[^>]*src=)["\']([^"\']+)["\']([^>]*>)/i';
                if (preg_match($pattern, $htmlContent, $matches)) {
                    $iframeSrc = $matches[2];
                    // Replace the src attribute with an empty string
                    $htmlContent = preg_replace($pattern, '$1""$3', $htmlContent);
                } else {
                    $iframeSrc = null;
                }
            }

            $iFrameCrcId = crc32($mainData);
            $html .= "<div class='media_block iframe_container {$isFluid[0]}'>";
            $html .= $this->writeHeadline($headline, $isFluid, 'h4');
            $html .= "<div class='iframe_wrapper rounded-2 overflow-hidden $isOnDemand' id='wrapper_{$iFrameCrcId}' ";
            $html .= $isOnDemand ? "data-target-height='$iframeHeight' data-iframe-src='$iframeSrc'>" : "style='height:$iframeHeight'>";
            $html .= $isOnDemand ? "<button class='load_iframe border-0 btn btn-danger bg_red2 px-4'>Load Content</button>" : null;

            $html .= $htmlContent;

            $html .= "</div>";
            $html .= $this->writeCaption($caption, $isFluid, $cite);
            $html .= "</div>";
        } else {
            $html .= $this->reportDataError($sectionName, $block, $index);
        }

        return $html;
    }
    private function htmlEmbedBlockFormatter($sectionName, $block, $index)
    {
        $dirPath = $this->projPath;
        $isFluid = $this->fluidProcessor($block);
        $mainData = UtilityClass::sanitizeValue($block['data'] ?? null);
        $headline = UtilityClass::sanitizeValue($block['headline'] ?? null);
        $caption = UtilityClass::sanitizeValue($block['caption'] ?? null);
        $cite = UtilityClass::sanitizeValue($block['cite'] ?? null);

        $html = '';

        if (isset($mainData)) {
            $htmlBlockCrcId = crc32($mainData);
            $html .= "<div class='media_block html_container {$isFluid[0]}'>";
            $html .= $this->writeHeadline($headline, $isFluid, 'h4');

            $html .= "<div class='html_wrapper rounded-2' id='wrapper_{$htmlBlockCrcId}'>";

            $htmlContent = file_get_contents($dirPath . '/' . $mainData);
            $html .= $htmlContent;

            $html .= "</div>";

            $html .= $this->writeCaption($caption, $isFluid, $cite);
            $html .= "</div>";
        } else {
            $html .= $this->reportDataError($sectionName, $block, $index);
        }

        return $html;
    }



    // error //////////////////////////////////////////////////////////////////////

    private static function reportMediaTypeError($sectionName, $block, $index)
    {
        $html = null;
        $type = !empty($block['type']) ? strval($block['type']) : "empty";
        $html .= "<div class='media_block col-12}'><div class='alert alert-dark' role='alert'>";
        $html .= "<h5 class='mb-3'>Media Type Error - Wrong Media Type:</h5>";
        $html .= "<p class='lead'>{$sectionName} -> chunk [$index]</p>";
        $html .= "<p>['type'] => '$type' is not a valid value</p><hr>";
        $html .= "<p>" . var_dump($block) . "</p></div></div>";
        return $html;
    }
    private static function reportDataError($sectionName, $block, $index)
    {
        $html = null;
        $mediaType = $block['type'] ?? 'empty';
        $data = $block['data'] ?? null;
        $dataType = gettype($data) ?? null;
        $html .= "<div class='media_block col-12}'><div class='alert alert-secondary' role='alert'>";
        $html .= "<h5 class='mb-3'>Data Type Error - " . ($data ? "Wrong Data Type:" : "Empty Data Field") . "</h5>";
        $html .= "<p class='lead'>{$sectionName} -> chunk [$index]</p>";
        if ($data) {
            $html .= "<p>For the ['type'] => '$mediaType' -- element ['data'] can't be '$dataType' type</p><hr>";
            $html .= "<p>" . var_dump($block) . "</p>";
        } else {
            $html .= "<hr>";
            $html .= "<p>[Empty Data]</p>";
        }
        $html .= "</div></div>";
        return $html;
    }

}

?>