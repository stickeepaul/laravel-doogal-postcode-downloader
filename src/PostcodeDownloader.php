<?php

namespace stickeepaul\PostcodeDownloader;

use Exception;
use GuzzleHttp\Client;
use League\Csv\Reader;
use League\Csv\Writer;

class PostcodeDownloader
{
    /**
     * The CSV URL.
     */
    const RAW_CSV_URL = 'https://www.doogal.co.uk/files/postcodes.zip';

    /**
     * The name of the source Zip file.
     */
    const SRC_ZIP_FILE = 'src-postcodes.zip';

    /**
     * The name of the CSV file.
     */
    const CSV_FILE = 'postcodes.csv';

    /**
     * If the postcodes not in use should be skipped.
     *
     * @var bool
     */
    private bool $onlyIncludeInUse;

    /**
     * The fields to write.
     *
     * @var array
     */
    private array $fields = [];

    /**
     * The directory to store files in.
     *
     * @var string
     */
    private string $directory;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this
            ->setOnlyIncludeInUse()
            ->setFields()
            ->setGzip()
            ->setDirectory();
    }

    /**
     * Sets the onlyIncludeInUse property.
     *
     * @return self
     */
    private function setOnlyIncludeInUse(): self
    {
        $this->onlyIncludeInUse = config('postcode-downloader.only_include_in_use');

        return $this;
    }

    /**
     * Sets the fields property.
     *
     * @return self
     */
    private function setFields(): self
    {
        $fields = [];

        foreach (config('postcode-downloader.fields') as $field => $used) {
            if ($used) {
                $fields[] = $field;
            }
        }

        $this->fields = $fields;

        return $this;
    }

    /**
     * Sets the gzip property.
     *
     * @return self
     */
    private function setGzip(): self
    {
        $this->gzip = config('postcode-downloader.gzip');

        return $this;
    }

    /**
     * Sets the directory property
     * and creates it if it does not exist.
     *
     * @return self
     */
    private function setDirectory(): self
    {
        $directory = config('postcode-downloader.directory');

        $this->directory = $directory;

        if ($directory !== false && !is_dir($directory)) {
            mkdir($directory);
        }

        return $this;
    }
    
    /**
     * Runs the downloader and writes the new CSV.
     *
     * @return void
     */
    public function run(): void
    {
        $this->download();
        $this->write();
    }

    /**
     * Downloads the CSV.
     *
     * @return void
     */
    public function download(): void
    {
        $client = new Client([
            \GuzzleHttp\RequestOptions::VERIFY => false, // SSL issue
        ]);

        $dest = $this->directory . DIRECTORY_SEPARATOR . self::SRC_ZIP_FILE;
        $resource = \GuzzleHttp\Psr7\Utils::tryFopen($dest, 'w');
        $client->request('GET', self::RAW_CSV_URL, ['sink' => $resource]);

        $this->unzip();
        $this->deleteSourceZip();
        $this->renameExtractedCsvFile();
    }

    /**
     * Unzips the CSV.
     *
     * @return void
     */
    private function unzip(): void
    {
        $file = $this->directory . DIRECTORY_SEPARATOR . self::SRC_ZIP_FILE;
        $path = pathinfo(realpath($file), PATHINFO_DIRNAME);

        $zip = new \ZipArchive;
        $res = $zip->open($file);
        if ($res === TRUE) {
            $zip->extractTo($path);
            $zip->close();
        } else {
            throw new Exception('Could not extract ZIP.');
        }
    }

    /**
     * Deletes the Zip file that was downloaded.
     *
     * @return void
     */
    private function deleteSourceZip(): void
    {
        unlink($this->directory . DIRECTORY_SEPARATOR . self::SRC_ZIP_FILE);
    }

    /**
     * Deletes the source CSV file.
     *
     * @return void
     */
    private function deleteSourceCsv(): void
    {
        unlink($this->directory . DIRECTORY_SEPARATOR . "doogal-" . self::CSV_FILE);
    }

    /**
     * Renames the extracted CSV file.
     *
     * @return void
     */
    private function renameExtractedCsvFile(): void
    {
        $fileName = self::CSV_FILE;
        
        rename(
            $this->directory . DIRECTORY_SEPARATOR . $fileName,
            $this->directory . DIRECTORY_SEPARATOR . "doogal-{$fileName}"
        );
    }

    /**
     * Writes the new CSV with only relevant fields.
     *
     * @return void
     */
    public function write(): void
    {
        $src = $this->directory . DIRECTORY_SEPARATOR . 'doogal-' . self::CSV_FILE;

        $reader = Reader::createFromStream(
            \GuzzleHttp\Psr7\Utils::tryFopen($src, 'r+')
        );

        $reader->setHeaderOffset(0);

        $dest = $this->directory . DIRECTORY_SEPARATOR . self::CSV_FILE;

        if ($this->gzip) {
            $stream = gzopen("${dest}.gz", 'w');
            $writer = Writer::createFromStream($stream);
        } else {
            $writer = Writer::createFromPath($dest, 'w');
        }

        $writer->insertOne($this->fields);

        foreach ($reader as $row) {
            if ($this->shouldSkipRow($row)) {
                continue;
            }
            $data = [];
            foreach ($this->fields as $field) {
                $data[] = $row[$field];
            }
            $writer->insertOne($data);
        }
        
        if ($this->gzip) {
            gzclose($stream);
        }

        $this->deleteSourceCsv();
    }

    /**
     * Returns if the row should be skipped.
     *
     * @param array $row
     *
     * @return bool
     */
    private function shouldSkipRow(array $row): bool
    {
        if ($this->onlyIncludeInUse) {
            return $row['In Use?'] === 'No';
        }

        return false;
    }

}