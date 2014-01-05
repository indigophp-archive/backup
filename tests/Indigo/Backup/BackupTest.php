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
    public function provider()
    {
        return array(
            array(
                \Mockery::mock(
                    'Indigo\\Backup\\Source\\SourceInterface, Indigo\\Backup\\Source\\CleanSourceInterface',
                    function($mock) {
                        $mock->shouldReceive('backup')->andReturn(array());
                        $mock->shouldReceive('cleanup')->andReturn(true);
                    }
                ),
                \Mockery::mock(
                    'Indigo\\Backup\\Destination\\DestinationInterface',
                    function($mock) {
                        $mock->shouldReceive('save')->andReturn(true);
                    }
                ),
            ),
            array(
                \Mockery::mock(
                    'Indigo\\Backup\\Source\\SourceInterface',
                    function($mock) {
                        $mock->shouldReceive('backup')->andReturn(array());
                    }
                ),
                \Mockery::mock(
                    'Indigo\\Backup\\Destination\\DestinationInterface',
                    function($mock) {
                        $mock->shouldReceive('save')->andReturn(true);
                    }
                ),
            ),
            array(
                \Mockery::mock(
                    'Indigo\\Backup\\Source\\DatabaseSource',
                    function($mock) {
                        $mock->shouldReceive('backup')->andReturn(array());
                        $mock->shouldReceive('cleanup')->andReturn(true);
                    }
                ),
                \Mockery::mock(
                    'Indigo\\Backup\\Destination\\FlysystemDestination',
                    function($mock) {
                        $mock->shouldReceive('save')->andReturn(true);
                    }
                ),
            ),
        );
    }

    public function tearDown()
    {
        \Mockery::close();
    }

    /**
     * @dataProvider provider
     */
    public function testBackup($source, $destination)
    {
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