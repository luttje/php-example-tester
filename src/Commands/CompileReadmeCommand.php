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
    name: 'example-tester:compile',
    description: 'Compile the README.md file for the package based on attributes in the tests.',
    aliases: ['example-tester:compile-readme'],
)]
class CompileReadmeCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument(
                'class',
                mode: InputArgument::REQUIRED,
                description: 'A fully qualified class name which contains the examples.'
            )
            ->addOption(
                'output',
                mode: InputOption::VALUE_REQUIRED,
                description: 'The path to the README.md file to write to. Defaults to the README.md file in the root of the package.'
            )
            ->addOption(
                'input',
                mode: InputOption::VALUE_REQUIRED,
                description: 'The path to the README.md file to read from. Defaults to the same file as the output.'
            )
            ->addOption(
                'warning-comment',
                mode: InputOption::VALUE_REQUIRED,
                description: 'Path to a file containing the warning comment to prepend to the examples section. Defaults to the warning comment in the ReadmeExampleCompiler class. If set to false, no warning comment will be prepended.'
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $class = $input->getArgument('class');
        $outputFile = $input->getOption('output') ?? __DIR__.'/../../README.md';
        $inputFile = $input->getOption('input') ?? $outputFile;

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
            '<!-- #EXAMPLES_START -->'.$warning.$this->getExamplesMarkdown($class)."\n\n".'<!-- #EXAMPLES_END -->',
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

    public function getExamplesMarkdown(string $class): string
    {
        $discoverer = new ReadmeExampleCompiler($class);

        return $discoverer->toMarkdown();
    }
}
