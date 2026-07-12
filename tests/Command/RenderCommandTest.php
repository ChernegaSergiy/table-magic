<?php

namespace Tests\Command;

use ChernegaSergiy\TableMagic\Command\RenderCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class RenderCommandTest extends TestCase
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

    public function testExecuteRendersTableSuccessfully(): void
    {
        $data = "name,age\nAlice,30\nBob,25";
        file_put_contents($this->tempFile, $data);

        $command = new RenderCommand();
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'file' => $this->tempFile,
            '--format' => 'csv'
        ]);

        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Alice', $output);
        $this->assertStringContainsString('30', $output);
    }

    public function testExecuteFailsIfFileNotFound(): void
    {
        $command = new RenderCommand();
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

        $command = new RenderCommand();
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

        $command = new RenderCommand();
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'file' => $this->tempFile,
            '--format' => 'json'
        ]);

        $this->assertNotSame(0, $commandTester->getStatusCode());
        $this->assertStringContainsString('Error rendering table', $commandTester->getDisplay());
    }
}
