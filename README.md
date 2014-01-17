# Indigo Backup

[![Build Status](https://travis-ci.org/indigophp/backup.png?branch=develop)](https://travis-ci.org/indigophp/backup)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/indigophp/backup/badges/quality-score.png?s=2cf8773e5649fb85fbd3d21e05f77446cd7c0efe)](https://scrutinizer-ci.com/g/indigophp/backup/)
[![Code Coverage](https://scrutinizer-ci.com/g/indigophp/backup/badges/coverage.png?s=760538c10f947ddd297fd3d36ca20fc3ad7007a7)](https://scrutinizer-ci.com/g/indigophp/backup/)

**Create backup from any source (files, databases, stream output, etc) and store at any destination.**

## Install

Via Composer

``` json
{
    "require": {
        "indigophp/dumper": "@stable"
    }
}
```

## Usage

``` php
// See Dumper at https://github.com/indigophp/dumper
$source = new Indigo\Backup\Source\DatabaseSource($dumper);

// See Flysystem at https://github.com/php-loep/flysystem
$destination = new FlysystemDestination($flysystem);

$backup = new Indigo\Backup\Backup($source, $destination);

$backup->run();
```

## Third-party

Current sources and destinations use the following libraries:

* [php-loep/flysystem](https://github.com/php-loep/flysystem)
* [IndigoPHP/Dumper](https://github.com/indigophp/dumper)

## Testing

``` bash
$ phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/indigophp/backup/blob/develop/CONTRIBUTING.md) for details.

## Credits

- [Márk Sági-Kazár](https://github.com/sagikazarmark)
- [All Contributors](https://github.com/indigophp/backup/contributors)


## License

The MIT License (MIT). Please see [License File](https://github.com/indigophp/backup/blob/develop/LICENSE) for more information.