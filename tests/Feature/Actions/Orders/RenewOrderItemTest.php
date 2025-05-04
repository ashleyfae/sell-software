<?php

namespace Tests\Feature\Actions\Orders;

use App\Actions\Orders\RenewOrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(RenewOrderItem::class)]
class RenewOrderItemTest extends TestCase
{
    /**
     * @see \App\Actions\Orders\RenewOrderItem::execute()
     */
    public function testCanExecute(): void
    {
        $this->markTestIncomplete(__METHOD__);
    }
}
