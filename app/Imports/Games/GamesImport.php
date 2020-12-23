<?php

namespace App\Imports;

use App\Models\GamePlatformProduct;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class GamesImport implements ToCollection, WithStartRow
{
    protected $product;

    public function __construct(GamePlatformProduct $product)
    {
        $this->product = $product;
    }

    /**
     * @param Collection $rows
     * @throws
     */
    public function collection(Collection $rows)
    {
        $data = $this->getExcelData($rows);

        batch_insert('games', $data, true);
    }

    public function getExcelData(Collection $rows)
    {
        $games = [];

        $now = now();

        foreach ($rows as $row) {

            if (empty($row[1])) {
                break;
            }

            $code = (string)$row[0];
            $games[] = [
                'platform_code' => $this->product->platform_code,
                'product_code'  => $this->product->code,
                'type'          => $this->product->type,
                'code'          => $code,
                'currencies'    => ['USD', 'VND', 'THB'],
                'languages'     => [
                    [
                        'language'      => 'en-US',
                        'name'          => $row[1],
                        'description'   => $row[1],
                        'content'       => $row[1],
                    ],
                    [
                        'language'      => 'vi-VN',
                        'name'          => $row[2],
                        'description'   => $row[2],
                        'content'       => $row[2],
                    ],
                    [
                        'language'      => 'th',
                        'name'          => $row[3],
                        'description'   => $row[3],
                        'content'       => $row[3],
                    ],
                ],
                'devices'           => array_keys(User::$devices),
                'web_img_path'      => 'uploads/games/web/' . strtolower($this->product->platform_code)  . '/' . $code . '.png',
                'mobile_img_path'   => 'uploads/games/mobile/' . strtolower($this->product->platform_code)  . '/' . $code . '.png',
                'created_at'        => $now,
                'updated_at'        => $now,
            ];


        }

        return $games;
    }

    public function startRow(): int
    {
        return 2;
    }
}
