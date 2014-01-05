<?php
/*
 * This file is part of the Indigo Backup package.
 *
 * (c) IndigoPHP Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Backup\Destination;

/**
 * Destination Test
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
abstract class DestinationTest extends \PHPUnit_Framework_TestCase
{
    protected $destination;

    public function provider()
    {
        return array(
            array(
                array(
                    tempnam(sys_get_temp_dir(), 'backup_'),
                    tempnam(sys_get_temp_dir(), 'backup_'),
                    tempnam(sys_get_temp_dir(), 'backup_'),
                    tempnam(sys_get_temp_dir(), 'backup_'),
                )
            ),
            array(
                array(
                    tempnam(sys_get_temp_dir(), 'backup_'),
                    tempnam(sys_get_temp_dir(), 'backup_'),
                    tempnam(sys_get_temp_dir(), 'backup_'),
                    tempnam(sys_get_temp_dir(), 'backup_'),
                )
            ),
            array(
                array(
                    tempnam(sys_get_temp_dir(), 'backup_'),
                    tempnam(sys_get_temp_dir(), 'backup_'),
                    tempnam(sys_get_temp_dir(), 'backup_'),
                    tempnam(sys_get_temp_dir(), 'backup_'),
                )
            ),
        );
    }

    public function testInstance()
    {
        $this->assertInstanceOf(
            get_class($this->destination),
            $this->destination
        );
    }

    public function testLogger()
    {
        $logger = \Mockery::mock('Psr\\Log\\LoggerInterface');

        $this->destination->setLogger($logger);
    }

    /**
     * @dataProvider provider
     */
    public function testSave($files)
    {
        $this->assertTrue($this->destination->save($files));
    }
}
