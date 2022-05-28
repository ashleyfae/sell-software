<?php

namespace Tests\Unit\Helpers;

use App\Helpers\DomainSanitizer;
use PHPUnit\Framework\TestCase;

class DomainSanitizerTest extends TestCase
{
    /**
     * @covers \App\Helpers\DomainSanitizer::stripWww()
     */
    public function testCanStripWww(): void
    {
        $this->assertSame('nosegraze.com', DomainSanitizer::stripWww('www.nosegraze.com'));
    }

    /**
     * @covers \App\Helpers\DomainSanitizer::untrailingSlash()
     */
    public function testCanUntrailingSlash(): void
    {
        $this->assertSame('https://nosegraze.com', DomainSanitizer::untrailingSlash('https://nosegraze.com/'));
    }

    /**
     * @covers \App\Helpers\DomainSanitizer::normalize()
     * @dataProvider providerCanNormalize
     * @throws \App\Exceptions\InvalidUrlException
     */
    public function testCanNormalize(string $input, string $expected): void
    {
        $this->assertSame($expected, DomainSanitizer::normalize($input));
    }

    /** @see testCanNormalize */
    public function providerCanNormalize(): \Generator
    {
        yield 'query string' => ['https://www.nosegraze.com?test=123', 'nosegraze.com'];
        yield 'has path' => ['https://www.nosegraze.com/test/', 'nosegraze.com/test'];
    }
}
