<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * @covers \App\Http\Controllers\HomepageController
 */
class HomepageControllerTest extends TestCase
{
    /**
     * @covers \App\Http\Controllers\HomepageController::__invoke()
     */
    public function testCanInvoke(): void
    {
        $response = $this->get(route('home'));

        $response->assertRedirectToRoute('customer.downloads.list');
    }
}
