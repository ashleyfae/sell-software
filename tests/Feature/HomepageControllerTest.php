<?php

namespace Tests\Feature;

use App\Http\Controllers\HomepageController;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;


#[CoversClass(HomepageController::class)]
class HomepageControllerTest extends TestCase
{
    /**
     * @see \App\Http\Controllers\HomepageController::__invoke()
     */
    public function testCanInvoke(): void
    {
        $response = $this->get(route('home'));

        $response->assertRedirectToRoute('customer.downloads.list');
    }
}
