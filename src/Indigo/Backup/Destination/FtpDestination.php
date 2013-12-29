<?php

namespace Indigo\Backup\Destination;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

class FtpDestination implements DestinationInterface
{
    protected $ftp;
    protected $options = array();

    public function __construct(array $options)
    {
        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);

        $options = $resolver->resolve($options);

        $this->connect($options['host'], $options['port']);
        $this->login($options['user'], $options['pass']);
        $this->chdir($options['path']);
    }

    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('user', 'pass', 'host', 'path'));

        $resolver->setDefaults(array('port' => 21));

        $resolver->setAllowedTypes(array(
            'user' => 'string',
            'pass' => 'string',
            'host' => 'string',
            'path' => 'string',
            'port' => 'integer'
        ));

        $resolver->setNormalizers(array(
            'path' => function (Options $options, $value) {
                return rtrim($value) . '/';
            },
        ));
    }

    public function __destruct()
    {
        ftp_close($this->ftp);
    }

    protected function connect($host, $port)
    {
        $this->ftp = ftp_connect($host, $port);

        if ($this->ftp === false) {
            throw new \RuntimeException('Cannot connect to FTP server: ' . $host . ':' . $port);
        }

        ftp_pasv($this->ftp, true);
    }

    protected function login($user, $pass)
    {
        $success = ftp_login($this->ftp, $user, $pass);

        if ($success === false) {
            throw new \InvalidArgumentException('Invalid credentials');
        }
    }

    protected function chdir($path, $create = true)
    {
        if (!@ftp_chdir($this->ftp, $path)) {
            if ($create) {
                ftp_mkdir($this->ftp, $path);
                $this->chdir($path, false);
            } else {
                throw new \RuntimeException('Cannot change to dir: ' . $path);
            }
        }
    }

    public function put(array $files)
    {
        foreach ($files as $file) {
            ftp_put($this->ftp, basename($file), $file, FTP_BINARY);
        }
    }
}
