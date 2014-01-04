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

use Indigo\Dumper\Dumper;

/**
 * Database Source Test
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class DatabaseSourceTest extends SourceTest
{
    public function setUp()
    {
        $dumper = \Mockery::mock('Indigo\\Dumper\\Dumper', function ($mock) {
            $mock->shouldReceive('getStore')
                ->andReturn(\Mockery::mock('Indigo\\Dumper\\Store\\FileStore', function ($mock) {
                    $mock->shouldReceive('getFile')
                        ->andReturn(tempnam(sys_get_temp_dir(), ''));
                }));

            $mock->shouldReceive('getDatabase')->andReturn('test');
            $mock->shouldReceive('dump')->andReturn(true);
        });


        $this->source = new DatabaseSource($dumper);
    }

    public function tearDown()
    {
        \Mockery::close();
    }
}
