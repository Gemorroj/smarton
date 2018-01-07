<?php

namespace App\Tests\Command;

use App\Command\DumperCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class DumperCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $application->add(new DumperCommand($kernel->getContainer()));

        $command = $application->find('app:dumper');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'date' => '2018-01-01 00:00:00.000000',
            'path' => '/dump.sql',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertContains('Дамп успешно создан', $output);
    }
}
