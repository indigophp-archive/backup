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
 * File Extension Processor Test
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class FileExtensionProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function provider()
    {
        return array(
            array(
                array(
                    'test.jpg',
                    'test.jpg',
                    'test.png',
                    'test.nope',
                ),
                'jpg',
                true
            ),
            array(
                array(
                    'test.jpg',
                    'test.jpg',
                    'test.png',
                    'test.nope',
                ),
                'png',
                false
            ),
            array(
                array(
                    'test.jpg',
                    'test.jpg',
                    'test.png',
                    'test.nope',
                ),
                array('nope', 'png' => true),
                true
            ),
            array(
                array(
                    'test.jpg',
                    'test.jpg',
                    'test.png',
                    'test.nope',
                ),
                array('jpg', 'png' => false, 'nope' => true),
                false
            ),
        );
    }

    public function testAddExtension()
    {
        $processor = new FileExtensionProcessor('none');
        $this->assertInstanceOf(
            'Indigo\\Backup\\Processor\\FileExtensionProcessor',
            $processor->addExtension('nope')
        );
    }

    /**
     * @dataProvider provider
     */
    public function testExtension(array $files, $ext, $blacklist)
    {
        $processor = new FileExtensionProcessor($ext, $blacklist);
        $files = $processor->process($files);

        foreach ($processor->getExtensions() as $e => $b) {
            if ($b) {
                $this->assertNotContains('test.' . $e, $files);
            } else {
                $this->assertContains('test.' . $e, $files);
            }
        }
    }
}
