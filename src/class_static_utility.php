<?php

class UtilityClass
{
    public static function sanitizeValue($data, $sanitize = true)
    {
        if (!empty($data)) {
            if ($sanitize) {
                if (!is_array($data)) {
                    return htmlspecialchars(strval($data));
                } elseif (is_array($data)) {
                    foreach ($data as &$value) {
                        $value = self::sanitizeValue($value, true); // Recursively sanitize the value
                    }
                    unset($value); // To break the reference with the last element
                    return $data;
                }
            }
            return $data;
        }
        return null;
    }
    public static function textWithNestingList($inputData)
    {
        $r = null;

        if (empty($inputData)) {
            return;
        } elseif (!is_array($inputData)) {
            $r .= strval($inputData);
        } elseif (is_array($inputData)) {
            $r .= "<ul class='indent_list'>";
            foreach ($inputData as $listitem) {
                $r .= "<li class='markdown'>";

                if (is_array($listitem)) {
                    $r .= self::textWithNestingList($listitem);
                } else {
                    $r .= strval($listitem);
                }

                $r .= "</li>";
            }
            $r .= "</ul>";
        }

        return $r;
    }



    public static function findLargerImage($path, $filename)
    {
        $info = pathinfo($filename);
        $extension = $info['extension'];
        $pathWithoutExtension = $info['dirname'] . '/' . $info['filename'];

        // Construct the pattern to search for originalname@something.extension
        $pattern = $path . '/' . $pathWithoutExtension . '@*.' . $extension;
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


}

?>