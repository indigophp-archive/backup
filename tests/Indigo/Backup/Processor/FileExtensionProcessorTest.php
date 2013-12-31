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
    public function blacklist_provider()
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
            ),
            array(
                array(
                    'test.jpg',
                    'test.jpg',
                    'test.png',
                    'test.nope',
                ),
                'png',
            ),
            array(
                array(
                    'test.jpg',
                    'test.jpg',
                    'test.png',
                    'test.nope',
                ),
                'nope',
            ),
        );
    }

    public function whitelist_provider()
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
            ),
            array(
                array(
                    'test.jpg',
                    'test.jpg',
                    'test.png',
                    'test.nope',
                ),
                'png',
            ),
            array(
                array(
                    'test.jpg',
                    'test.jpg',
                    'test.png',
                    'test.nope',
                ),
                'nope',
            ),
        );
    }

    /**
     * @dataProvider blacklist_provider
     */
    public function testBlacklist(array $files, $ext)
    {
        $processor = new FileExtensionProcessor($ext);
        $files = $processor->process($files);

        foreach (array('jpg', 'png', 'nope') as $e) {
            if ($ext == $e) {
                $this->assertNotContains('test.' . $e, $files);
            } else {
                $this->assertContains('test.' . $e, $files);
            }
        }
    }

    /**
     * @dataProvider whitelist_provider
     */
    public function testWhitelist(array $files, $ext)
    {
        $processor = new FileExtensionProcessor($ext, false);
        $files = $processor->process($files);

        foreach (array('jpg', 'png', 'nope') as $e) {
            if ($ext == $e) {
                $this->assertContains('test.' . $e, $files);
            } else {
                $this->assertNotContains('test.' . $e, $files);
            }
        }
    }
}
