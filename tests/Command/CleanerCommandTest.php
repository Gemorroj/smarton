<?php

namespace App\Tests\Command;

use App\Command\CleanerCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CleanerCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $application->add(new CleanerCommand($kernel->getContainer()->get('doctrine.orm.entity_manager')));

        $command = $application->find('app:cleaner');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'date' => '2018-01-01 00:00:00.000000',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertContains('Операция успешно выполнена.', $output);
    }
}
