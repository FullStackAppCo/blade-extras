<?php

declare(strict_types=1);

namespace FullStackAppCo\BladeExtras;

use DOMNodeList;
use DOMXPath;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider as BaseProvider;
use Illuminate\Testing\TestView;
use Masterminds\HTML5;
use PHPUnit\Framework\Assert;

class ServiceProvider extends BaseProvider
{
    public function register(): void
    {
        if (! App::environment('testing')) {
            return;
        }

        $this->app->singleton('html-fragment', function () {
            return new HTML5(['disable_html_ns' => true]);
        });
    }

    public function boot(): void
    {
        if (App::environment('testing')) {
            $this->bootTestHelpers();
        }
    }

    public function getXPathNodes(string $rendered, string $expression): DOMNodeList
    {
        $dom = App::make('html-fragment')->loadHTML($rendered);
        $xpath = new DOMXPath($dom);

        return $xpath->query($expression);
    }

    public function assertXPathCount(string $rendered, string $expression, int $count, ?string $message = null): void
    {
        Assert::assertCount(
            $count,
            $this->getXPathNodes($rendered, $expression),
            $message ?? "Failed asserting that XPath expression '{$expression}' matches exactly {$count} node(s).",
        );
    }

    protected function bootTestHelpers(): void
    {
        $self = $this;

        TestView::macro('assertXPathCount', function (string $expression, int $count, ?string $message = null) use ($self): TestView {
            $self->assertXPathCount($this->rendered, $expression, $count, $message);

            return $this;
        });

        TestView::macro('assertXPathSingle', function (string $expression) use ($self): TestView {
            $self->assertXPathCount($this->rendered, $expression, 1);

            return $this;
        });

        TestView::macro('assertXPathMissing', function (string $expression) use ($self): TestView {
            $self->assertXPathCount($this->rendered, $expression, 0, "Failed asserting that XPath expression '$expression' matches no nodes.");

            return $this;
        });

        TestView::macro('assertXPath', function (string $expression, callable $callback) use ($self): TestView {
            $nodes = $self->getXPathNodes($this->rendered, $expression);

            Assert::assertGreaterThan(
                0,
                $nodes->length,
                "Failed asserting that XPath expression '$expression' matches at least one node."
            );

            foreach ($nodes as $node) {
                Assert::assertTrue(
                    $callback($node->textContent),
                    "Failed asserting that callback returned true for node text: '$node->textContent'"
                );
            }

            return $this;
        });
    }
}
