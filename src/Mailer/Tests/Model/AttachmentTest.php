<?php

namespace Brouzie\Mailer\Tests\Model;

use Brouzie\Mailer\Model\Attachment;
use PHPUnit\Framework\TestCase;

class AttachmentTest extends TestCase
{
    const FIXTURE = __DIR__.'/../Fixtures/1px-transparent.gif';

    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(Attachment $attachment)
    {
        $this->assertInstanceOf(Attachment::class, $attachment);
        $this->assertSame('1px-transparent.gif', $attachment->getFilename());
        $this->assertSame('image/gif', $attachment->getContentType());
        $this->assertStringEqualsFile(self::FIXTURE, $attachment->getContent());
    }

    public function createDataProvider()
    {
        yield [Attachment::fromPath(self::FIXTURE)];
        yield [Attachment::fromStream(fopen(self::FIXTURE, 'rb'))];
    }
}
