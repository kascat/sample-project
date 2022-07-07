<?php

namespace Utils;

/**
 * Class LibreOffice
 * @package Utils
 */
class LibreOffice
{
    /**
     * @param string $filePath
     * @return array|string|string[]
     * @throws \Exception
     */
    public static function convertToPdf(string $filePath)
    {
        $extension = array_reverse(explode('.', $filePath))[0];
        $pdfFilePath = str_replace(".$extension", '.pdf', $filePath);

        shell_exec("echo 'rm $pdfFilePath' > /var/www/app/pipe");

        $filename = array_reverse(explode('/', $filePath))[0];
        $path = str_replace($filename, '', $filePath) ?: '/tmp/';

        shell_exec("echo 'libreoffice --headless --convert-to pdf \"$filePath\" --outdir $path' > /var/www/app/pipe");

        $attemptsToFindFile = 10;

        do {
            sleep(1);

            $fileExists = is_readable($pdfFilePath);

            $attemptsToFindFile--;
            if ($attemptsToFindFile === 0) {
                throw new \Exception('Pdf não encontrado');
            }
        } while (!$fileExists);

        return $pdfFilePath;
    }
}
