<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\TrackingStatisticLog::class, function (Faker $faker) {
    $date_time = $faker->date . ' ' . $faker->time;
    $ips = [
        "95.165.145.152",
        "185.168.102.213",
        "109.71.86.221",
        "121.79.221.77",
        "175.55.60.245",
        "168.119.157.218",
        "105.156.188.95",
        "173.126.212.63",
        "77.24.215.172",
        "158.69.175.254",
        "217.170.225.237",
        "176.107.155.61",
        "113.72.242.124",
        "29.51.77.132",
        "49.92.37.152",
        "216.37.191.100",
        "73.92.174.84",
        "65.202.74.164",
        "16.60.203.196",
        "254.250.27.163",
        "214.41.3.70",
        "209.234.218.51",
        "202.36.143.55",
        "156.128.185.150",
        "189.174.201.58",
        "79.24.85.182",
        "135.180.188.252",
        "145.201.147.33",
        "224.181.135.143",
        "165.150.59.67",
        "64.76.102.181",
        "205.36.62.95",
        "166.95.138.60",
        "79.222.250.4",
        "127.49.134.220",
        "35.6.171.129",
        "230.242.63.44",
        "142.153.217.163",
        "246.99.201.88",
        "215.57.164.133",
        "51.197.126.184",
        "51.228.70.28",
        "241.9.216.95",
        "152.24.119.16",
        "7.100.249.190",
        "207.227.68.86",
        "66.230.17.174",
        "29.12.166.117",
        "159.47.147.224",
        "57.232.183.245",
        "223.23.196.249",
        "155.227.196.37"
    ];
    return [
        'tracking_id' => mt_rand(1, 100),
        'ip'          => $ips[mt_rand(0, count($ips) -1)],
        'created_at'  => $date_time,
        'updated_at'  => $date_time,
    ];
});