<?php

namespace Brouzie\Mailer\Tests\Model;

use Brouzie\Mailer\Model\Address;
use Brouzie\Mailer\Model\Email;
use Brouzie\Mailer\Model\EmbeddedFile;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
   
    /**
     * @dataProvider emailReplaceHeadersDataProvider
     */
    public function testEmailReplaceHeaders(array $headers, array $replacedHeaders, array $expectedHeaders)
    {
        $email = new Email();

        $email->setHeaders($headers);
        $email->replaceHeaders($replacedHeaders);

        $this->assertSame($email->getHeaders(), $expectedHeaders);
    }

    public function emailReplaceHeadersDataProvider()
    {
        return [
            [
                [
                    'a' => 'a',
                    'b' => 'b',
                    'c' => 'c',
                ],
                [
                    'a' => '1',
                ],
                [
                    'a' => '1',
                    'b' => 'b',
                    'c' => 'c'
                ]
            ],
            [
                [
                    'a' => 'aa',
                    'b' => 'bb',
                    'c' => 'cc',
                    'd' => 'dd'
                ],
                [
                    'd' => 'aa'
                ],
                [
                    'a' => 'aa',
                    'b' => 'bb',
                    'c' => 'cc',
                    'd' => 'aa'
                ]
            ]
        ];
    }

    /**
     * @dataProvider addRecipientDataProvider
     */
    public function testAddRecipient(array $addresses)
    {
        $email = new Email();
        foreach ($addresses as $address) {
            $email->addRecipient($address);
        }

        $this->assertSame($email->getRecipients(), $addresses);
    }

    /**
     * @dataProvider addRecipientDataProvider
     */
    public function testAddRecipients(array $addresses)
    {
        $email = new Email();
        $email->addRecipients($addresses);

        $this->assertSame($email->getRecipients(), $addresses);
    }

    public function addRecipientDataProvider()
    {
        return [
            [
                [new Address(['name' => 'a@a.com']), new Address(['name2' => 'a2@a.com']), new Address(['name3' => 'a3@a.com'])]
            ]
        ];
    }

    /**
     * @dataProvider addEmbeddedFileDataProvider
     */
    public function testAddEmbeddedFile($embeddedFile, $name, array $expectedValue)
    {
        $email = new Email();
        $email->addEmbeddedFile($embeddedFile, $name);
        $this->assertEquals($email->getEmbeddedFiles(), $expectedValue);
    }

    public function addEmbeddedFileDataProvider()
    {
        $mockEmbeddedFile1 = $this->createMock(EmbeddedFile::class);
        $mockEmbeddedFile1->method('getFilename')
            ->willReturn('file_name');

        $mockEmbeddedFile2 = clone $mockEmbeddedFile1;

        return [
            [
                $mockEmbeddedFile1,
                'lol',
                [
                    'lol' => $mockEmbeddedFile1
                ]
            ],
            [
                $mockEmbeddedFile2,
                null,
                [
                    $mockEmbeddedFile1->getFilename() => $mockEmbeddedFile1
                ]
            ],
        ];
    }
}
