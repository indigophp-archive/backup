<?php

namespace Indigo\Backup\Destination;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use Flysystem\Filesystem;
use Flysystem\Adapter\Ftp as Adapter;

class FtpDestination implements DestinationInterface
{
    protected $ftp;
    protected $connection;
    protected $options = array();

    public function __construct(array $options = array())
    {
        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);

        $options = $resolver->resolve($options);

        $ftp = new Filesystem(new Adapter($options));

        $dir = '';
        foreach (explode('/', trim($options['path'])) as $path) {
            $dir .= $path . '/';

            if ($ftp->has($dir)) {
                continue;
            }

            $ftp->createDir($dir);
        }

        $this->options = $options;
        $this->ftp = $ftp;
    }

    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('username', 'password', 'host', 'root', 'path'));

        $resolver->setDefaults(array(
            'port'    => 21,
            'passive' => true,
            'ssl'     => false,
            'timeout' => 30
        ));

        $resolver->setAllowedTypes(array(
            'username' => 'string',
            'password' => 'string',
            'host'     => 'string',
            'root'     => 'string',
            'path'     => 'string',
            'port'     => 'integer',
            'passive'  => 'bool',
            'ssl'      => 'bool',
            'timeout'  => 'integer',
        ));

        $resolver->setNormalizers(array(
            'root' => function (Options $options, $value) {
                return rtrim($value, '/') . '/';
            },
            'path' => function (Options $options, $value) {
                return trim($value) . '/';
            },
        ));
    }

    public function put(array $files)
    {
        foreach ($files as $file) {
            $name = basename($file);
            $file = fopen($file, 'r+');
            $this->ftp->putStream($this->options['path'] . $name, $file);
            fclose($file);
        }
    }
}
