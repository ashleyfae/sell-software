<?php

namespace Tests\Unit\Helpers;

use App\Exceptions\InvalidUrlException;
use App\Helpers\DomainSanitizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(DomainSanitizer::class)]
class DomainSanitizerTest extends TestCase
{
    /**
     * @see \App\Helpers\DomainSanitizer::stripWww()
     */
    public function testCanStripWww(): void
    {
        $this->assertSame('nosegraze.com', DomainSanitizer::stripWww('www.nosegraze.com'));
    }

    /**
     * @see \App\Helpers\DomainSanitizer::untrailingSlash()
     */
    public function testCanUntrailingSlash(): void
    {
        $this->assertSame('https://nosegraze.com', DomainSanitizer::untrailingSlash('https://nosegraze.com/'));
    }

    /**
     * @see \App\Helpers\DomainSanitizer::normalize()
     * @throws InvalidUrlException
     */
    #[DataProvider('providerCanNormalize')]
    public function testCanNormalize(string $input, string $expected): void
    {
        $this->assertSame($expected, DomainSanitizer::normalize($input));
    }

    /** @see testCanNormalize */
    public static function providerCanNormalize(): \Generator
    {
        yield 'query string' => ['https://www.nosegraze.com?test=123', 'nosegraze.com'];
        yield 'has path' => ['https://www.nosegraze.com/test/', 'nosegraze.com/test'];
    }
}
