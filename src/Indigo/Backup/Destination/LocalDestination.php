<?php

namespace Indigo\Backup\Destination;

class LocalDestination implements DestinationInterface
{
	protected $path;

	public function __construct($path, $create = false)
	{
		$path = rtrim($path) . '/';
		if ( ! is_dir($path) or ! is_writeable($path)) {
			if ($create) {
				mkdir($path, 0777, true);
			} else {
				throw new \InvalidArgumentException('Given path is either not a directory or not writeable.');
			}
		}

		$this->path = $path;
	}

	public function put(array $files)
	{
		foreach ($files as $file) {
			copy($file, $this->path . basename($file));
		}
	}
}
