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

class LocalDestination extends AbstractDestination
{
    /**
     * Base destination path
     *
     * @var string
     */
    protected $file;

    public function __construct($path)
    {
        $this->file = new Filesystem(new Adapter($path));
    }

    /**
     * {@inheritdoc}
     */
    public function put(array $files)
    {
        foreach ($files as $file) {
            $name = basename($file);
            $file = fopen($file, 'r+');
            $this->file->putStream($name, $file);
            fclose($file);
        }
    }
}
