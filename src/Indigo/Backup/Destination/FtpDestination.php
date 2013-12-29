<?php
/*
 * This file is part of the Indigo Backup package.
 *
 * (c) IndigoPHP Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Backup\Destination;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use Flysystem\Filesystem;
use Flysystem\Adapter\Ftp as Adapter;

class FtpDestination implements DestinationInterface
{
    /**
     * Filesystem object
     *
     * @var Filesystem
     */
    protected $ftp;

    /**
     * Connection options
     *
     * @var array
     */
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

    /**
     * Set default connection options
     *
     * @param OptionsResolverInterface $resolver
     */
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

    /**
     * {@inheritdoc}
     */
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