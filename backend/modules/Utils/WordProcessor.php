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
        $templateProcessor = new TemplateProcessor($templateUrl);
        $templateProcessor->setValues($simpleValuesToReplace);

        foreach ($imagesToReplace as $key => $imageValue) {
            $templateProcessor->setImageValue($key, $imageValue);
        }

        foreach ($blocksToReplace as $key => $blockValue) {
            $templateProcessor->cloneBlock($key, 0, true, false, $blockValue);
        }

        foreach ($rowsToReplace as $key => $rowValue) {
            $templateProcessor->cloneRowAndSetValues($key, $rowValue);
        }

        $filename = $fileToSave ?: (Str::uuid() . '.docx');
        $templateProcessor->saveAs("/tmp/$filename");

        return "/tmp/$filename";
    }

    /**
     * @deprecated
     * TODO: remover função em breve e remover dependência php tcpdf
     */
    public static function saveAsPdf(string $fileUrl)
    {
        Settings::setPdfRendererName(Settings::PDF_RENDERER_TCPDF);
        Settings::setPdfRendererPath(base_path().'/vendor/tecnickcom/tcpdf');

        $phpWord = IOFactory::load($fileUrl, 'Word2007');

        $extension = array_reverse(explode('.', $fileUrl))[0];
        $pdfFilePath = str_replace(".$extension", '.pdf', $fileUrl);

        $phpWord->save($pdfFilePath, 'PDF');

        return $pdfFilePath;
    }
}
