<?php
/*
 * This file is part of the Indigo Backup package.
 *
 * (c) IndigoPHP Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Backup\Processor;

/**
 * Processor Test
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
abstract class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    protected $processor;

    public function testInstance()
    {
        $this->assertInstanceOf(
            get_class($this->processor),
            $this->processor
        );
    }

    public function testReturn()
    {
        $this->assertTrue(is_array($this->processor->process(array())));
    }
}
