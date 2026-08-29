<?php

declare(strict_types=1);

namespace FullStackAppCo\BladeExtras\Tests\Feature;

use FullStackAppCo\BladeExtras\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Testing\TestView;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\Test;

class ServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        View::addLocation(__DIR__.'/../fixtures/views');
    }

    /**
     * @param  list<string>  $items
     */
    protected function render(array $items = ['One', 'Two', 'Three']): TestView
    {
        return $this->view('fixture', ['items' => $items]);
    }

    #[Test]
    public function it_registers_the_html_fragment_singleton(): void
    {
        $this->assertSame($this->app->make('html-fragment'), $this->app->make('html-fragment'));
    }

    #[Test]
    public function it_registers_all_test_view_macros(): void
    {
        foreach (['assertXPathCount', 'assertXPathSingle', 'assertXPathMissing', 'assertXPath'] as $macro) {
            $this->assertTrue(TestView::hasMacro($macro), "Macro '{$macro}' is not registered.");
        }
    }

    #[Test]
    public function get_xpath_nodes_returns_matching_nodes(): void
    {
        $nodes = $this->app->make(ServiceProvider::class, ['app' => $this->app])
            ->getXPathNodes('<ul><li>a</li><li>b</li></ul>', '//li');

        $this->assertSame(2, $nodes->length);
        $this->assertSame('a', $nodes->item(0)?->textContent);
        $this->assertSame('b', $nodes->item(1)?->textContent);
    }

    #[Test]
    public function assert_xpath_count_passes_when_count_matches(): void
    {
        $view = $this->render();

        $this->assertSame($view, $view->assertXPathCount('//li[@class="item"]', 3));
    }

    #[Test]
    public function assert_xpath_count_fails_when_count_does_not_match(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessageIsOrContains("Failed asserting that XPath expression '//li' matches exactly 2 node(s).");

        $this->render()->assertXPathCount('//li', 2);
    }

    #[Test]
    public function assert_xpath_count_uses_a_custom_message(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessageIsOrContains('Custom message');

        $this->render()->assertXPathCount('//li', 2, 'Custom message');
    }

    #[Test]
    public function assert_xpath_single_passes_for_exactly_one_node(): void
    {
        $view = $this->render();

        $this->assertSame($view, $view->assertXPathSingle('//p[@id="intro"]'));
    }

    #[Test]
    public function assert_xpath_single_fails_for_multiple_nodes(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessageIsOrContains("Failed asserting that XPath expression '//li' matches exactly 1 node(s).");

        $this->render()->assertXPathSingle('//li');
    }

    #[Test]
    public function assert_xpath_missing_passes_when_no_nodes_match(): void
    {
        $view = $this->render();

        $this->assertSame($view, $view->assertXPathMissing('//table'));
    }

    #[Test]
    public function assert_xpath_missing_fails_when_nodes_match(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessageIsOrContains("Failed asserting that XPath expression '//li' matches no nodes.");

        $this->render()->assertXPathMissing('//li');
    }

    #[Test]
    public function assert_xpath_passes_when_callback_returns_true_for_every_node(): void
    {
        $view = $this->render();
        $seen = [];

        $result = $view->assertXPath('//li', function (string $text) use (&$seen): bool {
            $seen[] = $text;

            return true;
        });

        $this->assertSame($view, $result);
        $this->assertSame(['One', 'Two', 'Three'], $seen);
    }

    #[Test]
    public function assert_xpath_fails_when_no_nodes_match(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessageIsOrContains("Failed asserting that XPath expression '//table' matches at least one node.");

        $this->render()->assertXPath('//table', fn (string $text): bool => true);
    }

    #[Test]
    public function assert_xpath_fails_when_callback_returns_false_for_any_node(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessageIsOrContains("Failed asserting that callback returned true for node text: 'Two'");

        $this->render()->assertXPath('//li', fn (string $text): bool => $text !== 'Two');
    }
}
