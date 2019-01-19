<?php

namespace Brouzie\Mailer\Tests\Renderer;

use Brouzie\Mailer\Model\Email;
use Brouzie\Mailer\Renderer\ChainRenderer;
use Brouzie\Mailer\Renderer\Renderer;
use PHPUnit\Framework\TestCase;

class ChainRendererTest extends TestCase
{

    /**
     * @expectedException              \Brouzie\Mailer\Exception\RendererNotFoundException
     * @expectedExceptionMessageRegExp /^No renderer found for email of type ".+?"\.$/
     */
    public function testRenderException()
    {
        $chainRenderer = new ChainRenderer([$this->createRendererMock(false)]);

        $chainRenderer->render($this->createMock(Email::class));
    }

    /**
     * @dataProvider supportsDataProvider
     */
    public function testSupports(iterable $renders, $email, $expectedValue)
    {
        $chainRenderer = new ChainRenderer($renders);

        $this->assertSame($chainRenderer->supports($email), $expectedValue);
    }

    public function supportsDataProvider()
    {
        $emailMock = $this->createMock(Email::class);

        $supportedRenderer = $this->createRendererMock(true);
        $notSupportedRenderer = $this->createRendererMock(false);

        return [
            [
                [
                    $supportedRenderer,
                    $supportedRenderer,
                ],
                $emailMock,
                true,
            ],
            [
                [
                    $notSupportedRenderer,
                    $supportedRenderer,
                ],
                $emailMock,
                true,
            ],
            [
                [
                    $notSupportedRenderer,
                    $notSupportedRenderer,
                ],
                $emailMock,
                false,
            ],
        ];
    }

    private function createRendererMock(bool $support = true)
    {
        $renderer = $this->createMock(Renderer::class);

        $renderer
            ->expects($this->any())
            ->method('render')
            ->will($this->returnValue(''));

        $renderer
            ->expects($this->any())
            ->method('supports')
            ->will($this->returnValue($support));

        return $renderer;
    }
}
