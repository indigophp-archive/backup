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
 * Local Destination Test
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class LocalDestinationTest extends DestinationTest
{
    public function setUp()
    {
        $this->destination = new LocalDestination(sys_get_temp_dir() . '/' . uniqid('backup_'));
    }
}
