<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransferLanguageCommand extends Command implements FromArray, WithHeadings
{
    use Exportable;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:transfer-language {--input=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'make language transfer to/from excel  ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $input = $this->option('input');

        // 写入
        if ($input){
            $rows    = Excel::toArray(new TransferLanguageIn(), app_path('../resources/lang/language.xlsx'));
            $newData = $this->makeInputReady($rows[0]);
            $this->replaceData($newData);
            dd('import done');
        }

        File::exists(app_path('../resources/lang/language.xlsx')) or touch(app_path('../resources/lang/language.xlsx'));
        Excel::store(new self(), app_path('../resources/lang/language.xlsx'));
        dd('export done');
    }

    public function array(): array
    {
        $languageExport = $this->getOldData();
        return $languageExport;
    }

    public function headings(): array
    {
        return ['Language', 'FileName', 'Key', 'Value', 'Comment', 'Usage', 'Comment By Dev'];
    }

    private function makeInputReady($rows)
    {
        $newRows = [];
        foreach ($rows as $row) {
            $newRows[$row[0]][$row[1]][$row[2]] = $row[3];
        }
        return $newRows;
    }

    private function getOldData()
    {
        $languageList   = ['en-US', 'zh-CN', 'th', 'vi-VN'];
        $languageExport = [];
        foreach ($languageList as $languageCode) {
            $path  = app_path('../resources/lang/') . $languageCode;
            $files = File::allFiles($path);
            foreach ($files as $file) {
                $array = require $file; // k=>v
                foreach ($array as $key => $value) {
                    $languageExport[$languageCode][] = [
                        $languageCode,
                        $file->getRealPath(),
                        $key,
                        $value,
                        null,
                        null,
                        'dont change the key!!',
                    ];
                }
            }
        }
        return $languageExport;
    }

    private function replaceData($new)
    {
        $exceptionLanguages = [];
        $exceptionFiles     = ['validation.php'];
        // language, filePath, key, value
        foreach ($new as $language => $fileData) {
            if ($this->checkInArray($language, $exceptionLanguages)){
                continue;
            }
            foreach ($fileData as $file => $content) {
                if ($this->checkInArray($file, $exceptionFiles)){
                    continue;
                }

                if (! File::exists($file)){
                    continue;
                }
                $fileContent = File::get($file);
                foreach ($content as $key => $value) {
                    $relation = require $file;
                    if (isset($relation[$key]) && is_string($relation[$key])) {
                        $relation[$key] != $value and $fileContent = str_replace($relation[$key], $value, $fileContent);
                    }
                }
                File::put($file, $fileContent);
            }
        }
    }

    private function checkInArray(string $check, array $exception)
    {
        foreach ($exception as $fileName){
            if (strstr($check, $fileName) != false){
                return true;
            }
        }
        return false;
    }
}

class TransferLanguageIn implements ToArray, WithStartRow
{
    public function array(array $rows)
    {
        return $rows;
    }

    public function startRow(): int
    {
        return 2;
    }
}


