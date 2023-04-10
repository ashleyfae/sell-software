<?php

namespace Tests\Feature\Actions\Orders;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @covers \App\Actions\Orders\RenewOrderItem
 */
class RenewOrderItemTest extends TestCase
{
    /**
     * @covers \App\Actions\Orders\RenewOrderItem::execute()
     */
    public function testCanExecute(): void
    {
        $this->markTestIncomplete(__METHOD__);
    }
}
