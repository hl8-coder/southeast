<?php

use Illuminate\Database\Seeder;
use App\Models\GamePlatform;
use App\Models\GamePlatformProduct;
use App\Models\Game;
use App\Models\ChangingConfig;

class PPPlatformSeeder extends Seeder
{

	const CODE = 'PP';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        #Game Platform Start
        if (app()->isLocal()) {
            $platform = [
                'name'                  => self::CODE,
                'code'                  => self::CODE,
                'request_url'           => 'https://api.prerelease-env.biz/IntegrationService/v3/http/CasinoGameAPI',
                'report_request_url'    => 'https://api.prerelease-env.biz/IntegrationService/v3/DataFeeds',
                'launcher_request_url'  => 'https://demogamesfree-asia.pragmaticplay.net/gs2c/openGame.do?gameSymbol={game_symbol}&lang={language}&cur={currency_symbol}&stylename={secureLogin}',
                'rsa_our_private_key'   => '',
                'rsa_our_public_key'    => '',
                'rsa_public_key'        => '',
                'account'               => '{"language": "en", "secured_login": "2success_hl8vietnam", "secured_password": "testKey", "secure_key": "testKey"}',
                'exchange_currencies'   => null,
                'is_update_list'        => false,
                'update_interval'       => 7,
                'interval'              => 10, # 间隔时间
                'delay'                 => 0, # 延迟时间
                'offset'                => 10, # 向前偏移时间
                'limit'                 => 1, # 每分钟拉取次数
                'status'                => true,
                'icon'                  => ''
            ];
        } else {
            $platform = [
                'name'                  => self::CODE,
                'code'                  => self::CODE,
                'request_url'           => 'https://api.prerelease-env.biz/IntegrationService/v3/http/CasinoGameAPI',
                'report_request_url'    => 'https://api.prerelease-env.biz/IntegrationService/v3/http/CasinoGameAPI',
                'launcher_request_url'  => 'https://demogamesfree-asia.pragmaticplay.net/gs2c/openGame.do?gameSymbol={game_symbol}&lang={language}&cur={currency_symbol}&stylename={secureLogin}',
                'rsa_our_private_key'   => '',
                'rsa_our_public_key'    => '',
                'rsa_public_key'        => '',
                'account'               => '{"language": "en", "secured_login": "2success_hl8vietnam", "secured_password": "testKey", "secure_key": "testKey"}',
                'exchange_currencies'   => null,
                'is_update_list'        => false,
                'update_interval'       => 7,
                'interval'              => 10, # 间隔时间
                'delay'                 => 0, # 延迟时间
                'offset'                => 10, # 向前偏移时间
                'limit'                 => 1, # 每分钟拉取次数
                'status'                => true,
                'icon'                  => ''
            ];
        }

        GamePlatform::insert($platform);
        #Game Platform End

        #Game Platform Product Start
        $products = [
            [
                'platform_code'  => self::CODE,
                'code'           => 'PP_Slot',
                'type'           => GamePlatformProduct::TYPE_SLOT,
                'currencies'     => ['VND', 'THB', 'USD'],
                'languages'     => [
                    [
                        'language'    => 'en-US',
                        'name'        => 'PP_Slot',
                        'description' => 'PP_Slot',
                        'content'     => 'PP_Slot',
                    ],
                    [
                        'language'    => 'vi-VN',
                        'name'        => 'PP_Slot',
                        'description' => 'PP_Slot',
                        'content'     => 'PP_Slot',
                    ],
                    [
                        'language'    => 'th',
                        'name'        => 'PP_Slot',
                        'description' => 'PP_Slot',
                        'content'     => 'PP_Slot',
                    ],
                ],
                'is_can_try'	=> 1,
                'devices'       => [1, 2],
            ]
        ];

