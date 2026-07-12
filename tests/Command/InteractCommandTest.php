<?php

namespace Tests\Command;

use ChernegaSergiy\TableMagic\Command\InteractCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class InteractCommandTest extends TestCase
{
    private string $tempFile;

    protected function setUp(): void
    {
        $this->tempFile = tempnam(sys_get_temp_dir(), 'tm_test_');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    public function testExecuteFailsIfFileNotFound(): void
    {
        $command = new InteractCommand();
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'file' => '/path/to/non/existent/file.csv'
        ]);

        $this->assertNotSame(0, $commandTester->getStatusCode());
        $this->assertStringContainsString('File not found or not readable', $commandTester->getDisplay());
    }

    public function testExecuteFailsIfFormatCannotBeGuessed(): void
    {
        file_put_contents($this->tempFile, "data");

        $command = new InteractCommand();
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'file' => $this->tempFile
        ]);

        $this->assertNotSame(0, $commandTester->getStatusCode());
        $this->assertStringContainsString('Could not guess format from file extension', $commandTester->getDisplay());
    }

    public function testExecuteFailsOnInvalidData(): void
    {
        file_put_contents($this->tempFile, "invalid json");

        $command = new InteractCommand();
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'file' => $this->tempFile,
            '--format' => 'json'
        ]);

        $this->assertNotSame(0, $commandTester->getStatusCode());
        $this->assertStringContainsString('Error: ', $commandTester->getDisplay());
    }

    public function testExecuteSuccessfullyStartsInteractiveMode(): void
    {
        $data = "name,age\nAlice,30\nBob,25";
        file_put_contents($this->tempFile, $data);

        $command = new InteractCommand();
        $commandTester = new CommandTester($command);
        $commandTester->setInputs(['q']);
        $commandTester->execute([
            'file' => $this->tempFile,
            '--format' => 'csv',
            '--style' => 'default',
            '--rows' => '5'
        ]);

        $this->assertSame(0, $commandTester->getStatusCode());
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Starting interactive mode', $output);
        $this->assertStringContainsString('Interactive mode exited', $output);
    }
}
