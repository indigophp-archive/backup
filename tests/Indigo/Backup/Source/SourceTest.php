<?php
/*
 * This file is part of the Indigo Backup package.
 *
 * (c) IndigoPHP Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Backup\Source;

/**
 * Source Test
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
abstract class SourceTest extends \PHPUnit_Framework_TestCase
{
    protected $source;

    public function testInstance()
    {
        $this->assertInstanceOf(
            get_class($this->source),
            $this->source
        );
    }

    public function testBackupReturn()
    {
        $files = $this->source->backup();
        $this->assertTrue(is_array($files));
        return $files;
    }

    /**
     * @depends testBackupReturn
     */
    public function testFileExists($files)
    {
        if (!empty($files)) {
            $files = reset($files);
            $this->assertFileExists($files);
        }
    }

    public function testLogger()
    {
        $logger = \Mockery::mock('Psr\\Log\\LoggerInterface');

        $this->source->setLogger($logger);
    }

    public function testCleanup()
    {
        if ($this->source instanceof CleanSourceInterface) {
            $this->assertTrue(is_bool($this->source->cleanup()));
        }
    }
}
