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

use Flysystem\Filesystem;
use Flysystem\Adapter\Local as Adapter;

/**
 * Flysystem Destination Test
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class FlysystemDestinationTest extends DestinationTest
{
    public function setUp()
    {
    	$filesystem = new Filesystem(new Adapter(sys_get_temp_dir()));
        $this->destination = new FlysystemDestination($filesystem);
    }
}
