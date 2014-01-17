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

use League\Flysystem\Filesystem;

class FlysystemDestination extends AbstractDestination
{
    /**
     * Filesystem object
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Optional path under root
     *
     * @var string
     */
    protected $path;

    public function __construct(Filesystem $filesystem, $path = null)
    {
        $this->filesystem = $filesystem;
        $this->path = $path ? trim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR : null;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $files)
    {
        foreach ($files as $file) {
            $name = basename($file);
            $file = fopen($file, 'r+');
            $this->filesystem->putStream($this->path . $name, $file);
            fclose($file);
        }

        return true;
    }
}
