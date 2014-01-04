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
use Indigo\Dumper\Store\FileStore;
use Psr\Log\NullLogger;

class DatabaseSource extends AbstractSource implements CleanSourceInterface
{
    /**
     * File
     *
     * @var string
     */
    protected $file;

    /**
     * Dumper object
     *
     * @var Dumper
     */
    protected $dumper;

    public function __construct(Dumper $dumper)
    {
        $this->setDumper($dumper);
        $this->logger = new NullLogger;
    }

    /**
     * Get Dumper object
     *
     * @return Dumper
     */
    public function getDumper()
    {
        return $this->dumps;
    }

    /**
     * Add Dumper object
     *
     * @param Dumper          $dumper
     * @return DatabaseSource
     */
    public function setDumper(Dumper $dumper)
    {
        if (!$dumper->getStore() instanceof FileStore) {
            throw new \InvalidArgumentException('Store should be an instance of FileStore');
        }

        $this->dumper = $dumper;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function backup()
    {
        $this->logger->debug('Backing up database: ' . $this->dumper->getDatabase());

        $this->dumper->dump();

        $this->file = $this->dumper->getStore()->getFile();

        return array($this->file);
    }

    /**
     * {@inheritdoc}
     */
    public function cleanup()
    {
        @unlink($this->file);
    }
}