        foreach ($products as $product) {
            GamePlatformProduct::query()->create($product);
        }
        #Game Platform Product End

       
        #Game Platform Games Start
        $games=  [
            
			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs7776aztec',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Aztec Bonanza',
                        'description'   => 'Aztec Bonanza',
                        'content'       => 'Aztec Bonanza'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Aztec Bonanza',
                        'description'   => 'Aztec Bonanza',
                        'content'       => 'Aztec Bonanza'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Aztec Bonanza',
                        'description'   => 'Aztec Bonanza',
                        'content'       => 'Aztec Bonanza'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs10bookoftut',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Book of Tut',
                        'description'   => 'Book of Tut',
                        'content'       => 'Book of Tut'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Book of Tut',
                        'description'   => 'Book of Tut',
                        'content'       => 'Book of Tut'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Book of Tut',
                        'description'   => 'Book of Tut',
                        'content'       => 'Book of Tut'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs75bronco',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Bronco Spirit',
                        'description'   => 'Bronco Spirit',
                        'content'       => 'Bronco Spirit'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Bronco Spirit',
                        'description'   => 'Bronco Spirit',
                        'content'       => 'Bronco Spirit'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Bronco Spirit',
                        'description'   => 'Bronco Spirit',
                        'content'       => 'Bronco Spirit'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs243dancingpar',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Dance Party',
                        'description'   => 'Dance Party',
                        'content'       => 'Dance Party'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Dance Party',
                        'description'   => 'Dance Party',
                        'content'       => 'Dance Party'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Dance Party',
                        'description'   => 'Dance Party',
                        'content'       => 'Dance Party'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs40frrainbow',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Fruit Rainbow',
                        'description'   => 'Fruit Rainbow',
                        'content'       => 'Fruit Rainbow'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Fruit Rainbow',
                        'description'   => 'Fruit Rainbow',
                        'content'       => 'Fruit Rainbow'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Fruit Rainbow',
                        'description'   => 'Fruit Rainbow',
                        'content'       => 'Fruit Rainbow'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs75empress',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Golden Beauty',
                        'description'   => 'Golden Beauty',
                        'content'       => 'Golden Beauty'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Golden Beauty',
                        'description'   => 'Golden Beauty',
                        'content'       => 'Golden Beauty'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Golden Beauty',
                        'description'   => 'Golden Beauty',
                        'content'       => 'Golden Beauty'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vswaysrhino',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Great Rhino Megaways',
                        'description'   => 'Great Rhino Megaways',
                        'content'       => 'Great Rhino Megaways'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Great Rhino Megaways',
                        'description'   => 'Great Rhino Megaways',
                        'content'       => 'Great Rhino Megaways'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Great Rhino Megaways',
                        'description'   => 'Great Rhino Megaways',
                        'content'       => 'Great Rhino Megaways'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs5hotburn',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Hot to Burn',
                        'description'   => 'Hot to Burn',
                        'content'       => 'Hot to Burn'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Hot to Burn',
                        'description'   => 'Hot to Burn',
                        'content'       => 'Hot to Burn'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Hot to Burn',
                        'description'   => 'Hot to Burn',
                        'content'       => 'Hot to Burn'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs1ball',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Lucky Dragon Ball',
                        'description'   => 'Lucky Dragon Ball',
                        'content'       => 'Lucky Dragon Ball'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Lucky Dragon Ball',
                        'description'   => 'Lucky Dragon Ball',
                        'content'       => 'Lucky Dragon Ball'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Lucky Dragon Ball',
                        'description'   => 'Lucky Dragon Ball',
                        'content'       => 'Lucky Dragon Ball'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs1masterjoker',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Master Joker',
                        'description'   => 'Master Joker',
                        'content'       => 'Master Joker'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Master Joker',
                        'description'   => 'Master Joker',
                        'content'       => 'Master Joker'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Master Joker',
                        'description'   => 'Master Joker',
                        'content'       => 'Master Joker'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs40madwheel',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'The Wild Machine',
                        'description'   => 'The Wild Machine',
                        'content'       => 'The Wild Machine'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'The Wild Machine',
                        'description'   => 'The Wild Machine',
                        'content'       => 'The Wild Machine'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'The Wild Machine',
                        'description'   => 'The Wild Machine',
                        'content'       => 'The Wild Machine'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs10threestar',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Three Star Fortune',
                        'description'   => 'Three Star Fortune',
                        'content'       => 'Three Star Fortune'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Three Star Fortune',
                        'description'   => 'Three Star Fortune',
                        'content'       => 'Three Star Fortune'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Three Star Fortune',
                        'description'   => 'Three Star Fortune',
                        'content'       => 'Three Star Fortune'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs40wildwest',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Wild West Gold',
                        'description'   => 'Wild West Gold',
                        'content'       => 'Wild West Gold'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Wild West Gold',
                        'description'   => 'Wild West Gold',
                        'content'       => 'Wild West Gold'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Wild West Gold',
                        'description'   => 'Wild West Gold',
                        'content'       => 'Wild West Gold'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs4096mystery',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Mysterious',
                        'description'   => 'Mysterious',
                        'content'       => 'Mysterious'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Mysterious',
                        'description'   => 'Mysterious',
                        'content'       => 'Mysterious'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Mysterious',
                        'description'   => 'Mysterious',
                        'content'       => 'Mysterious'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs5super7',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Super 7s ',
                        'description'   => 'Super 7s ',
                        'content'       => 'Super 7s '
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Super 7s ',
                        'description'   => 'Super 7s ',
                        'content'       => 'Super 7s '
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Super 7s ',
                        'description'   => 'Super 7s ',
                        'content'       => 'Super 7s '
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25mmouse',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Money Mouse',
                        'description'   => 'Money Mouse',
                        'content'       => 'Money Mouse'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Money Mouse',
                        'description'   => 'Money Mouse',
                        'content'       => 'Money Mouse'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Money Mouse',
                        'description'   => 'Money Mouse',
                        'content'       => 'Money Mouse'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20hercpeg',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Hercules and Pegasus',
                        'description'   => 'Hercules and Pegasus',
                        'content'       => 'Hercules and Pegasus'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Hercules and Pegasus',
                        'description'   => 'Hercules and Pegasus',
                        'content'       => 'Hercules and Pegasus'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Hercules and Pegasus',
                        'description'   => 'Hercules and Pegasus',
                        'content'       => 'Hercules and Pegasus'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20aladdinsorc',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Aladdin and the Sorcerer',
                        'description'   => 'Aladdin and the Sorcerer',
                        'content'       => 'Aladdin and the Sorcerer'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Aladdin and the Sorcerer',
                        'description'   => 'Aladdin and the Sorcerer',
                        'content'       => 'Aladdin and the Sorcerer'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Aladdin and the Sorcerer',
                        'description'   => 'Aladdin and the Sorcerer',
                        'content'       => 'Aladdin and the Sorcerer'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20honey',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Honey Honey Honey',
                        'description'   => 'Honey Honey Honey',
                        'content'       => 'Honey Honey Honey'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Honey Honey Honey',
                        'description'   => 'Honey Honey Honey',
                        'content'       => 'Honey Honey Honey'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Honey Honey Honey',
                        'description'   => 'Honey Honey Honey',
                        'content'       => 'Honey Honey Honey'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs1fortunetree',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Tree of Riches',
                        'description'   => 'Tree of Riches',
                        'content'       => 'Tree of Riches'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Tree of Riches',
                        'description'   => 'Tree of Riches',
                        'content'       => 'Tree of Riches'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Tree of Riches',
                        'description'   => 'Tree of Riches',
                        'content'       => 'Tree of Riches'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25scarabqueen',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'John Hunter and the Tomb of the Scarab Queen',
                        'description'   => 'John Hunter and the Tomb of the Scarab Queen',
                        'content'       => 'John Hunter and the Tomb of the Scarab Queen'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'John Hunter and the Tomb of the Scarab Queen',
                        'description'   => 'John Hunter and the Tomb of the Scarab Queen',
                        'content'       => 'John Hunter and the Tomb of the Scarab Queen'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'John Hunter and the Tomb of the Scarab Queen',
                        'description'   => 'John Hunter and the Tomb of the Scarab Queen',
                        'content'       => 'John Hunter and the Tomb of the Scarab Queen'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs243lionsgold',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => '5 Lions Gold',
                        'description'   => '5 Lions Gold',
                        'content'       => '5 Lions Gold'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => '5 Lions Gold',
                        'description'   => '5 Lions Gold',
                        'content'       => '5 Lions Gold'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => '5 Lions Gold',
                        'description'   => '5 Lions Gold',
                        'content'       => '5 Lions Gold'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs5spjoker',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Super Joker',
                        'description'   => 'Super Joker',
                        'content'       => 'Super Joker'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Super Joker',
                        'description'   => 'Super Joker',
                        'content'       => 'Super Joker'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Super Joker',
                        'description'   => 'Super Joker',
                        'content'       => 'Super Joker'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs243mwarrior',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Monkey Warrior',
                        'description'   => 'Monkey Warrior',
                        'content'       => 'Monkey Warrior'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Monkey Warrior',
                        'description'   => 'Monkey Warrior',
                        'content'       => 'Monkey Warrior'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Monkey Warrior',
                        'description'   => 'Monkey Warrior',
                        'content'       => 'Monkey Warrior'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs18mashang',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Treasure Horse',
                        'description'   => 'Treasure Horse',
                        'content'       => 'Treasure Horse'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Treasure Horse',
                        'description'   => 'Treasure Horse',
                        'content'       => 'Treasure Horse'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Treasure Horse',
                        'description'   => 'Treasure Horse',
                        'content'       => 'Treasure Horse'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs40pirate',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Pirate Gold',
                        'description'   => 'Pirate Gold',
                        'content'       => 'Pirate Gold'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Pirate Gold',
                        'description'   => 'Pirate Gold',
                        'content'       => 'Pirate Gold'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Pirate Gold',
                        'description'   => 'Pirate Gold',
                        'content'       => 'Pirate Gold'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25goldrush',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Gold Rush',
                        'description'   => 'Gold Rush',
                        'content'       => 'Gold Rush'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Gold Rush',
                        'description'   => 'Gold Rush',
                        'content'       => 'Gold Rush'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Gold Rush',
                        'description'   => 'Gold Rush',
                        'content'       => 'Gold Rush'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs10egyptcls',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Ancient Egypt Classic',
                        'description'   => 'Ancient Egypt Classic',
                        'content'       => 'Ancient Egypt Classic'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Ancient Egypt Classic',
                        'description'   => 'Ancient Egypt Classic',
                        'content'       => 'Ancient Egypt Classic'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Ancient Egypt Classic',
                        'description'   => 'Ancient Egypt Classic',
                        'content'       => 'Ancient Egypt Classic'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20doghouse',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'The Dog House',
                        'description'   => 'The Dog House',
                        'content'       => 'The Dog House'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'The Dog House',
                        'description'   => 'The Dog House',
                        'content'       => 'The Dog House'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'The Dog House',
                        'description'   => 'The Dog House',
                        'content'       => 'The Dog House'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25davinci',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Da Vinci\'s Treasure',
                        'description'   => 'Da Vinci\'s Treasure',
                        'content'       => 'Da Vinci\'s Treasure'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Da Vinci\'s Treasure',
                        'description'   => 'Da Vinci\'s Treasure',
                        'content'       => 'Da Vinci\'s Treasure'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Da Vinci\'s Treasure',
                        'description'   => 'Da Vinci\'s Treasure',
                        'content'       => 'Da Vinci\'s Treasure'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs7776secrets',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Aztec Treasure',
                        'description'   => 'Aztec Treasure',
                        'content'       => 'Aztec Treasure'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Aztec Treasure',
                        'description'   => 'Aztec Treasure',
                        'content'       => 'Aztec Treasure'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Aztec Treasure',
                        'description'   => 'Aztec Treasure',
                        'content'       => 'Aztec Treasure'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs5aztecgems',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Aztec Gems',
                        'description'   => 'Aztec Gems',
                        'content'       => 'Aztec Gems'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Aztec Gems',
                        'description'   => 'Aztec Gems',
                        'content'       => 'Aztec Gems'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Aztec Gems',
                        'description'   => 'Aztec Gems',
                        'content'       => 'Aztec Gems'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs243caishien',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Caishen\'s Cash',
                        'description'   => 'Caishen\'s Cash',
                        'content'       => 'Caishen\'s Cash'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Caishen\'s Cash',
                        'description'   => 'Caishen\'s Cash',
                        'content'       => 'Caishen\'s Cash'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Caishen\'s Cash',
                        'description'   => 'Caishen\'s Cash',
                        'content'       => 'Caishen\'s Cash'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs1dragon8',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => '888 Dragons',
                        'description'   => '888 Dragons',
                        'content'       => '888 Dragons'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => '888 Dragons',
                        'description'   => '888 Dragons',
                        'content'       => '888 Dragons'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => '888 Dragons',
                        'description'   => '888 Dragons',
                        'content'       => '888 Dragons'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25wolfgold',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Wolf Gold',
                        'description'   => 'Wolf Gold',
                        'content'       => 'Wolf Gold'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Wolf Gold',
                        'description'   => 'Wolf Gold',
                        'content'       => 'Wolf Gold'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Wolf Gold',
                        'description'   => 'Wolf Gold',
                        'content'       => 'Wolf Gold'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs9madmonkey',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Monkey Madness',
                        'description'   => 'Monkey Madness',
                        'content'       => 'Monkey Madness'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Monkey Madness',
                        'description'   => 'Monkey Madness',
                        'content'       => 'Monkey Madness'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Monkey Madness',
                        'description'   => 'Monkey Madness',
                        'content'       => 'Monkey Madness'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs243fortune',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Caishen\'s Gold',
                        'description'   => 'Caishen\'s Gold',
                        'content'       => 'Caishen\'s Gold'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Caishen\'s Gold',
                        'description'   => 'Caishen\'s Gold',
                        'content'       => 'Caishen\'s Gold'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Caishen\'s Gold',
                        'description'   => 'Caishen\'s Gold',
                        'content'       => 'Caishen\'s Gold'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs9hotroll',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Hot Chilli',
                        'description'   => 'Hot Chilli',
                        'content'       => 'Hot Chilli'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Hot Chilli',
                        'description'   => 'Hot Chilli',
                        'content'       => 'Hot Chilli'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Hot Chilli',
                        'description'   => 'Hot Chilli',
                        'content'       => 'Hot Chilli'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20rhino',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Great Rhino',
                        'description'   => 'Great Rhino',
                        'content'       => 'Great Rhino'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Great Rhino',
                        'description'   => 'Great Rhino',
                        'content'       => 'Great Rhino'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Great Rhino',
                        'description'   => 'Great Rhino',
                        'content'       => 'Great Rhino'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs10firestrike',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Fire Strike',
                        'description'   => 'Fire Strike',
                        'content'       => 'Fire Strike'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Fire Strike',
                        'description'   => 'Fire Strike',
                        'content'       => 'Fire Strike'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Fire Strike',
                        'description'   => 'Fire Strike',
                        'content'       => 'Fire Strike'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'bjmb',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'American Blackjack',
                        'description'   => 'American Blackjack',
                        'content'       => 'American Blackjack'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'American Blackjack',
                        'description'   => 'American Blackjack',
                        'content'       => 'American Blackjack'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'American Blackjack',
                        'description'   => 'American Blackjack',
                        'content'       => 'American Blackjack'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs10vampwolf',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Vampires vs Wolves',
                        'description'   => 'Vampires vs Wolves',
                        'content'       => 'Vampires vs Wolves'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Vampires vs Wolves',
                        'description'   => 'Vampires vs Wolves',
                        'content'       => 'Vampires vs Wolves'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Vampires vs Wolves',
                        'description'   => 'Vampires vs Wolves',
                        'content'       => 'Vampires vs Wolves'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20chicken',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'The Great Chicken Escape',
                        'description'   => 'The Great Chicken Escape',
                        'content'       => 'The Great Chicken Escape'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'The Great Chicken Escape',
                        'description'   => 'The Great Chicken Escape',
                        'content'       => 'The Great Chicken Escape'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'The Great Chicken Escape',
                        'description'   => 'The Great Chicken Escape',
                        'content'       => 'The Great Chicken Escape'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20egypttrs',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Egyptian Fortunes',
                        'description'   => 'Egyptian Fortunes',
                        'content'       => 'Egyptian Fortunes'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Egyptian Fortunes',
                        'description'   => 'Egyptian Fortunes',
                        'content'       => 'Egyptian Fortunes'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Egyptian Fortunes',
                        'description'   => 'Egyptian Fortunes',
                        'content'       => 'Egyptian Fortunes'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs5trjokers',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Triple Jokers',
                        'description'   => 'Triple Jokers',
                        'content'       => 'Triple Jokers'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Triple Jokers',
                        'description'   => 'Triple Jokers',
                        'content'       => 'Triple Jokers'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Triple Jokers',
                        'description'   => 'Triple Jokers',
                        'content'       => 'Triple Jokers'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'bndt',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Dragon Tiger',
                        'description'   => 'Dragon Tiger',
                        'content'       => 'Dragon Tiger'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Dragon Tiger',
                        'description'   => 'Dragon Tiger',
                        'content'       => 'Dragon Tiger'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Dragon Tiger',
                        'description'   => 'Dragon Tiger',
                        'content'       => 'Dragon Tiger'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20fruitsw',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Sweet Bonanza',
                        'description'   => 'Sweet Bonanza',
                        'content'       => 'Sweet Bonanza'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Sweet Bonanza',
                        'description'   => 'Sweet Bonanza',
                        'content'       => 'Sweet Bonanza'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Sweet Bonanza',
                        'description'   => 'Sweet Bonanza',
                        'content'       => 'Sweet Bonanza'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'bnadvanced',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Dragon Bonus Baccarat',
                        'description'   => 'Dragon Bonus Baccarat',
                        'content'       => 'Dragon Bonus Baccarat'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Dragon Bonus Baccarat',
                        'description'   => 'Dragon Bonus Baccarat',
                        'content'       => 'Dragon Bonus Baccarat'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Dragon Bonus Baccarat',
                        'description'   => 'Dragon Bonus Baccarat',
                        'content'       => 'Dragon Bonus Baccarat'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs10fruity2',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Extra Juicy',
                        'description'   => 'Extra Juicy',
                        'content'       => 'Extra Juicy'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Extra Juicy',
                        'description'   => 'Extra Juicy',
                        'content'       => 'Extra Juicy'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Extra Juicy',
                        'description'   => 'Extra Juicy',
                        'content'       => 'Extra Juicy'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25goldpig',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Golden Pig',
                        'description'   => 'Golden Pig',
                        'content'       => 'Golden Pig'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Golden Pig',
                        'description'   => 'Golden Pig',
                        'content'       => 'Golden Pig'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Golden Pig',
                        'description'   => 'Golden Pig',
                        'content'       => 'Golden Pig'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs50safariking',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Safari King',
                        'description'   => 'Safari King',
                        'content'       => 'Safari King'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Safari King',
                        'description'   => 'Safari King',
                        'content'       => 'Safari King'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Safari King',
                        'description'   => 'Safari King',
                        'content'       => 'Safari King'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20wildpix',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Wild Pixies',
                        'description'   => 'Wild Pixies',
                        'content'       => 'Wild Pixies'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Wild Pixies',
                        'description'   => 'Wild Pixies',
                        'content'       => 'Wild Pixies'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Wild Pixies',
                        'description'   => 'Wild Pixies',
                        'content'       => 'Wild Pixies'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25mustang',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Mustang Gold',
                        'description'   => 'Mustang Gold',
                        'content'       => 'Mustang Gold'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Mustang Gold',
                        'description'   => 'Mustang Gold',
                        'content'       => 'Mustang Gold'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Mustang Gold',
                        'description'   => 'Mustang Gold',
                        'content'       => 'Mustang Gold'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20leprexmas',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Leprechaun Carol',
                        'description'   => 'Leprechaun Carol',
                        'content'       => 'Leprechaun Carol'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Leprechaun Carol',
                        'description'   => 'Leprechaun Carol',
                        'content'       => 'Leprechaun Carol'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Leprechaun Carol',
                        'description'   => 'Leprechaun Carol',
                        'content'       => 'Leprechaun Carol'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs5trdragons',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Triple Dragons',
                        'description'   => 'Triple Dragons',
                        'content'       => 'Triple Dragons'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Triple Dragons',
                        'description'   => 'Triple Dragons',
                        'content'       => 'Triple Dragons'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Triple Dragons',
                        'description'   => 'Triple Dragons',
                        'content'       => 'Triple Dragons'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20vegasmagic',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Vegas Magic',
                        'description'   => 'Vegas Magic',
                        'content'       => 'Vegas Magic'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Vegas Magic',
                        'description'   => 'Vegas Magic',
                        'content'       => 'Vegas Magic'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Vegas Magic',
                        'description'   => 'Vegas Magic',
                        'content'       => 'Vegas Magic'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs9chen',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Master Chen\'s Fortune',
                        'description'   => 'Master Chen\'s Fortune',
                        'content'       => 'Master Chen\'s Fortune'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Master Chen\'s Fortune',
                        'description'   => 'Master Chen\'s Fortune',
                        'content'       => 'Master Chen\'s Fortune'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Master Chen\'s Fortune',
                        'description'   => 'Master Chen\'s Fortune',
                        'content'       => 'Master Chen\'s Fortune'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20leprechaun',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Leprechaun Song',
                        'description'   => 'Leprechaun Song',
                        'content'       => 'Leprechaun Song'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Leprechaun Song',
                        'description'   => 'Leprechaun Song',
                        'content'       => 'Leprechaun Song'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Leprechaun Song',
                        'description'   => 'Leprechaun Song',
                        'content'       => 'Leprechaun Song'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25peking',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Peking Luck',
                        'description'   => 'Peking Luck',
                        'content'       => 'Peking Luck'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Peking Luck',
                        'description'   => 'Peking Luck',
                        'content'       => 'Peking Luck'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Peking Luck',
                        'description'   => 'Peking Luck',
                        'content'       => 'Peking Luck'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs1024butterfly',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Jade Butterfly',
                        'description'   => 'Jade Butterfly',
                        'content'       => 'Jade Butterfly'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Jade Butterfly',
                        'description'   => 'Jade Butterfly',
                        'content'       => 'Jade Butterfly'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Jade Butterfly',
                        'description'   => 'Jade Butterfly',
                        'content'       => 'Jade Butterfly'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs10madame',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Madame Destiny',
                        'description'   => 'Madame Destiny',
                        'content'       => 'Madame Destiny'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Madame Destiny',
                        'description'   => 'Madame Destiny',
                        'content'       => 'Madame Destiny'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Madame Destiny',
                        'description'   => 'Madame Destiny',
                        'content'       => 'Madame Destiny'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25asgard',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Asgard',
                        'description'   => 'Asgard',
                        'content'       => 'Asgard'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Asgard',
                        'description'   => 'Asgard',
                        'content'       => 'Asgard'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Asgard',
                        'description'   => 'Asgard',
                        'content'       => 'Asgard'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs243lions',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => '5 Lions',
                        'description'   => '5 Lions',
                        'content'       => '5 Lions'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => '5 Lions',
                        'description'   => '5 Lions',
                        'content'       => '5 Lions'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => '5 Lions',
                        'description'   => '5 Lions',
                        'content'       => '5 Lions'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25champ',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'The Champions',
                        'description'   => 'The Champions',
                        'content'       => 'The Champions'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'The Champions',
                        'description'   => 'The Champions',
                        'content'       => 'The Champions'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'The Champions',
                        'description'   => 'The Champions',
                        'content'       => 'The Champions'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'scwolfgoldai',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Wolf Gold 1,000,000',
                        'description'   => 'Wolf Gold 1,000,000',
                        'content'       => 'Wolf Gold 1,000,000'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Wolf Gold 1,000,000',
                        'description'   => 'Wolf Gold 1,000,000',
                        'content'       => 'Wolf Gold 1,000,000'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Wolf Gold 1,000,000',
                        'description'   => 'Wolf Gold 1,000,000',
                        'content'       => 'Wolf Gold 1,000,000'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'scsafariai',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Hot Safari 75,000',
                        'description'   => 'Hot Safari 75,000',
                        'content'       => 'Hot Safari 75,000'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Hot Safari 75,000',
                        'description'   => 'Hot Safari 75,000',
                        'content'       => 'Hot Safari 75,000'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Hot Safari 75,000',
                        'description'   => 'Hot Safari 75,000',
                        'content'       => 'Hot Safari 75,000'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'scqogai',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Queen of Gold 100,000',
                        'description'   => 'Queen of Gold 100,000',
                        'content'       => 'Queen of Gold 100,000'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Queen of Gold 100,000',
                        'description'   => 'Queen of Gold 100,000',
                        'content'       => 'Queen of Gold 100,000'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Queen of Gold 100,000',
                        'description'   => 'Queen of Gold 100,000',
                        'content'       => 'Queen of Gold 100,000'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'scpandai',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Panda Gold 50,000',
                        'description'   => 'Panda Gold 50,000',
                        'content'       => 'Panda Gold 50,000'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Panda Gold 50,000',
                        'description'   => 'Panda Gold 50,000',
                        'content'       => 'Panda Gold 50,000'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Panda Gold 50,000',
                        'description'   => 'Panda Gold 50,000',
                        'content'       => 'Panda Gold 50,000'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'scgoldrushai',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Gold Rush 500,000',
                        'description'   => 'Gold Rush 500,000',
                        'content'       => 'Gold Rush 500,000'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Gold Rush 500,000',
                        'description'   => 'Gold Rush 500,000',
                        'content'       => 'Gold Rush 500,000'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Gold Rush 500,000',
                        'description'   => 'Gold Rush 500,000',
                        'content'       => 'Gold Rush 500,000'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'scdiamondai',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Diamond Strike 250,000',
                        'description'   => 'Diamond Strike 250,000',
                        'content'       => 'Diamond Strike 250,000'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Diamond Strike 250,000',
                        'description'   => 'Diamond Strike 250,000',
                        'content'       => 'Diamond Strike 250,000'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Diamond Strike 250,000',
                        'description'   => 'Diamond Strike 250,000',
                        'content'       => 'Diamond Strike 250,000'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'sc7piggiesai',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => '7 Piggies 25,000',
                        'description'   => '7 Piggies 25,000',
                        'content'       => '7 Piggies 25,000'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => '7 Piggies 25,000',
                        'description'   => '7 Piggies 25,000',
                        'content'       => '7 Piggies 25,000'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => '7 Piggies 25,000',
                        'description'   => '7 Piggies 25,000',
                        'content'       => '7 Piggies 25,000'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs5joker',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Joker\'s Jewels',
                        'description'   => 'Joker\'s Jewels',
                        'content'       => 'Joker\'s Jewels'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Joker\'s Jewels',
                        'description'   => 'Joker\'s Jewels',
                        'content'       => 'Joker\'s Jewels'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Joker\'s Jewels',
                        'description'   => 'Joker\'s Jewels',
                        'content'       => 'Joker\'s Jewels'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs7fire88',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Fire 88',
                        'description'   => 'Fire 88',
                        'content'       => 'Fire 88'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Fire 88',
                        'description'   => 'Fire 88',
                        'content'       => 'Fire 88'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Fire 88',
                        'description'   => 'Fire 88',
                        'content'       => 'Fire 88'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs15fairytale',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Fairytale Fortune',
                        'description'   => 'Fairytale Fortune',
                        'content'       => 'Fairytale Fortune'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Fairytale Fortune',
                        'description'   => 'Fairytale Fortune',
                        'content'       => 'Fairytale Fortune'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Fairytale Fortune',
                        'description'   => 'Fairytale Fortune',
                        'content'       => 'Fairytale Fortune'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs10egypt',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Ancient Egypt',
                        'description'   => 'Ancient Egypt',
                        'content'       => 'Ancient Egypt'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Ancient Egypt',
                        'description'   => 'Ancient Egypt',
                        'content'       => 'Ancient Egypt'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Ancient Egypt',
                        'description'   => 'Ancient Egypt',
                        'content'       => 'Ancient Egypt'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25newyear',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Lucky New Year',
                        'description'   => 'Lucky New Year',
                        'content'       => 'Lucky New Year'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Lucky New Year',
                        'description'   => 'Lucky New Year',
                        'content'       => 'Lucky New Year'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Lucky New Year',
                        'description'   => 'Lucky New Year',
                        'content'       => 'Lucky New Year'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs1tigers',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Triple Tigers',
                        'description'   => 'Triple Tigers',
                        'content'       => 'Triple Tigers'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Triple Tigers',
                        'description'   => 'Triple Tigers',
                        'content'       => 'Triple Tigers'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Triple Tigers',
                        'description'   => 'Triple Tigers',
                        'content'       => 'Triple Tigers'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25chilli',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Chilli Heat',
                        'description'   => 'Chilli Heat',
                        'content'       => 'Chilli Heat'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Chilli Heat',
                        'description'   => 'Chilli Heat',
                        'content'       => 'Chilli Heat'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Chilli Heat',
                        'description'   => 'Chilli Heat',
                        'content'       => 'Chilli Heat'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'scwolfgold',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Wolf Gold 1 Million',
                        'description'   => 'Wolf Gold 1 Million',
                        'content'       => 'Wolf Gold 1 Million'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Wolf Gold 1 Million',
                        'description'   => 'Wolf Gold 1 Million',
                        'content'       => 'Wolf Gold 1 Million'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Wolf Gold 1 Million',
                        'description'   => 'Wolf Gold 1 Million',
                        'content'       => 'Wolf Gold 1 Million'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'scqog',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Queen of Gold 100,000',
                        'description'   => 'Queen of Gold 100,000',
                        'content'       => 'Queen of Gold 100,000'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Queen of Gold 100,000',
                        'description'   => 'Queen of Gold 100,000',
                        'content'       => 'Queen of Gold 100,000'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Queen of Gold 100,000',
                        'description'   => 'Queen of Gold 100,000',
                        'content'       => 'Queen of Gold 100,000'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'scpanda',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Panda Gold 10,000',
                        'description'   => 'Panda Gold 10,000',
                        'content'       => 'Panda Gold 10,000'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Panda Gold 10,000',
                        'description'   => 'Panda Gold 10,000',
                        'content'       => 'Panda Gold 10,000'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Panda Gold 10,000',
                        'description'   => 'Panda Gold 10,000',
                        'content'       => 'Panda Gold 10,000'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'scsafari',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Hot Safari 50,000',
                        'description'   => 'Hot Safari 50,000',
                        'content'       => 'Hot Safari 50,000'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Hot Safari 50,000',
                        'description'   => 'Hot Safari 50,000',
                        'content'       => 'Hot Safari 50,000'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Hot Safari 50,000',
                        'description'   => 'Hot Safari 50,000',
                        'content'       => 'Hot Safari 50,000'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'scgoldrush',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Gold Rush 250,000',
                        'description'   => 'Gold Rush 250,000',
                        'content'       => 'Gold Rush 250,000'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Gold Rush 250,000',
                        'description'   => 'Gold Rush 250,000',
                        'content'       => 'Gold Rush 250,000'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Gold Rush 250,000',
                        'description'   => 'Gold Rush 250,000',
                        'content'       => 'Gold Rush 250,000'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'scdiamond',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Diamond Strike 100,000',
                        'description'   => 'Diamond Strike 100,000',
                        'content'       => 'Diamond Strike 100,000'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Diamond Strike 100,000',
                        'description'   => 'Diamond Strike 100,000',
                        'content'       => 'Diamond Strike 100,000'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Diamond Strike 100,000',
                        'description'   => 'Diamond Strike 100,000',
                        'content'       => 'Diamond Strike 100,000'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'sc7piggies',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => '7 Piggies 5,000',
                        'description'   => '7 Piggies 5,000',
                        'content'       => '7 Piggies 5,000'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => '7 Piggies 5,000',
                        'description'   => '7 Piggies 5,000',
                        'content'       => '7 Piggies 5,000'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => '7 Piggies 5,000',
                        'description'   => '7 Piggies 5,000',
                        'content'       => '7 Piggies 5,000'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20santa',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Santa',
                        'description'   => 'Santa',
                        'content'       => 'Santa'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Santa',
                        'description'   => 'Santa',
                        'content'       => 'Santa'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Santa',
                        'description'   => 'Santa',
                        'content'       => 'Santa'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25pandagold',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Panda\'s Fortune',
                        'description'   => 'Panda\'s Fortune',
                        'content'       => 'Panda\'s Fortune'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Panda\'s Fortune',
                        'description'   => 'Panda\'s Fortune',
                        'content'       => 'Panda\'s Fortune'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Panda\'s Fortune',
                        'description'   => 'Panda\'s Fortune',
                        'content'       => 'Panda\'s Fortune'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'cs5moneyroll',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Money Roll',
                        'description'   => 'Money Roll',
                        'content'       => 'Money Roll'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Money Roll',
                        'description'   => 'Money Roll',
                        'content'       => 'Money Roll'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Money Roll',
                        'description'   => 'Money Roll',
                        'content'       => 'Money Roll'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs7pigs',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => '7 Piggies',
                        'description'   => '7 Piggies',
                        'content'       => '7 Piggies'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => '7 Piggies',
                        'description'   => '7 Piggies',
                        'content'       => '7 Piggies'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => '7 Piggies',
                        'description'   => '7 Piggies',
                        'content'       => '7 Piggies'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs15diamond',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Diamond Strike',
                        'description'   => 'Diamond Strike',
                        'content'       => 'Diamond Strike'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Diamond Strike',
                        'description'   => 'Diamond Strike',
                        'content'       => 'Diamond Strike'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Diamond Strike',
                        'description'   => 'Diamond Strike',
                        'content'       => 'Diamond Strike'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25vegas',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Vegas Nights',
                        'description'   => 'Vegas Nights',
                        'content'       => 'Vegas Nights'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Vegas Nights',
                        'description'   => 'Vegas Nights',
                        'content'       => 'Vegas Nights'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Vegas Nights',
                        'description'   => 'Vegas Nights',
                        'content'       => 'Vegas Nights'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25wildspells',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Wild Spells',
                        'description'   => 'Wild Spells',
                        'content'       => 'Wild Spells'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Wild Spells',
                        'description'   => 'Wild Spells',
                        'content'       => 'Wild Spells'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Wild Spells',
                        'description'   => 'Wild Spells',
                        'content'       => 'Wild Spells'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25gladiator',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Wild Gladiator',
                        'description'   => 'Wild Gladiator',
                        'content'       => 'Wild Gladiator'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Wild Gladiator',
                        'description'   => 'Wild Gladiator',
                        'content'       => 'Wild Gladiator'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Wild Gladiator',
                        'description'   => 'Wild Gladiator',
                        'content'       => 'Wild Gladiator'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs50pixie',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Pixie Wings',
                        'description'   => 'Pixie Wings',
                        'content'       => 'Pixie Wings'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Pixie Wings',
                        'description'   => 'Pixie Wings',
                        'content'       => 'Pixie Wings'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Pixie Wings',
                        'description'   => 'Pixie Wings',
                        'content'       => 'Pixie Wings'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs4096jurassic',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Jurassic Giants',
                        'description'   => 'Jurassic Giants',
                        'content'       => 'Jurassic Giants'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Jurassic Giants',
                        'description'   => 'Jurassic Giants',
                        'content'       => 'Jurassic Giants'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Jurassic Giants',
                        'description'   => 'Jurassic Giants',
                        'content'       => 'Jurassic Giants'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25kingdomsnojp',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => '3 Kingdoms - Battle of Red Cliffs',
                        'description'   => '3 Kingdoms - Battle of Red Cliffs',
                        'content'       => '3 Kingdoms - Battle of Red Cliffs'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => '3 Kingdoms - Battle of Red Cliffs',
                        'description'   => '3 Kingdoms - Battle of Red Cliffs',
                        'content'       => '3 Kingdoms - Battle of Red Cliffs'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => '3 Kingdoms - Battle of Red Cliffs',
                        'description'   => '3 Kingdoms - Battle of Red Cliffs',
                        'content'       => '3 Kingdoms - Battle of Red Cliffs'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20eightdragons',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => '8 Dragons',
                        'description'   => '8 Dragons',
                        'content'       => '8 Dragons'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => '8 Dragons',
                        'description'   => '8 Dragons',
                        'content'       => '8 Dragons'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => '8 Dragons',
                        'description'   => '8 Dragons',
                        'content'       => '8 Dragons'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs3train',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Gold Train',
                        'description'   => 'Gold Train',
                        'content'       => 'Gold Train'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Gold Train',
                        'description'   => 'Gold Train',
                        'content'       => 'Gold Train'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Gold Train',
                        'description'   => 'Gold Train',
                        'content'       => 'Gold Train'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25kingdoms',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => '3 Kingdoms - Battle of Red Cliffs',
                        'description'   => '3 Kingdoms - Battle of Red Cliffs',
                        'content'       => '3 Kingdoms - Battle of Red Cliffs'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => '3 Kingdoms - Battle of Red Cliffs',
                        'description'   => '3 Kingdoms - Battle of Red Cliffs',
                        'content'       => '3 Kingdoms - Battle of Red Cliffs'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => '3 Kingdoms - Battle of Red Cliffs',
                        'description'   => '3 Kingdoms - Battle of Red Cliffs',
                        'content'       => '3 Kingdoms - Battle of Red Cliffs'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25pantherqueen',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Panther Queen',
                        'description'   => 'Panther Queen',
                        'content'       => 'Panther Queen'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Panther Queen',
                        'description'   => 'Panther Queen',
                        'content'       => 'Panther Queen'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Panther Queen',
                        'description'   => 'Panther Queen',
                        'content'       => 'Panther Queen'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs1024atlantis',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Queen of Atlantis',
                        'description'   => 'Queen of Atlantis',
                        'content'       => 'Queen of Atlantis'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Queen of Atlantis',
                        'description'   => 'Queen of Atlantis',
                        'content'       => 'Queen of Atlantis'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Queen of Atlantis',
                        'description'   => 'Queen of Atlantis',
                        'content'       => 'Queen of Atlantis'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'cs3irishcharms',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Irish Charms',
                        'description'   => 'Irish Charms',
                        'content'       => 'Irish Charms'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Irish Charms',
                        'description'   => 'Irish Charms',
                        'content'       => 'Irish Charms'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Irish Charms',
                        'description'   => 'Irish Charms',
                        'content'       => 'Irish Charms'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25queenofgold',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Queen of Gold',
                        'description'   => 'Queen of Gold',
                        'content'       => 'Queen of Gold'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Queen of Gold',
                        'description'   => 'Queen of Gold',
                        'content'       => 'Queen of Gold'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Queen of Gold',
                        'description'   => 'Queen of Gold',
                        'content'       => 'Queen of Gold'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'cs5triple8gold',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => '888 Gold',
                        'description'   => '888 Gold',
                        'content'       => '888 Gold'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => '888 Gold',
                        'description'   => '888 Gold',
                        'content'       => '888 Gold'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => '888 Gold',
                        'description'   => '888 Gold',
                        'content'       => '888 Gold'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs50hercules',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Hercules Son of Zeus',
                        'description'   => 'Hercules Son of Zeus',
                        'content'       => 'Hercules Son of Zeus'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Hercules Son of Zeus',
                        'description'   => 'Hercules Son of Zeus',
                        'content'       => 'Hercules Son of Zeus'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Hercules Son of Zeus',
                        'description'   => 'Hercules Son of Zeus',
                        'content'       => 'Hercules Son of Zeus'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25dragonkingdom',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Dragon Kingdom',
                        'description'   => 'Dragon Kingdom',
                        'content'       => 'Dragon Kingdom'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Dragon Kingdom',
                        'description'   => 'Dragon Kingdom',
                        'content'       => 'Dragon Kingdom'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Dragon Kingdom',
                        'description'   => 'Dragon Kingdom',
                        'content'       => 'Dragon Kingdom'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs50aladdin',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => '3 Genie Wishes',
                        'description'   => '3 Genie Wishes',
                        'content'       => '3 Genie Wishes'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => '3 Genie Wishes',
                        'description'   => '3 Genie Wishes',
                        'content'       => '3 Genie Wishes'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => '3 Genie Wishes',
                        'description'   => '3 Genie Wishes',
                        'content'       => '3 Genie Wishes'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs30catz',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'The Catfather Part II',
                        'description'   => 'The Catfather Part II',
                        'content'       => 'The Catfather Part II'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'The Catfather Part II',
                        'description'   => 'The Catfather Part II',
                        'content'       => 'The Catfather Part II'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'The Catfather Part II',
                        'description'   => 'The Catfather Part II',
                        'content'       => 'The Catfather Part II'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25journey',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Journey to the West',
                        'description'   => 'Journey to the West',
                        'content'       => 'Journey to the West'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Journey to the West',
                        'description'   => 'Journey to the West',
                        'content'       => 'Journey to the West'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Journey to the West',
                        'description'   => 'Journey to the West',
                        'content'       => 'Journey to the West'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs40beowulf',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Beowulf',
                        'description'   => 'Beowulf',
                        'content'       => 'Beowulf'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Beowulf',
                        'description'   => 'Beowulf',
                        'content'       => 'Beowulf'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Beowulf',
                        'description'   => 'Beowulf',
                        'content'       => 'Beowulf'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs50chinesecharms',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Lucky Dragons',
                        'description'   => 'Lucky Dragons',
                        'content'       => 'Lucky Dragons'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Lucky Dragons',
                        'description'   => 'Lucky Dragons',
                        'content'       => 'Lucky Dragons'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Lucky Dragons',
                        'description'   => 'Lucky Dragons',
                        'content'       => 'Lucky Dragons'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25dwarves_new',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Dwarven Gold Deluxe',
                        'description'   => 'Dwarven Gold Deluxe',
                        'content'       => 'Dwarven Gold Deluxe'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Dwarven Gold Deluxe',
                        'description'   => 'Dwarven Gold Deluxe',
                        'content'       => 'Dwarven Gold Deluxe'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Dwarven Gold Deluxe',
                        'description'   => 'Dwarven Gold Deluxe',
                        'content'       => 'Dwarven Gold Deluxe'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25romeoandjuliet',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Romeo and Juliet',
                        'description'   => 'Romeo and Juliet',
                        'content'       => 'Romeo and Juliet'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Romeo and Juliet',
                        'description'   => 'Romeo and Juliet',
                        'content'       => 'Romeo and Juliet'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Romeo and Juliet',
                        'description'   => 'Romeo and Juliet',
                        'content'       => 'Romeo and Juliet'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs9hockey',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Hockey League Wild Match',
                        'description'   => 'Hockey League Wild Match',
                        'content'       => 'Hockey League Wild Match'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Hockey League Wild Match',
                        'description'   => 'Hockey League Wild Match',
                        'content'       => 'Hockey League Wild Match'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Hockey League Wild Match',
                        'description'   => 'Hockey League Wild Match',
                        'content'       => 'Hockey League Wild Match'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25safari',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Hot Safari',
                        'description'   => 'Hot Safari',
                        'content'       => 'Hot Safari'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Hot Safari',
                        'description'   => 'Hot Safari',
                        'content'       => 'Hot Safari'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Hot Safari',
                        'description'   => 'Hot Safari',
                        'content'       => 'Hot Safari'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20godiva',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Lady Godiva',
                        'description'   => 'Lady Godiva',
                        'content'       => 'Lady Godiva'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Lady Godiva',
                        'description'   => 'Lady Godiva',
                        'content'       => 'Lady Godiva'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Lady Godiva',
                        'description'   => 'Lady Godiva',
                        'content'       => 'Lady Godiva'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs9catz',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'The Catfather',
                        'description'   => 'The Catfather',
                        'content'       => 'The Catfather'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'The Catfather',
                        'description'   => 'The Catfather',
                        'content'       => 'The Catfather'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'The Catfather',
                        'description'   => 'The Catfather',
                        'content'       => 'The Catfather'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs50kingkong',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Mighty Kong',
                        'description'   => 'Mighty Kong',
                        'content'       => 'Mighty Kong'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Mighty Kong',
                        'description'   => 'Mighty Kong',
                        'content'       => 'Mighty Kong'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Mighty Kong',
                        'description'   => 'Mighty Kong',
                        'content'       => 'Mighty Kong'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs15ktv',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'KTV',
                        'description'   => 'KTV',
                        'content'       => 'KTV'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'KTV',
                        'description'   => 'KTV',
                        'content'       => 'KTV'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'KTV',
                        'description'   => 'KTV',
                        'content'       => 'KTV'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs243crystalcave',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Magic Crystals',
                        'description'   => 'Magic Crystals',
                        'content'       => 'Magic Crystals'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Magic Crystals',
                        'description'   => 'Magic Crystals',
                        'content'       => 'Magic Crystals'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Magic Crystals',
                        'description'   => 'Magic Crystals',
                        'content'       => 'Magic Crystals'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20hockey',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Hockey League',
                        'description'   => 'Hockey League',
                        'content'       => 'Hockey League'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Hockey League',
                        'description'   => 'Hockey League',
                        'content'       => 'Hockey League'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Hockey League',
                        'description'   => 'Hockey League',
                        'content'       => 'Hockey League'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs50amt',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Aladdin\'s Treasure',
                        'description'   => 'Aladdin\'s Treasure',
                        'content'       => 'Aladdin\'s Treasure'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Aladdin\'s Treasure',
                        'description'   => 'Aladdin\'s Treasure',
                        'content'       => 'Aladdin\'s Treasure'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Aladdin\'s Treasure',
                        'description'   => 'Aladdin\'s Treasure',
                        'content'       => 'Aladdin\'s Treasure'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs13ladyofmoon',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Lady of the Moon',
                        'description'   => 'Lady of the Moon',
                        'content'       => 'Lady of the Moon'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Lady of the Moon',
                        'description'   => 'Lady of the Moon',
                        'content'       => 'Lady of the Moon'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Lady of the Moon',
                        'description'   => 'Lady of the Moon',
                        'content'       => 'Lady of the Moon'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs7monkeys',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => '7 Monkeys',
                        'description'   => '7 Monkeys',
                        'content'       => '7 Monkeys'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => '7 Monkeys',
                        'description'   => '7 Monkeys',
                        'content'       => '7 Monkeys'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => '7 Monkeys',
                        'description'   => '7 Monkeys',
                        'content'       => '7 Monkeys'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20cw',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Sugar Rush Winter',
                        'description'   => 'Sugar Rush Winter',
                        'content'       => 'Sugar Rush Winter'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Sugar Rush Winter',
                        'description'   => 'Sugar Rush Winter',
                        'content'       => 'Sugar Rush Winter'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Sugar Rush Winter',
                        'description'   => 'Sugar Rush Winter',
                        'content'       => 'Sugar Rush Winter'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20egypt',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Tales of Egypt',
                        'description'   => 'Tales of Egypt',
                        'content'       => 'Tales of Egypt'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Tales of Egypt',
                        'description'   => 'Tales of Egypt',
                        'content'       => 'Tales of Egypt'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Tales of Egypt',
                        'description'   => 'Tales of Egypt',
                        'content'       => 'Tales of Egypt'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs4096bufking',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Buffalo King',
                        'description'   => 'Buffalo King',
                        'content'       => 'Buffalo King'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Buffalo King',
                        'description'   => 'Buffalo King',
                        'content'       => 'Buffalo King'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Buffalo King',
                        'description'   => 'Buffalo King',
                        'content'       => 'Buffalo King'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25dwarves',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Dwarven Gold',
                        'description'   => 'Dwarven Gold',
                        'content'       => 'Dwarven Gold'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Dwarven Gold',
                        'description'   => 'Dwarven Gold',
                        'content'       => 'Dwarven Gold'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Dwarven Gold',
                        'description'   => 'Dwarven Gold',
                        'content'       => 'Dwarven Gold'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs243fortseren',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Greek Gods',
                        'description'   => 'Greek Gods',
                        'content'       => 'Greek Gods'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Greek Gods',
                        'description'   => 'Greek Gods',
                        'content'       => 'Greek Gods'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Greek Gods',
                        'description'   => 'Greek Gods',
                        'content'       => 'Greek Gods'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs8magicjourn',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Magic Journey',
                        'description'   => 'Magic Journey',
                        'content'       => 'Magic Journey'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Magic Journey',
                        'description'   => 'Magic Journey',
                        'content'       => 'Magic Journey'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Magic Journey',
                        'description'   => 'Magic Journey',
                        'content'       => 'Magic Journey'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20kraken',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Release the Kraken',
                        'description'   => 'Release the Kraken',
                        'content'       => 'Release the Kraken'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Release the Kraken',
                        'description'   => 'Release the Kraken',
                        'content'       => 'Release the Kraken'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Release the Kraken',
                        'description'   => 'Release the Kraken',
                        'content'       => 'Release the Kraken'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20sbxmas',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Sweet Bonanza Xmas',
                        'description'   => 'Sweet Bonanza Xmas',
                        'content'       => 'Sweet Bonanza Xmas'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Sweet Bonanza Xmas',
                        'description'   => 'Sweet Bonanza Xmas',
                        'content'       => 'Sweet Bonanza Xmas'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Sweet Bonanza Xmas',
                        'description'   => 'Sweet Bonanza Xmas',
                        'content'       => 'Sweet Bonanza Xmas'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20rome',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Glorious Rome',
                        'description'   => 'Glorious Rome',
                        'content'       => 'Glorious Rome'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Glorious Rome',
                        'description'   => 'Glorious Rome',
                        'content'       => 'Glorious Rome'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Glorious Rome',
                        'description'   => 'Glorious Rome',
                        'content'       => 'Glorious Rome'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20cms',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Sugar Rush Summer Time',
                        'description'   => 'Sugar Rush Summer Time',
                        'content'       => 'Sugar Rush Summer Time'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Sugar Rush Summer Time',
                        'description'   => 'Sugar Rush Summer Time',
                        'content'       => 'Sugar Rush Summer Time'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Sugar Rush Summer Time',
                        'description'   => 'Sugar Rush Summer Time',
                        'content'       => 'Sugar Rush Summer Time'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20cmv',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Sugar Rush Valentine\'s Day',
                        'description'   => 'Sugar Rush Valentine\'s Day',
                        'content'       => 'Sugar Rush Valentine\'s Day'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Sugar Rush Valentine\'s Day',
                        'description'   => 'Sugar Rush Valentine\'s Day',
                        'content'       => 'Sugar Rush Valentine\'s Day'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Sugar Rush Valentine\'s Day',
                        'description'   => 'Sugar Rush Valentine\'s Day',
                        'content'       => 'Sugar Rush Valentine\'s Day'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20cm',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Sugar Rush',
                        'description'   => 'Sugar Rush',
                        'content'       => 'Sugar Rush'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Sugar Rush',
                        'description'   => 'Sugar Rush',
                        'content'       => 'Sugar Rush'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Sugar Rush',
                        'description'   => 'Sugar Rush',
                        'content'       => 'Sugar Rush'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25sea',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Great Reef',
                        'description'   => 'Great Reef',
                        'content'       => 'Great Reef'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Great Reef',
                        'description'   => 'Great Reef',
                        'content'       => 'Great Reef'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Great Reef',
                        'description'   => 'Great Reef',
                        'content'       => 'Great Reef'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20bl',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Busy Bees',
                        'description'   => 'Busy Bees',
                        'content'       => 'Busy Bees'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Busy Bees',
                        'description'   => 'Busy Bees',
                        'content'       => 'Busy Bees'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Busy Bees',
                        'description'   => 'Busy Bees',
                        'content'       => 'Busy Bees'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs20gg',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Spooky Fortune',
                        'description'   => 'Spooky Fortune',
                        'content'       => 'Spooky Fortune'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Spooky Fortune',
                        'description'   => 'Spooky Fortune',
                        'content'       => 'Spooky Fortune'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Spooky Fortune',
                        'description'   => 'Spooky Fortune',
                        'content'       => 'Spooky Fortune'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'bca',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Baccarat',
                        'description'   => 'Baccarat',
                        'content'       => 'Baccarat'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Baccarat',
                        'description'   => 'Baccarat',
                        'content'       => 'Baccarat'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Baccarat',
                        'description'   => 'Baccarat',
                        'content'       => 'Baccarat'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'bjma',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Multihand Blackjack',
                        'description'   => 'Multihand Blackjack',
                        'content'       => 'Multihand Blackjack'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Multihand Blackjack',
                        'description'   => 'Multihand Blackjack',
                        'content'       => 'Multihand Blackjack'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Multihand Blackjack',
                        'description'   => 'Multihand Blackjack',
                        'content'       => 'Multihand Blackjack'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'kna',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Keno',
                        'description'   => 'Keno',
                        'content'       => 'Keno'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Keno',
                        'description'   => 'Keno',
                        'content'       => 'Keno'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Keno',
                        'description'   => 'Keno',
                        'content'       => 'Keno'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'cs3w',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Diamonds are Forever 3 Lines',
                        'description'   => 'Diamonds are Forever 3 Lines',
                        'content'       => 'Diamonds are Forever 3 Lines'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Diamonds are Forever 3 Lines',
                        'description'   => 'Diamonds are Forever 3 Lines',
                        'content'       => 'Diamonds are Forever 3 Lines'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Diamonds are Forever 3 Lines',
                        'description'   => 'Diamonds are Forever 3 Lines',
                        'content'       => 'Diamonds are Forever 3 Lines'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'rla',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Roulette',
                        'description'   => 'Roulette',
                        'content'       => 'Roulette'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Roulette',
                        'description'   => 'Roulette',
                        'content'       => 'Roulette'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Roulette',
                        'description'   => 'Roulette',
                        'content'       => 'Roulette'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vpa',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Jacks or Better',
                        'description'   => 'Jacks or Better',
                        'content'       => 'Jacks or Better'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Jacks or Better',
                        'description'   => 'Jacks or Better',
                        'content'       => 'Jacks or Better'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Jacks or Better',
                        'description'   => 'Jacks or Better',
                        'content'       => 'Jacks or Better'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs25h',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Fruity Blast',
                        'description'   => 'Fruity Blast',
                        'content'       => 'Fruity Blast'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Fruity Blast',
                        'description'   => 'Fruity Blast',
                        'content'       => 'Fruity Blast'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Fruity Blast',
                        'description'   => 'Fruity Blast',
                        'content'       => 'Fruity Blast'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs13g',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Devil\'s 13',
                        'description'   => 'Devil\'s 13',
                        'content'       => 'Devil\'s 13'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Devil\'s 13',
                        'description'   => 'Devil\'s 13',
                        'content'       => 'Devil\'s 13'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Devil\'s 13',
                        'description'   => 'Devil\'s 13',
                        'content'       => 'Devil\'s 13'
                    ],
                ],
                'devices'       => [1, 2],
            ],

			[
                'platform_code' => self::CODE,
                'product_code'  => 'PP_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'vs15b',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Crazy 7s',
                        'description'   => 'Crazy 7s',
                        'content'       => 'Crazy 7s'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Crazy 7s',
                        'description'   => 'Crazy 7s',
                        'content'       => 'Crazy 7s'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Crazy 7s',
                        'description'   => 'Crazy 7s',
                        'content'       => 'Crazy 7s'
                    ],
                ],
                'devices'       => [1, 2],
            ],

        ];


        foreach ($games as $game) {
            Game::query()->create($game);
        }
        #Game Platform Games End


        #Changing config
        $configs = [
            [
                'code'          => 'pp_last_timepoint',
                'name'          => 'PP pp_last_timepoint',
                'remark'        => 'PP pp_last_timepoint',
                'is_front_show' => false,
                'type'          => 'string',
                'value'         => '',
            ],
        ];
        ChangingConfig::insert($configs);


    }
}
