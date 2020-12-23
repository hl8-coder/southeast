<?php

use Illuminate\Database\Seeder;

class GamePlatformsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('game_platforms')->truncate();

        if (app()->isLocal()) {
            $gamePlatforms = [
                [
                    'id'                    => 1,
                    'name'                  => 'SA',
                    'code'                  => 'SA',
                    'request_url'           => 'http://sai-api.sa-apisvr.com/api/api.aspx',
                    'report_request_url'    => 'http://sai-api.sa-rpt.com/api/api.aspx',
                    'launcher_request_url'  => 'https://www.sai.slgaming.net/app.aspx',
                    'rsa_our_private_key'   => '',
                    'rsa_our_public_key'    => '',
                    'rsa_public_key'        => '',
                    'account'               => '{"encrypt_key":"g9G16nTs","secret_key":"1B03957B2DDF493EBE3BD56CB5BB9204","md5_key":"GgaIMaiNNtg","lobby":"A1208"}',
//                'account'               => '{"encrypt_key":"g9G16nTs","secret_key":"444BD2CB7A4C48B39ACEB56F1E67BAF8","md5_key":"GgaIMaiNNtg","lobby":"A1259"}',
                    'exchange_currencies'   => null,
                    'is_update_list'        => false,
                    'update_interval'       => 7,
                    'interval'              => 1, # 间隔时间
                    'delay'                 => 0, # 延迟时间
                    'offset'                => 10, # 向前偏移时间
                    'limit'                 => 1, # 每分钟拉取次数
                    'status'                => true,
                    'icon'                  => 'data:image/jpeg;base64,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',
                ],
                [
                    # RTG 延迟10分钟并偏移20分钟
                    'id'                    => 2,
                    'name'                  => 'RTG',
                    'code'                  => 'RTG',
                    'request_url'           => 'https://cms-pre.rtgintegrations.com/api',
                    'report_request_url'    => 'https://cms-pre.rtgintegrations.com/api',
                    'launcher_request_url'  => '',
                    'rsa_our_private_key'   => '',
                    'rsa_our_public_key'    => '',
                    'rsa_public_key'        => '',
                    'account'               => '{"username":"empiregemapi","password":"826ce3c1f7","agent_id":"5e4ea1dd-915e-474e-994e-9dcc6d08f3c3","id":"6c739417-e080-47aa-8a95-4d2fee54bd53","name":"Braavos"}',
                    'exchange_currencies'   => '{"VND":"USD"}',
                    'is_update_list'        => true,
                    'update_interval'       => 7,
                    'interval'              => 5, # 间隔时间
                    'delay'                 => 10, # 延迟时间
                    'offset'                => 20, # 向前偏移时间
                    'limit'                 => 1, # 每分钟拉取次数
                    'status'                => true,
                    'icon'                  => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAUCAYAAACEYr13AAAByklEQVQ4jW3Uy4uPYRQH8M+8KJdMzbjFEHukLKRElJ1kg1iyoiwoK2Ql/gU18weMrGVDKStlYSFJUXJbmJ/bYFzGpfOb8/LM+8ypt/d9zvV7zvk+78CvG8vhCC5gAaaxED08w04swh/Mx2tswQ+pCDmOjWbL+3Re09EPYhduxaFJZaOWqxittNzDSZwqEQxXbozjc6WdQfYSJ/CtyerLOk6jRfDdKgVfcy7bIniok+AjzhTni53gnxiI6njVJPwlafyOvfhSBNzBleI8lRuIZyoSjKThIbbifgWYc7ic38NZKBJNNtnLaWzGoyr0v5zHAUxgbbYw0eRhB1ZXIbMlyHMU85JwgeBtO4NDeIrtVdiMHMaDZGzLmUDQawoORCu35+DEQVzL715uYTBX2W9hReG8GJeK80pcL86f8j1UtrCqU/FYXqaQsY7tQ65vaSKZbolUSgRvSKf9HdtkEk3ezv5dGFHLJuyptPzO3ptE0E+wbg7H2Eqg6Mqb5EHMKmbQT7AvA9bnBYo9R5/PY0gJNSpG//F/2I0Xyca+4WYy8UnLrlxpwI21vUtdVAyqx1WOtsP+jxSxnrOJIFgWw4pkUb19Qv84L16gHIe/IXttgQKxIOYAAAAASUVORK5CYII=',
                ],
                [
                    'id'                    => 3,
                    'name'                  => 'EBET',
                    'code'                  => 'EBET',
                    'request_url'           => 'http://hl8vnd.ebet.im:8888/api',
                    'report_request_url'    => 'http://hl8vnd.ebet.im:8888/api',
                    'launcher_request_url'  => 'http://hl8vnd.drfs.live/h5/c878f8',
                    'rsa_our_private_key'   => '-----BEGIN PRIVATE KEY-----
MIIBVgIBADANBgkqhkiG9w0BAQEFAASCAUAwggE8AgEAAkEAhta2CJ/RMAkxChx6
2m9cnfzGf4n97A0GXDYz1/wNYcAiJvb7E8GKKCgbAS5cIQ5LmL2/tS1Q4q2hNkPb
osATewIDAQABAkAPirwcrl8sTELsyW+XsfJY+4Pdu4gbJz0ub8j2AkWAmLwCYjxX
r+THSSmWYAm+DXmKyxVw57QvTmk+/ETPelHRAiEAudzgG025QDxrC/QVKtYKccoT
jfykX10GKMXolCm0PXcCIQC5uLIhYsvh1zQdxlmlZRGOSvMdufy9gGo/Y8QjwxcL
HQIhAJe+LvHbuP0q1rLBqm54pbpVIzXvKDv7dMXhHouoqNDtAiEAhLKMxAH9Pu4u
1J9maiCevJacwr6i8RuRzp0QBaVdD5kCIQCDoSmL+LEQFAZtEjPh1MyT5WOBnx3b
otUqkwwE1ErTnA==
-----END PRIVATE KEY-----',
                    'rsa_our_public_key'    => '',
                    'rsa_public_key'        => '-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAIbWtgif0TAJMQocetpvXJ38xn+J/ewN
Blw2M9f8DWHAIib2+xPBiigoGwEuXCEOS5i9v7UtUOKtoTZD26LAE3sCAwEAAQ==
-----END PUBLIC KEY-----',
                    'account'               => '{"channel_id":"960","wallet_type":0}',
                    'exchange_currencies'   => null,
                    'is_update_list'        => false,
                    'update_interval'       => 7,
                    'interval'              => 1, # 间隔时间
                    'delay'                 => 0, # 延迟时间
                    'offset'                => 10, # 向前偏移时间
                    'limit'                 => 1, # 每分钟拉取次数
                    'status'                => true,
                    'icon'                  => '',
                ],
                [
                    'id'                    => 4,
                    'name'                  => 'SP',
                    'code'                  => 'SP',
                    'request_url'           => 'http://api.sp-portal.com/api/api.aspx',
                    'report_request_url'    => 'http://api.sp-portal.com/api/api.aspx',
                    'launcher_request_url'  => '',
                    'rsa_our_private_key'   => '',
                    'rsa_our_public_key'    => '',
                    'rsa_public_key'        => '',
                    'account'               => '{"encrypt_key":"g9G16nTs","secret_key":"83E1AB55CBD04CD3BC45A1FFF0D4C45A","md5_key":"GgaIMaiNNtg","lobby":"S057"}',
                    'exchange_currencies'   => null,
                    'is_update_list'        => false,
                    'update_interval'       => 7,
                    'interval'              => 1, # 间隔时间
                    'delay'                 => 0, # 延迟时间
                    'offset'                => 10, # 向前偏移时间
                    'limit'                 => 1, # 每分钟拉取次数
                    'status'                => true,
                    'icon'                  => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAAUCAYAAACAl21KAAABrklEQVQ4jZXUT4iOURTH8Y/X+LegjD+xQrGzYjtEkR1lY+NPRDYTEQsrMaUkbCyYGpGdskAKC/Ivi8FOSqFkQWKBBWomnencup73neH91dPzPOee873nnnO6k0Zuzd2D47iT77e60MBw/5hzC4NYgB14jRPdgIoC9Lv6n4wjCdzZ5j2BAvQrl39WbstwEbfHD23PqOgLNuJGZdvQFjGOalAvbmJT/r+vQCtwHWcwoxOqBo1iCqZjOxbhLobwPLM9gDfYOhEoCj01a3UFu/EJuxoxC3P9HtYXY8zRN8zMoBiDVbiMJQ3AJSzGmoY9oAfrjOL7Ph42INewMsdhbW70rFrfhgc9lWFeY7eXOelXGxm8a4xKaH5Lu2JA92F5A7I0u/oBfZX9aWTZkwUuOodT2fqiOTiMQ9mQopj+kwPD/TG4AjQNT7CuQ8rRsbOYVdlG88jHasdWFrIvO1K0OQs61IAM5hH/ghTQC1zAqwSerzpV9BirsTeL3aY42lfMxggeNRw+Yn+HznUEBSRUF/I7Tufz41+QAmoqdo9r7/P/AIqiRkfzCom7J6Z2S7cQ+ANVcFiY3mK/AQAAAABJRU5ErkJggg==',
                ],
//            [
//                'name'                  => 'SmartSoft',
//                'code'                  => 'SmartSoft',
//                'request_url'           => 'http://test.ssgportal.com:8099/GamblingService/GamblingWebService.asmx?WSDL',
//                'report_request_url'    => 'http://test.ssgportal.com:8099/GamblingService/GamblingWebService.asmx?WSDL',
//                'launcher_request_url'  => '',
//                'rsa_our_private_key'   => '',
//                'rsa_our_public_key'    => '',
//                'rsa_public_key'        => '',
//                'account'               => '{"client_external_key":"1001","portal_name":"TestPortal","hash_value":"4f306f1b13bb49759aaced44a44d20d7"}',
//                'exchange_currencies'   => null,
//                'is_update_list'        => false,
//                'update_interval'       => 7,
//                'interval'              => 1, # 间隔时间
//                'delay'                 => 0, # 延迟时间
//                'offset'                => 1, # 向前偏移时间
//                'limit'                 => 1, # 每分钟拉取次数
//                'status'                => true,
//                'icon'                  => '',
//            ],
                [
                    'id'                    => 5,
                    'name'                  => 'ISB',
                    'code'                  => 'ISB',
                    'request_url'           => 'https://gap-player-sg-stage.isoftbet.com/v1/546?',
                    'report_request_url'    => 'https://gap-player-sg-stage.isoftbet.com/v1/546?',
                    'launcher_request_url'  => 'https://stage-game-launcher-sg.isoftbet.com/546',
                    'rsa_our_private_key'   => '',
                    'rsa_our_public_key'    => '',
                    'rsa_public_key'        => '',
                    'account'               => '{"lid":"546","secret_key":"gRrjj8kYDZB9PupJu637QFzbcj81rzGE"}',
                    'exchange_currencies'   => null,
                    'is_update_list'        => false,
                    'update_interval'       => 7,
                    'interval'              => 1, # 间隔时间
                    'delay'                 => 5, # 延迟时间
                    'offset'                => 1, # 向前偏移时间
                    'limit'                 => 10, # 每分钟拉取次数
                    'status'                => true,
                    'icon'                  => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAcAAAAUCAYAAABBECfmAAAAzUlEQVQokdXRPUpEMRSG4ecOYivKqGDnCvy9dtYW2liKW8gGLC5cSGU/hbgBFyCDvaXxZxdqYaFgJYiSIXdmQF2AX3OSvLyHk6Rq21ZTDxZxgi084DSm8FR9DvuruEHfJG/Y6OGsgMOYQoUDzOE8mx94jyksdF5TD56xnM1bzDf1YLuAtQyQsrmJuyI9YqWsd3oxhXus4xJfGKKOKaTRVf7KzNQQx9jHRUwhd5EH6rKHI+x2B9PwpdTX3+CP/F+4VOr468bPhyvM4nq0wzd2mDJjd5CbwgAAAABJRU5ErkJggg==',
                ],
                [
                    'id'                    => 6,
                    'name'                  => 'N2',
                    'code'                  => 'N2',
                    'request_url'           => 'https://stgmerchanttalk.azuritebox.com',
                    'report_request_url'    => 'https://stgmerchanttalk.azuritebox.com',
                    'launcher_request_url'  => '',
                    'rsa_our_private_key'   => '',
                    'rsa_our_public_key'    => '',
                    'rsa_public_key'        => '',
                    'account'               => '{"merchant_passcode":"2DFC3663F234E0C93C861EB5BB4CE45D","vendor_id":"393","merchant_code":"EPG","mobile_domain":"https://stgepgm.n2ea.com","desktop_domain":"https://stgepg.n2ea.com"}',
                    'exchange_currencies'   => null,
                    'is_update_list'        => false,
                    'update_interval'       => 7,
                    'interval'              => 1, # 间隔时间
                    'delay'                 => 15, # 延迟时间
                    'offset'                => 10, # 向前偏移时间
                    'limit'                 => 1, # 每分钟拉取次数
                    'status'                => true,
                    'icon'                  => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAASCAYAAABfJS4tAAABW0lEQVQ4jdXUPUtcURAG4GdVRAVT+JHCoJAEAkKCFgoSrRQUBZtAAqlikYQUQlKkSGMltlb+AUG0sBC/ECQI2gSy/gHBQhDL2BmyxaIcGIvsTdxdtvKFy52588575s6Zc3LF3Q414AteoBh+HS6x0JChVodHeI7fuI4F3mGg1orN52f/8ucGl57itFbhJnzEQxRwhTH01NKKlLuBCZyE34xzvC4n3BLkVMmfklg/RvBkPj97VpqYWjGNGSxjC+N4gz48DvEk+gMrWIvcAfzENn5FxWkDG3GcnJd4hV58wxAusI/1IA5jKp5RfEgbhEU8Q2eIij9saoi5S+jCET4jn2kKb7GK99jBJr6WTsUt0kDnwk5Jn/4jKlqwHvZkJvoP4WrwPbit1QjXZ6JZFOJLMRO5Q7gS3C5+XY5brXDFuJ/CbWG3V8B/EO+yN1c6IIfoxl4FwsdxrA/uZOEGazZCwkhd0d4AAAAASUVORK5CYII=',
                ],
                [
                    'id'                    => 7,
                    'name'                  => 'IBC',
                    'code'                  => 'IBC',
                    'request_url'           => 'http://tsa.thaihl8.com/api',
                    'report_request_url'    => 'http://tsa.thaihl8.com/api',
                    'launcher_request_url'  => 'http://sbtest.thaihl8.com/Deposit_ProcessLogin.aspx',
                    'rsa_our_private_key'   => '',
                    'rsa_our_public_key'    => '',
                    'rsa_public_key'        => '',
                    'account'               => '{"operator_id":"HeleThai","vendor_id":"tux9qv79ps","secret_key":"ICJ72X6DRL94Q3J00S3R","max_transfer":20000,"min_transfer":1,"skincolor":"bl003"}',
                    'exchange_currencies'   => null,
                    'is_update_list'        => false,
                    'update_interval'       => 7,
                    'interval'              => 3, # 间隔时间
                    'delay'                 => 0, # 延迟时间
                    'offset'                => 1, # 向前偏移时间
                    'limit'                 => 1, # 每分钟拉取次数
                    'status'                => true,
                    'icon'                  => '',
                ],
                [
                    'id'                    => 8,
                    'name'                  => 'S128',
                    'code'                  => 'S128',
                    'request_url'           => 'https://api2288.cfb2.net',
                    'report_request_url'    => 'https://api2288.cfb2.net',
                    'launcher_request_url'  => 'http://cs.cfb2.net',
                    'rsa_our_private_key'   => '',
                    'rsa_our_public_key'    => '',
                    'rsa_public_key'        => '',
                    'account'               => '{"agent_code":"VC680128","login_id":"VC680128", "password":"AAAA1111", "api_key":"17E71701AC8C4B3B87E043ABB65E9465"}',
                    'exchange_currencies'   => null,
                    'is_update_list'        => false,
                    'update_interval'       => 1,
                    'interval'              => 2, # 间隔时间
                    'delay'                 => 10, # 延迟时间
                    'offset'                => 20, # 向前偏移时间
                    'limit'                 => 1, # 每分钟拉取次数
                    'status'                => true,
                    'icon'                  => '',
                ],
                [
                    'id'                    => 9,
                    'name'                  => 'GPI',
                    'code'                  => 'GPI',
                    'request_url'           => 'http://club8api.bet8uat.com/op',
                    'report_request_url'    => 'http://casino.w88uat.com/csnbo/api/gateway/betDetail.html',
                    'launcher_request_url'  => '',
                    'rsa_our_private_key'   => '',
                    'rsa_our_public_key'    => '',
                    'rsa_public_key'        => '',
                    'account'               => '{"merch_id":"CLOUDGEEK","merch_pwd":"BD5C64C9-8814-4241-90AD-41ABFF4CB510","live_url":"http://casino.w88uat.com","slot_url":"http://rslots.gpiuat.com","lottery_url":"http://keno.gpiuat.com","p2p_url":"http://pmj.w88uat.com"}',
                    'exchange_currencies'   => null,
                    'is_update_list'        => false,
                    'update_interval'       => 1,
                    'interval'              => 3, # 间隔时间
                    'delay'                 => 0, # 延迟时间
                    'offset'                => 10, # 向前偏移时间
                    'limit'                 => 1, # 每分钟拉取次数
                    'status'                => true,
                    'icon'                  => '',
                ],
                [
                    'id'                    => 10,
                    'name'                  => 'GG',
                    'code'                  => 'GG',
                    'request_url'           => 'https://testapi.gg626.com/api/doLink.do',
                    'report_request_url'    => 'https://testbetrec.gg626.com/api/doReport.do',
                    'launcher_request_url'  => '',
                    'rsa_our_private_key'   => '',
                    'rsa_our_public_key'    => '',
                    'rsa_public_key'        => '',
                    'account'               => '{"agent_name":"TCHL8","password":"asdf1234","md5_key":"Fm2Wh4EUKJ66108r80","des_key":"6P33F561"}',
                    'exchange_currencies'   => null,
                    'is_update_list'        => false,
                    'update_interval'       => 1,
                    'interval'              => 1, # 间隔时间
                    'delay'                 => 10, # 延迟时间
                    'offset'                => 10, # 时间跨度
                    'limit'                 => 1, # 每分钟拉取次数
                    'status'                => true,
                    'icon'                  => '',
                ],
            ];
        } else { # 正式环境
            $gamePlatforms = [
                [
                    'id'                    => 1,
                    'name'                  => 'SA',
                    'code'                  => 'SA',
                    'request_url'           => 'http://api.sa-apisvr.com/api/api.aspx',
                    'report_request_url'    => 'http://api.sa-rpt.com/api/api.aspx',
                    'launcher_request_url'  => 'https://egm.sa-api4.net/app.aspx',
                    'rsa_our_private_key'   => '',
                    'rsa_our_public_key'    => '',
                    'rsa_public_key'        => '',
                    'account'               => '{"encrypt_key":"g9G16nTs","secret_key":"585973D970B940A0A4D13A432EF0253D","md5_key":"GgaIMaiNNtg","lobby":"A1208"}',
//                'account'               => '{"encrypt_key":"g9G16nTs","secret_key":"444BD2CB7A4C48B39ACEB56F1E67BAF8","md5_key":"GgaIMaiNNtg","lobby":"A1259"}',
                    'exchange_currencies'   => null,
                    'is_update_list'        => false,
                    'update_interval'       => 7,
                    'interval'              => 1, # 间隔时间
                    'delay'                 => 0, # 延迟时间
                    'offset'                => 10, # 向前偏移时间
                    'limit'                 => 1, # 每分钟拉取次数
                    'status'                => true,
                    'icon'                  => 'data:image/jpeg;base64,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',
                ],
                [
                    # RTG 延迟10分钟并偏移20分钟
                    'id'                    => 2,
                    'name'                  => 'RTG',
                    'code'                  => 'RTG',
                    'request_url'           => 'https://cms.rtgintegrations.com/api',
                    'report_request_url'    => 'https://cms.rtgintegrations.com/api',
                    'launcher_request_url'  => '',
                    'rsa_our_private_key'   => '',
                    'rsa_our_public_key'    => '',
                    'rsa_public_key'        => '',
                    'account'               => '{"username":"empiregemprodapi","password":"ZiCqPNUxHj","agent_id":"bf942237-a127-47b7-8458-407eb691ec16","id":"14c6562c-3423-4191-8e2f-10df94df1e53","name":"AS1"}',
                    'exchange_currencies'   => '{"VND":"USD"}',
                    'is_update_list'        => true,
                    'update_interval'       => 7,
                    'interval'              => 5, # 间隔时间
                    'delay'                 => 10, # 延迟时间
                    'offset'                => 20, # 向前偏移时间
                    'limit'                 => 1, # 每分钟拉取次数
                    'status'                => true,
                    'icon'                  => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAUCAYAAACEYr13AAAByklEQVQ4jW3Uy4uPYRQH8M+8KJdMzbjFEHukLKRElJ1kg1iyoiwoK2Ql/gU18weMrGVDKStlYSFJUXJbmJ/bYFzGpfOb8/LM+8ypt/d9zvV7zvk+78CvG8vhCC5gAaaxED08w04swh/Mx2tswQ+pCDmOjWbL+3Re09EPYhduxaFJZaOWqxittNzDSZwqEQxXbozjc6WdQfYSJ/CtyerLOk6jRfDdKgVfcy7bIniok+AjzhTni53gnxiI6njVJPwlafyOvfhSBNzBleI8lRuIZyoSjKThIbbifgWYc7ic38NZKBJNNtnLaWzGoyr0v5zHAUxgbbYw0eRhB1ZXIbMlyHMU85JwgeBtO4NDeIrtVdiMHMaDZGzLmUDQawoORCu35+DEQVzL715uYTBX2W9hReG8GJeK80pcL86f8j1UtrCqU/FYXqaQsY7tQ65vaSKZbolUSgRvSKf9HdtkEk3ezv5dGFHLJuyptPzO3ptE0E+wbg7H2Eqg6Mqb5EHMKmbQT7AvA9bnBYo9R5/PY0gJNSpG//F/2I0Xyca+4WYy8UnLrlxpwI21vUtdVAyqx1WOtsP+jxSxnrOJIFgWw4pkUb19Qv84L16gHIe/IXttgQKxIOYAAAAASUVORK5CYII=',
                ],
//            [
//                'name'                  => 'IBC',
//                'code'                  => 'IBC',
//                'request_url'           => 'http://tsa.thaihl8.com/api',
//                'report_request_url'    => 'http://tsa.thaihl8.com/api',
//                'launcher_request_url'  => '',
//                'rsa_our_private_key'   => '',
//                'rsa_our_public_key'    => '',
//                'rsa_public_key'        => '',
//                'account'               => '{"operator_id":"HeleThai","vendor_id":"tux9qv79ps","secret_key":"ICJ72X6DRL94Q3J00S3R","max_transfer":20000,"min_transfer":1,"odds_type":1}',
//                'exchange_currencies'   => null,
//                'is_update_list'        => false,
//                'update_interval'       => 7,
//                'interval'              => 1, # 间隔时间
//                'delay'                 => 0, # 延迟时间
//                'offset'                => 1, # 向前偏移时间
//                'limit'                 => 1, # 每分钟拉取次数
//                'status'                => true,
//            ],
                [
                    'id'                    => 3,
                    'name'                  => 'EBET',
                    'code'                  => 'EBET',
                    'request_url'           => 'http://egthb.ebet.im:8888/api',
                    'report_request_url'    => 'http://egthb.ebet.im:8888/api',
                    'launcher_request_url'  => 'http://egthb.zxc.today/h5/79ad9d',
                    'rsa_our_private_key'   => '-----BEGIN PRIVATE KEY-----
MIIBUwIBADANBgkqhkiG9w0BAQEFAASCAT0wggE5AgEAAkEAm3uqUk0jJEKSeVao
W1mmdSubVdEpfFrLHYzp4JIsXqv0KhxGohi16kLNIrsT7THby3t4FXiA7NcPuRdU
/bSGywIDAQABAkBILU0wrXxNvdWvHCpFVcWvCNIwMUuX3bICgsKjLFgKjgDtMFNY
mxfZlran8DTaSYPZf+X5TMP1KVyJg0inAgAxAiEA0hLhH+JNMZawE6FWFccqNAkB
ii3VXjU1WIKm7TTJPRMCIQC9eYeAcSF8ZOLFf9J2D+VsV+xZJfm2T7/tS35/pXze
aQIgKBzWw+HQX6GoaXcrGul204zMsHfkACMk1ovMGAs2SeUCIBH3Iff1wh0PW8kq
M4RqQXCibZCOXz0AFsyjKd1kjZBxAiAOlPImq7fs1OINdaPfXQj5Ezll0a8aZluk
h5ogpjaWAg==
-----END PRIVATE KEY-----',
                    'rsa_our_public_key'    => '',
                    'rsa_public_key'        => '-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAJt7qlJNIyRCknlWqFtZpnUrm1XRKXxa
yx2M6eCSLF6r9CocRqIYtepCzSK7E+0x28t7eBV4gOzXD7kXVP20hssCAwEAAQ==
-----END PUBLIC KEY-----',
                    'account'               => '{"channel_id":"1070","wallet_type":0}',
                    'exchange_currencies'   => null,
                    'is_update_list'        => false,
                    'update_interval'       => 7,
                    'interval'              => 1, # 间隔时间
                    'delay'                 => 0, # 延迟时间
                    'offset'                => 10, # 向前偏移时间
                    'limit'                 => 1, # 每分钟拉取次数
                    'status'                => true,
                    'icon'                  => '',
                ],
                [
                    'id'                    => 4,
                    'name'                  => 'SP',
                    'code'                  => 'SP',
                    'request_url'           => 'http://api.sp-connection.com/api/api.aspx',
                    'report_request_url'    => 'http://api.sp-connection.com/api/api.aspx',
                    'launcher_request_url'  => '',
                    'rsa_our_private_key'   => '',
                    'rsa_our_public_key'    => '',
                    'rsa_public_key'        => '',
                    'account'               => '{"encrypt_key":"g9G16nTs","secret_key":"6677C588ADE54B64A6EEA76F6822C721","md5_key":"GgaIMaiNNtg","lobby":"S057"}',
                    'exchange_currencies'   => null,
                    'is_update_list'        => false,
                    'update_interval'       => 7,
                    'interval'              => 1, # 间隔时间
                    'delay'                 => 0, # 延迟时间
                    'offset'                => 10, # 向前偏移时间
                    'limit'                 => 1, # 每分钟拉取次数
                    'status'                => true,
                    'icon'                  => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAAUCAYAAACAl21KAAABrklEQVQ4jZXUT4iOURTH8Y/X+LegjD+xQrGzYjtEkR1lY+NPRDYTEQsrMaUkbCyYGpGdskAKC/Ivi8FOSqFkQWKBBWomnencup73neH91dPzPOee873nnnO6k0Zuzd2D47iT77e60MBw/5hzC4NYgB14jRPdgIoC9Lv6n4wjCdzZ5j2BAvQrl39WbstwEbfHD23PqOgLNuJGZdvQFjGOalAvbmJT/r+vQCtwHWcwoxOqBo1iCqZjOxbhLobwPLM9gDfYOhEoCj01a3UFu/EJuxoxC3P9HtYXY8zRN8zMoBiDVbiMJQ3AJSzGmoY9oAfrjOL7Ph42INewMsdhbW70rFrfhgc9lWFeY7eXOelXGxm8a4xKaH5Lu2JA92F5A7I0u/oBfZX9aWTZkwUuOodT2fqiOTiMQ9mQopj+kwPD/TG4AjQNT7CuQ8rRsbOYVdlG88jHasdWFrIvO1K0OQs61IAM5hH/ghTQC1zAqwSerzpV9BirsTeL3aY42lfMxggeNRw+Yn+HznUEBSRUF/I7Tufz41+QAmoqdo9r7/P/AIqiRkfzCom7J6Z2S7cQ+ANVcFiY3mK/AQAAAABJRU5ErkJggg==',
                ],
//            [
//                'name'                  => 'SmartSoft',
//                'code'                  => 'SmartSoft',
//                'request_url'           => 'http://test.ssgportal.com:8099/GamblingService/GamblingWebService.asmx?WSDL',
//                'report_request_url'    => 'http://test.ssgportal.com:8099/GamblingService/GamblingWebService.asmx?WSDL',
//                'launcher_request_url'  => '',
//                'rsa_our_private_key'   => '',
//                'rsa_our_public_key'    => '',
//                'rsa_public_key'        => '',
//                'account'               => '{"client_external_key":"1001","portal_name":"TestPortal","hash_value":"4f306f1b13bb49759aaced44a44d20d7"}',
//                'exchange_currencies'   => null,
//                'is_update_list'        => false,
//                'update_interval'       => 7,
//                'interval'              => 1, # 间隔时间
//                'delay'                 => 0, # 延迟时间
//                'offset'                => 1, # 向前偏移时间
//                'limit'                 => 1, # 每分钟拉取次数
//                'status'                => true,
//                'icon'                  => '',
//            ],
                [
                    'id'                    => 5,
                    'name'                  => 'ISB',
                    'code'                  => 'ISB',
                    'request_url'           => 'http://gap-player-sg.isoftbet.com/v1/546?',
                    'report_request_url'    => 'http://gap-player-sg.isoftbet.com/v1/546?',
                    'launcher_request_url'  => 'https://game-launcher-sg.isoftbet.com/546',
                    'rsa_our_private_key'   => '',
                    'rsa_our_public_key'    => '',
                    'rsa_public_key'        => '',
                    'account'               => '{"lid":"546","secret_key":"mCWbY6cdjFOvPYGsqtJpB800jL4R8MAk"}',
                    'exchange_currencies'   => null,
                    'is_update_list'        => false,
                    'update_interval'       => 7,
                    'interval'              => 1, # 间隔时间
                    'delay'                 => 5, # 延迟时间
                    'offset'                => 1, # 向前偏移时间
                    'limit'                 => 10, # 每分钟拉取次数
                    'status'                => true,
                    'icon'                  => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAcAAAAUCAYAAABBECfmAAAAzUlEQVQokdXRPUpEMRSG4ecOYivKqGDnCvy9dtYW2liKW8gGLC5cSGU/hbgBFyCDvaXxZxdqYaFgJYiSIXdmQF2AX3OSvLyHk6Rq21ZTDxZxgi084DSm8FR9DvuruEHfJG/Y6OGsgMOYQoUDzOE8mx94jyksdF5TD56xnM1bzDf1YLuAtQyQsrmJuyI9YqWsd3oxhXus4xJfGKKOKaTRVf7KzNQQx9jHRUwhd5EH6rKHI+x2B9PwpdTX3+CP/F+4VOr468bPhyvM4nq0wzd2mDJjd5CbwgAAAABJRU5ErkJggg==',
                ],
                [
                    'id'                    => 6,
                    'name'                  => 'N2',
                    'code'                  => 'N2',
                    'request_url'           => 'https://merchanttalk.azuritebox.com',
                    'report_request_url'    => 'https://merchanttalk.azuritebox.com',
                    'launcher_request_url'  => '',
                    'rsa_our_private_key'   => '',
                    'rsa_our_public_key'    => '',
                    'rsa_public_key'        => '',
                    'account'               => '{"merchant_passcode":"5184005298AEF4899EB641F59B9B3C05","vendor_id":"335","merchant_code":"EPG","mobile_domain":"https://epgm.n2ea.com","desktop_domain":"https://epg.n2ea.com"}',
                    'exchange_currencies'   => null,
                    'is_update_list'        => false,
                    'update_interval'       => 7,
                    'interval'              => 1, # 间隔时间
                    'delay'                 => 15, # 延迟时间
                    'offset'                => 10, # 向前偏移时间
                    'limit'                 => 5, # 每分钟拉取次数
                    'status'                => true,
                    'icon'                  => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAASCAYAAABfJS4tAAABW0lEQVQ4jdXUPUtcURAG4GdVRAVT+JHCoJAEAkKCFgoSrRQUBZtAAqlikYQUQlKkSGMltlb+AUG0sBC/ECQI2gSy/gHBQhDL2BmyxaIcGIvsTdxdtvKFy52588575s6Zc3LF3Q414AteoBh+HS6x0JChVodHeI7fuI4F3mGg1orN52f/8ucGl57itFbhJnzEQxRwhTH01NKKlLuBCZyE34xzvC4n3BLkVMmfklg/RvBkPj97VpqYWjGNGSxjC+N4gz48DvEk+gMrWIvcAfzENn5FxWkDG3GcnJd4hV58wxAusI/1IA5jKp5RfEgbhEU8Q2eIij9saoi5S+jCET4jn2kKb7GK99jBJr6WTsUt0kDnwk5Jn/4jKlqwHvZkJvoP4WrwPbit1QjXZ6JZFOJLMRO5Q7gS3C5+XY5brXDFuJ/CbWG3V8B/EO+yN1c6IIfoxl4FwsdxrA/uZOEGazZCwkhd0d4AAAAASUVORK5CYII=',
                ],
                [
                    'id'                    => 8,
                    'name'                  => 'S128',
                    'code'                  => 'S128',
                    'request_url'           => 'https://api2288.uu128.net',
                    'report_request_url'    => 'https://api2288.uu128.net',
                    'launcher_request_url'  => 'http://cs.0128128.net',
                    'rsa_our_private_key'   => '',
                    'rsa_our_public_key'    => '',
                    'rsa_public_key'        => '',
                    'account'               => '{"agent_code":"VC680128","login_id":"VC680128", "password":"AAAA1111", "api_key":"37141954F8264CD491BAEF57A9624D35"}',
                    'exchange_currencies'   => null,
                    'is_update_list'        => false,
                    'update_interval'       => 1,
                    'interval'              => 2, # 间隔时间
                    'delay'                 => 10, # 延迟时间
                    'offset'                => 20, # 向前偏移时间
                    'limit'                 => 1, # 每分钟拉取次数
                    'status'                => true,
                    'icon'                  => '',
                ],
                [
                    'id'                    => 10,
                    'name'                  => 'GG',
                    'code'                  => 'GG',
                    'request_url'           => 'https://api.gg626.com/api/doLink.do',
                    'report_request_url'    => 'http://betrec.gg626.com/api/doReport.do',
                    'launcher_request_url'  => '',
                    'rsa_our_private_key'   => '',
                    'rsa_our_public_key'    => '',
                    'rsa_public_key'        => '',
                    'account'               => '{"agent_name":"null","password":"null","md5_key":"null","des_key":"null"}',
                    'exchange_currencies'   => null,
                    'is_update_list'        => false,
                    'update_interval'       => 1,
                    'interval'              => 1, # 间隔时间
                    'delay'                 => 0, # 延迟时间
                    'offset'                => 1, # 向前偏移时间
                    'limit'                 => 1, # 每分钟拉取次数
                    'status'                => true,
                    'icon'                  => '',
                ],
            ];
        }


        \App\Models\GamePlatform::insert($gamePlatforms);
    }
}
