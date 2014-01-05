<?php
/*
 * This file is part of the Indigo Backup package.
 *
 * (c) IndigoPHP Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Backup;

/**
 * Backup Test
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class BackupTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        \Mockery::close();
    }

    public function testBackup()
    {
        $source = \Mockery::mock(
            'Indigo\\Backup\\Source\\SourceInterface, Indigo\\Backup\\Source\\CleanSourceInterface',
            function($mock) {
                $mock->shouldReceive('backup')
                    ->andReturn(array());

                $mock->shouldReceive('cleanup')->andReturn(true);
        });

        $destination = \Mockery::mock('Indigo\\Backup\\Destination\\DestinationInterface', function($mock) {
            $mock->shouldReceive('save')
                ->andReturn(true);
        });

        $logger = \Mockery::mock('Psr\\Log\\LoggerInterface');

        $backup = new Backup($source, $destination);

        $this->assertInstanceOf(
            'Indigo\\Backup\\Backup',
            $backup->addSource($source)
        );

        $this->assertInstanceOf(
            'Indigo\\Backup\\Backup',
            $backup->addSource($source, true)
        );

        $this->assertInstanceOf(
            'Indigo\\Backup\\Backup',
            $backup->addDestination($destination)
        );

        $this->assertInstanceOf(
            'Indigo\\Backup\\Backup',
            $backup->addDestination($destination, true)
        );

        $this->assertTrue($backup->run());

        $this->assertNull($backup->setLogger($logger));
    }
}