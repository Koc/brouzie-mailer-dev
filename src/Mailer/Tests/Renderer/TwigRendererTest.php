<?php

namespace Brouzie\Mailer\Tests\Renderer;

use Brouzie\Mailer\Model\Email;
use Brouzie\Mailer\Model\Twig\TwigEmail;
use Brouzie\Mailer\Renderer\TwigRenderer;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Template;

class TwigRendererTest extends TestCase
{
    public function testSupports()
    {
        $twig = $this->createMock(Environment::class);
        $renderer = new TwigRenderer($twig);

        $this->assertFalse($renderer->supports(new Email()));

        $this->assertTrue($renderer->supports(new TwigEmail('')));
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(array $blocks, array $blocksResult)
    {
        $renderer = new TwigRenderer($this->getTwigMock($blocks));

        $email = new TwigEmail('foo');

        $this->assertNull($email->getSubject());
        $this->assertNull($email->getContent());
        $this->assertNull($email->getPlainTextContent());
        $this->assertSame([], $email->getHeaders());
        $this->assertSame('foo', $email->getTemplate());

        $renderer->render($email, []);

        $this->assertSame($blocksResult[TwigEmail::BLOCK_SUBJECT], $email->getSubject());
        $this->assertSame($blocksResult[TwigEmail::BLOCK_CONTENT], $email->getContent());
        $this->assertSame($blocksResult[TwigEmail::BLOCK_PLAIN_TEXT_CONTENT], $email->getPlainTextContent());
        $this->assertSame($blocksResult[TwigEmail::BLOCK_HEADERS], $email->getHeaders());
    }

    public function renderDataProvider()
    {

        return [
            [
                [
                    TwigEmail::BLOCK_SUBJECT => 'subject foo_value',
                    TwigEmail::BLOCK_CONTENT => 'content <b>foo_value</b>',
                    TwigEmail::BLOCK_PLAIN_TEXT_CONTENT => 'plain text content foo_value',
                    TwigEmail::BLOCK_HEADERS => 'X-Header-Name: foo_value',
                ],
                [
                    TwigEmail::BLOCK_SUBJECT => 'subject foo_value',
                    TwigEmail::BLOCK_CONTENT => 'content <b>foo_value</b>',
                    TwigEmail::BLOCK_PLAIN_TEXT_CONTENT => 'plain text content foo_value',
                    TwigEmail::BLOCK_HEADERS => ['X-Header-Name' => 'foo_value'],
                ],
            ],
            [
                [
                    TwigEmail::BLOCK_SUBJECT => 'subject foo2_value',
                    TwigEmail::BLOCK_PLAIN_TEXT_CONTENT => 'plain text content foo2_value',
                    TwigEmail::BLOCK_HEADERS => 'X-Header-Name: foo2_value',
                ],
                [
                    TwigEmail::BLOCK_SUBJECT => 'subject foo2_value',
                    TwigEmail::BLOCK_CONTENT => null,
                    TwigEmail::BLOCK_PLAIN_TEXT_CONTENT => 'plain text content foo2_value',
                    TwigEmail::BLOCK_HEADERS => ['X-Header-Name' => 'foo2_value'],
                ],
            ],
            [
                [
                    TwigEmail::BLOCK_CONTENT => 'content <b>foo3_value</b>',
                    TwigEmail::BLOCK_PLAIN_TEXT_CONTENT => 'plain text content foo3_value',
                ],
                [
                    TwigEmail::BLOCK_SUBJECT => null,
                    TwigEmail::BLOCK_CONTENT => 'content <b>foo3_value</b>',
                    TwigEmail::BLOCK_PLAIN_TEXT_CONTENT => 'plain text content foo3_value',
                    TwigEmail::BLOCK_HEADERS => [],
                ],
            ],
            [
                [
                    TwigEmail::BLOCK_SUBJECT => 'subject foo4_value',
                    TwigEmail::BLOCK_HEADERS => 'X-Header-Name: foo4_value',
                    TwigEmail::BLOCK_PLAIN_TEXT_CONTENT => 'plain text content foo4_value',
                ],
                [
                    TwigEmail::BLOCK_SUBJECT => 'subject foo4_value',
                    TwigEmail::BLOCK_CONTENT => null,
                    TwigEmail::BLOCK_PLAIN_TEXT_CONTENT => 'plain text content foo4_value',
                    TwigEmail::BLOCK_HEADERS => ['X-Header-Name' => 'foo4_value'],
                ],
            ]
        ];
    }

    protected function getTwigMock(array $blocks = [])
    {
        $twig = $this->createMock(Environment::class);

        $twig
            ->expects($this->any())
            ->method('load')
            ->willReturnCallback(
                function ($templateName) use ($blocks) {
                    $template = $this->createMock(Template::class);

                    $template
                        ->expects($this->any())
                        ->method('renderBlock')
                        ->willReturnCallback(
                            function ($block, $context) use ($blocks) {
                                return $blocks[$block];
                            }
                        );

                    $template
                        ->expects($this->any())
                        ->method('hasBlock')
                        ->willReturnCallback(
                            function ($block) use ($blocks) {
                                return array_key_exists($block, $blocks);
                            }
                        );

                    return $template;
                }
            );

        return $twig;
    }
}
