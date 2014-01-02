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

class FlysystemDestination extends AbstractDestination
{
    /**
     * Filesystem object
     *
     * @var Filesystem
     */
    protected $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $files)
    {
        foreach ($files as $file) {
            $name = basename($file);
            $file = fopen($file, 'r+');
            $this->filesystem->putStream($name, $file);
            fclose($file);
        }
    }
}
