<?php
/*
 * This file is part of the Indigo Backup package.
 *
 * (c) IndigoPHP Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Backup;

use Indigo\Backup\Source\SourceInterface;
use Indigo\Backup\Source\CleanSourceInterface;
use Indigo\Backup\Destination\DestinationInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Backup implements LoggerAwareInterface
{
    /**
     * Array of SourceInterface objects
     *
     * @var array
     */
    protected $sources = array();

    /**
     * Array of DestinationInterface objects
     *
     * @var array
     */
    protected $destinations = array();

    /**
     * Logger instance
     *
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(SourceInterface $source, DestinationInterface $destination)
    {
        $this->sources[] = $source;
        $this->destinations[] = $destination;

        $this->logger = new NullLogger;
    }

    /**
     * Push a new SourceInterface
     *
     * @param  SourceInterface $source
     * @param  boolean         $prepend Add the source to the beginning of the list
     * @return Backup
     */
    public function pushSource(SourceInterface $source, $prepend = false)
    {
        if ($prepend) {
            array_unshift($this->sources, $source);
        } else {
            array_push($this->sources, $source);
        }

        return $this;
    }

    /**
     * Push a new DestinationInterface
     *
     * @param  DestinationInterface $destination
     * @param  boolean              $prepend     Add the destiantion to the beginning of the list
     * @return Backup
     */
    public function pushDestination(DestinationInterface $destination, $prepend = false)
    {
        if ($prepend) {
            array_unshift($this->destinations, $destination);
        } else {
            array_push($this->destinations, $destination);
        }

        return $this;
    }

    /**
     * Run backup
     */
    public function run()
    {
        $this->logger->debug('Backup started');

        $files = array();

        // Get files from sources
        foreach ($this->sources as $source) {
            $files = array_merge($files, $source->backup());
        }

        // Send data to destinations
        foreach ($this->destinations as $destination) {
            $destination->save($files);
        }

        $this->cleanUp();

        $this->logger->info('Backup finished succesfully');

        return true;
    }

    /**
     * Clean up junk data
     */
    private function cleanUp()
    {
        foreach ($this->sources as $source) {
            if ($source instanceof CleanSourceInterface) {
                $source->cleanup();
            }
        }
    }

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
