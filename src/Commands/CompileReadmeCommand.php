<?php

namespace Luttje\ExampleTester\Commands;

use ColinODell\Indentation\Indentation;
use Luttje\ExampleTester\Attributes\ReadmeExampleCompiler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'compile',
    description: 'Compile the README.md file for the package based on attributes in the tests. Run this command from the root of your project.',
    aliases: ['compile-readme'],
)]
class CompileReadmeCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument(
                'namespace',
                mode: InputArgument::REQUIRED,
                description: 'The namespace to search for tests with attributes. Uses composer autoload to find the files. Must be autoloaded through PSR-4.',
            )
            ->addOption(
                'output',
                mode: InputOption::VALUE_REQUIRED,
                description: 'The path to the README.md file to write to. Defaults to the README.md file in the root of the package.',
            )
            ->addOption(
                'input',
                mode: InputOption::VALUE_REQUIRED,
                description: 'The path to the README.md file to read from. Defaults to the same file as the output.',
            )
            ->addOption(
                'warning-comment',
                mode: InputOption::VALUE_REQUIRED,
                description: 'Path to a file containing the warning comment to prepend to the examples section. Defaults to the warning comment in the ReadmeExampleCompiler class. If set to false, no warning comment will be prepended.',
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $workingDirectory = getcwd();
        $namespace = trim($input->getArgument('namespace'), "\\") . '\\';
        $outputFile = $input->getOption('output') ?? $workingDirectory.'/README.md';
        $inputFile = $input->getOption('input') ?? $outputFile;

        // Check if the namespace is autoloaded through PSR-4.
        $autoloadPath = $workingDirectory.'/vendor/composer/autoload_psr4.php';
        $autoloadNamespaces = require $autoloadPath;
        $directory = null;

        foreach ($autoloadNamespaces as $autoloadNamespace => $autoloadDirectory) {
            if (str_starts_with($namespace, $autoloadNamespace)) {
                $directory = $autoloadDirectory[0];

                // If it exactly matches, we can stop searching. If this is only a parent namespace we will append the
                // rest of the namespace to the directory and check if that exists.
                if ($namespace === $autoloadNamespace) {
                    break;
                }

                $directory .= DIRECTORY_SEPARATOR . trim(str_replace($autoloadNamespace, '', $namespace), "\\");
                $directory = realpath($directory);

                if ($directory !== false) {
                    break;
                }

                $directory = null;
            }
        }

        if ($directory === null) {
            $exists = is_file($autoloadPath) ? 'exists' : 'does not exist';
            $output->writeLn("<error>Namespace {$namespace} nor any of it's parents are not autoloaded through PSR-4. Used path: $autoloadPath ($exists).</error>");

            return Command::FAILURE;
        }

        if (! is_file($inputFile)) {
            $output->writeLn("<error>Input file {$inputFile} does not exist.</error>");

            return Command::FAILURE;
        }

        if (is_dir($outputFile)) {
            $output->writeLn("<error>Output file {$outputFile} is a directory.</error>");

            return Command::FAILURE;
        }

        // Check if the output file is a valid file path.
        if (is_file($outputFile) && ! is_writable($outputFile)) {
            $output->writeLn("<error>Output file {$outputFile} is not writable.</error>");

            return Command::FAILURE;
        }

        if ($inputFile === $outputFile) {
            $output->writeLn("Compiling examples in {$inputFile}...");
        } else {
            $output->writeLn("Compiling examples from {$inputFile} to {$outputFile}...");
        }

        $warning = '';
        $warningFile = $input->getOption('warning-comment');

        if ($warningFile !== null) {
            if ($warningFile === 'false') {
                $warning = '';
            } else {
                if (! is_file($warningFile)) {
                    $output->writeLn("<error>Warning comment file {$warningFile} does not exist.</error>");

                    return Command::FAILURE;
                }

                $warning = "\n".file_get_contents($warningFile);
            }
        } else {
            $warning = "\n".$this->getDefaultWarningComment();
        }

        $readme = file_get_contents($inputFile);

        $readme = preg_replace(
            '/<!-- #EXAMPLES_START -->(.*)<!-- #EXAMPLES_END -->/s',
            '<!-- #EXAMPLES_START -->'.$warning.$this->getExamplesMarkdown($directory, $namespace)."\n\n".'<!-- #EXAMPLES_END -->',
            $readme
        );

        file_put_contents($outputFile, $readme);

        $output->writeLn('Done compiling examples!');

        return Command::SUCCESS;
    }

    public function getDefaultWarningComment(): string
    {
        return Indentation::unindent(<<<'TEXT'
        <!--
            WARNING!

            The contents up until #EXAMPLES_END are auto-generated based on attributes
            in the tests.

            Do not edit this section manually or your changes will be overwritten.
        -->
        TEXT);
    }

    /**
     * Goes through all php files in the given directory and returns the markdown for all examples.
     */
    public function getExamplesMarkdown(string $directory, string $namespace): string
    {
        $allMarkdown = '';
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));

        foreach ($files as $file) {
            if (! $file->isFile()) {
                continue;
            }

            if ($file->getExtension() !== 'php') {
                continue;
            }

            $className = $namespace . ltrim(str_replace([$directory, '.php'], ['', ''], $file->getPathname()), DIRECTORY_SEPARATOR);

            if (! class_exists($className)) {
                echo $className . ' does not exist' . PHP_EOL;
                continue;
            }

            $reflectionClass = new \ReflectionClass($className);

            if ($reflectionClass->isAbstract()) {
                continue;
            }

            $compiler = new ReadmeExampleCompiler($className);
            $markdown = $compiler->toMarkdown();

            if ($markdown === '') {
                continue;
            }

            $allMarkdown .= $markdown;
        }

        return $allMarkdown;
    }
}
