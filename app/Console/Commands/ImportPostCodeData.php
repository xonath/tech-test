<?php

namespace App\Console\Commands;

use App\Models\Postcode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Csv\Reader;
use ZipArchive;

class ImportPostCodeData extends Command
{
    private const POSTCODE_DATA_URL = 'https://parlvid.mysociety.org/os/ONSPD/2022-11.zip';
    private const DESTINATION = 'data';
    private const CHUNK_SIZE = 500;

    protected array $filesToImport = []; // a list of csv files ot import
    protected array $batch = []; // records of postcodes to be inserted in batch
    protected int $counter = 0; // counter - used to indicate if a batch needs to be sent to the db

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-post-code-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download and import UK data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->downloadPostcodeData();
        $this->unzipPostcodeData();

        foreach ($this->filesToImport as $file) {
            $this->info('Processing ' . Str::afterLast($file, '/'));
            $this->importPostcodesFromCsv($file);
        }

        $this->cleanup();

        $this->info('Completed');
    }

    protected function cleanup(): void
    {
        $this->info('Cleaning up old files...');

        // remove all unzipped files
        File::deleteDirectory(Storage::path(self::DESTINATION . '/unzipped'));

        // remove the postcode zip file
        File::delete(Storage::path('data/postcode.zip'));
    }

    protected function importPostcodesFromCsv(string $file): void
    {
        $reader = Reader::createFromPath(Storage::path($file), 'r');
        $reader->setHeaderOffset(0);
        $records = $reader->getRecords();

        foreach ($records as $row) {
            $this->processRow($row);
        }

        $this->saveBatch();
    }

    protected function processRow(array $row): void
    {
        $this->batch[] = $this->rowDto($row);
        $this->counter++;

        if ($this->counter === self::CHUNK_SIZE) {
            $this->saveBatch();
        }
    }

    protected function rowDto(array $row): array
    {
        return [
            'postcode' => Str::replace(' ', '', $row['pcd']),
            'latitude' => $row['lat'],
            'longitude' => $row['long'],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    protected function saveBatch(): void
    {
        if (empty($this->batch)) {
            return;
        }

        Postcode::insert($this->batch);

        $this->counter = 0;
        $this->batch = [];
    }

    protected function downloadPostcodeData(): void
    {
        $this->info('Downloading postcode file...');

        // ensure that destination exists
        if (!Storage::exists(self::DESTINATION)) {
            Storage::makeDirectory(self::DESTINATION);
        }

        try {
            // Open a stream to the destination file
            $fileHandle = fopen(Storage::path('data/postcode.zip'), 'w');

            // Stream the download using Guzzle
            $response = Http::sink($fileHandle)->get(self::POSTCODE_DATA_URL);

            if ($response->successful()) {
                $this->info('File downloaded successfully.');
            } else {
                $this->error('Failed to download file. Status code: ' . $response->status());
                exit();
            }

            // Close the file handle
            fclose($fileHandle);

        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }

    protected function unzipPostcodeData(): void
    {
        $this->info('Unzipping - Please wait...');
        $zip = new ZipArchive;

        if ($zip->open(Storage::path('data/postcode.zip')) === TRUE) {
            $zip->extractTo(Storage::path(self::DESTINATION . '/unzipped'));
            $zip->close();

            // add files to the filesToImport array
            $this->filesToImport = array_filter(Storage::files(self::DESTINATION . '/unzipped/data/multi_csv'), function ($file) {
                return File::extension($file) === 'csv';
            });
        } else {
            $this->error('Failed to open the zip file at ' . self::DESTINATION . '/postcode.zip');
            exit();
        }
    }
}
