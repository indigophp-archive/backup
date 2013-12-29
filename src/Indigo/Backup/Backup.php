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
use Indigo\Backup\Archive\ArchiveInterface;

class Backup
{
	protected $sources = array();
	protected $archives = array();
	protected $destinations = array();

	public function __construct(SourceInterface $source, DestinationInterface $destination, ArchiveInterface $archive = null)
	{
		$this->sources[] = $source;
		$this->archives[] = $archive;
		$this->destinations[] = $destination;
	}

	public function pushSource(SourceInterface $source, $prepend = false)
	{
		if ($prepend) {
			array_unshift($this->sources, $source);
		} else {
			array_push($this->sources, $source);
		}

		return $this;
	}

	public function pushDestination(DestinationInterface $destination, $prepend = false)
	{
		if ($prepend) {
			array_unshift($this->destinations, $destination);
		} else {
			array_push($this->destinations, $destination);
		}

		return $this;
	}

	public function run()
	{
		$files = array();

		foreach ($this->sources as $source) {
			$files = array_merge($files, $source->backup());
		}

		foreach ($this->destinations as $destination) {
			$destination->put($files);
		}

		foreach ($this->sources as $source) {
			if ($source instanceof CleanSourceInterface) {
				$source->cleanup();
			}
		}
	}
}
