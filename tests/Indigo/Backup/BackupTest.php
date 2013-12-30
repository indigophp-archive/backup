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
        $source = \Mockery::mock('Indigo\Backup\Source\SourceInterface', function($mock) {
            $mock->shouldReceive('backup')
                ->andReturn(array());
        });

        $destination = \Mockery::mock('Indigo\Backup\Destination\DestinationInterface', function($mock) {
            $mock->shouldReceive('put')
                ->andReturn(array());
        });


        $backup = new Backup($source, $destination);

        $this->assertEquals(true, $backup->run());
    }
}