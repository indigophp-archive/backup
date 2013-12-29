Indigo Backup
=============

Create backup from any source (files, databases, stream output, etc) and store at any destination.

Current supported sources:
* Database (tested only with MySQL)
* Files (experimental, directories not supported)

Current supported destinations:
* Local
* Ftp

Usage
-----
```php
<?php

use Indigo\Backup\Backup;
use Indigo\Backup\Source\DatabaseSource;
use Indigo\Backup\Destination\LocalDestination;
use Indigo\Backup\Destination\FtpDestination;

$source = new DatabaseSource();
```