<?php

namespace Luttje\ExampleTester\Attributes;

use ColinODell\Indentation\Indentation;

class ReadmeExampleCompiler
{
    protected array $examples = [];

    public function __construct(protected string $className)
    {
        $this->discover();
    }

    public function discover(): array
    {
        $reflectionClass = new \ReflectionClass($this->className);

        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            $readmeExampleAttribute = $reflectionMethod->getAttributes(ReadmeExample::class);
            $readmeExampleDescriptionAttribute = $reflectionMethod->getAttributes(ReadmeExampleDescription::class);

            if (count($readmeExampleAttribute) === 0 && count($readmeExampleDescriptionAttribute) === 0) {
                continue;
            }

            $name = $readmeExampleAttribute[0]->newInstance()->name;

            $this->examples[$name] = $this->examples[$name] ?? [];

            // Add the description and method body to the examples for this name
            $descriptions = [];

            foreach ($readmeExampleDescriptionAttribute as $attribute) {
                $descriptions[] = $attribute->newInstance();
            }

            $example = [
                'descriptions' => $descriptions,
                'code' => $this->getCode($reflectionMethod),
            ];

            $this->examples[$name][] = $example;
        }

        return $this->examples;
    }

    protected function getCode(\ReflectionMethod $reflectionMethod): string
    {
        $fileName = $reflectionMethod->getFileName();
        $startLine = $reflectionMethod->getStartLine();
        $endLine = $reflectionMethod->getEndLine() - 1;

        $code = '';

        foreach (range($startLine, $endLine) as $lineNumber) {
            $code .= file($fileName)[$lineNumber];
        }

        $code = ltrim(rtrim(Indentation::unindent($code)));

        // Remove { and } and any newlines before/after them
        $code = preg_replace('/^{[\r\n]+/', '', $code);
        $code = preg_replace('/[\r\n]+}$/', '', $code);

        return Indentation::unindent($code);
    }

    public function getExamples(): array
    {
        return $this->examples;
    }

    public function toMarkdown(): string
    {
        $markdown = '';

        foreach ($this->examples as $name => $example) {
            $markdown .= $this->exampleToMarkdown($name, $example);
        }

        return $markdown;
    }

    protected function exampleToMarkdown(string $name, array $examples): string
    {
        $markdown = '';

        $markdown .= "\n\n### {$name}";

        foreach ($examples as $example) {
            $footnotes = '';

            foreach ($example['descriptions'] as $description) {
                $descriptionText = Indentation::unindent($description->description);
                $markdown .= "\n\n{$descriptionText}";

                if ($description->footnotes) {
                    $footnotesText = Indentation::unindent($description->footnotes);
                    $footnotes .= "{$footnotesText}";
                }
            }

            $markdown .= "\n\n```php\n{$example['code']}\n```";

            if ($footnotes) {
                $markdown .= "\n\n{$footnotes}";
            }
        }

        return $markdown;
    }
}
