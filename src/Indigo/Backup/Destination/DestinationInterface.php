<?php

namespace Indigo\Backup\Destination;

interface DestinationInterface
{
	public function put(array $files);
}
