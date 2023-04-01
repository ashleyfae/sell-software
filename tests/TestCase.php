<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Traits\CanGetInaccessibleMembers;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, CanGetInaccessibleMembers;
}
