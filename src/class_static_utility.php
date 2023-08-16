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
        if (empty($inputData)) {
            return;
        } elseif (!is_array($inputData)) {
            echo strval($inputData);
        } elseif (is_array($inputData)) {
            echo "<ul class='indent_list'>";
            foreach ($inputData as $listitem) {
                echo "<li>";
                if (is_array($listitem)) {
                    self::textWithNestingList($listitem);
                } else {
                    echo strval($listitem);
                }
                echo "</li>";
            }
            echo "</ul>";
        }
    }


    public static function findLargerImage($path, $filename)
    {

        $dirPath = $path;
        $info = pathinfo($filename);
        // $baseName = $info['filename'];
        $pathWithoutExtension = $info['dirname'] . '/' . $info['filename'];
        echo $pathWithoutExtension;
        // $extension = $info['extension'];

        // Construct the pattern to search for originalname@something.something
        $pattern = $dirPath . '/' . $pathWithoutExtension . '@*.*';
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