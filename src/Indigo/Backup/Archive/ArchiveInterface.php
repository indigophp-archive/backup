<?php

namespace Indigo\Backup\Archive;

interface ArchiveInterface
{
	public function compress();

	public function decompress();
}
