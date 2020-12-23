<?php

namespace Tests\Feature;

use App\Services\SMSService;
use Tests\TestCase;

class MessageTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $smsService = new SMSService();
        $a = $smsService->call('84786854643');
    }
}
