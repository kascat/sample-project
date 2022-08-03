<?php

namespace Utils;

use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;

/**
 * Class WordProcessor
 * @package Utils
 */
class WordProcessor
{
    /**
     * @param string $templateUrl
     * @param string|null $fileToSave
     * @param array $simpleValuesToReplace
     * @param array $imagesToReplace
     * @param array $rowsToReplace
     * @param array $blocksToReplace
     * @return string
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     */
    public static function processWordTemplate(
        string $templateUrl,
        string $fileToSave = null,
        array $simpleValuesToReplace = [],
        array $imagesToReplace = [],
        array $rowsToReplace = [],
        array $blocksToReplace = [],
    ) {
        $imageValues = [];
        $simpleValues = [];
        foreach ($simpleValuesToReplace as $key => $value) {
            if (str_contains($key, '+')) {
                $imageValues[str_replace('+', '', $key)] = $value;
            } else {
                $simpleValues[$key] = $value;
            }
        }

        $templateProcessor = new TemplateProcessor($templateUrl);
        $templateProcessor->setValues($simpleValues);

        foreach (array_merge($imageValues, $imagesToReplace) as $key => $imageValue) {
            if ($imageValue) {
                $templateProcessor->setImageValue($key, $imageValue);
            }
        }

        foreach ($blocksToReplace as $key => $blockValue) {
            $templateProcessor->cloneBlock($key, 0, true, false, $blockValue);
        }

        foreach ($rowsToReplace as $key => $value) {
            $imageValues = [];
            $rowItems = [];
            foreach ($value as $rowKey => $rowValue) {
                foreach ($rowValue as $propKey => $propValue) {
                    if (str_contains($propKey, '+')) {
                        $imageValues[$rowKey][str_replace('+', '', $propKey)] = $propValue;
                    } else {
                        $rowItems[$rowKey][$propKey] = $propValue;
                    }
                }
            }

            $templateProcessor->cloneRowAndSetValues($key, $rowItems);

            foreach ($imageValues as $rowKey => $imageItems) {
                foreach ($imageItems as $imageKey => $imageValue) {
                    $currentKey = "$imageKey#" . $rowKey+1;
                    if ($imageValue) {
                        $templateProcessor->setImageValue($currentKey, $imageValue);
                    }
                }
            }
        }

        $filename = $fileToSave ?: (Str::uuid() . '.docx');
        $templateProcessor->saveAs("/tmp/$filename");

        return "/tmp/$filename";
    }
}
