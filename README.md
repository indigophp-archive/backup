Indigo Backup
=============

[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/indigophp/backup/badges/quality-score.png?s=2cf8773e5649fb85fbd3d21e05f77446cd7c0efe)](https://scrutinizer-ci.com/g/indigophp/backup/)

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

// See other settings at https://github.com/clouddueling/mysqldump-php
$source = new DatabaseSource(array(
	'host'     => 'localhost',
	'username' => 'root',
	'password' => 'secret',
	'type'     => 'mysql',
), array(
	'compress' => 'GZIP',
));

// Create directory if not exists
$destination = new LocalDestination('/tmp/databases', true);

$backup = new Backup($source, $destination);

$destination = new FtpDestination(array(
	'host' => 'localhost',
	'username' => 'root',
	'password' => 'secret',
	'port' => 21,
	'root' => '/home/backup',
	'path' => date('YmdHis')
));

$backup->pushDestination($destination);

$backup->run();

```

Todo
----

* SFTP Destination
* Unit tests
* Reviewing current backup implementation
* Error handling of streams