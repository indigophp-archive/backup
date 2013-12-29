<?php

namespace Indigo\Backup\Source;

use Clouddueling\Mysqldump\Mysqldump;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

class DatabaseSource implements SourceInterface, CleanupSourceInterface
{
    protected $user;
    protected $pass;
    protected $host;
    protected $type;
    protected $options = array();
    protected $settings = array();


    protected $databases = array();

    protected $result = array();

    public function __construct(array $options, array $settings = array())
    {
        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);

        $options = $resolver->resolve($options);

        $this->user = $options['user'];
        $this->pass = $options['pass'];
        $this->host = $options['host'];
        $this->type = $options['type'];

        $this->options = $options;
        mkdir($options['tmp'], 0777, true);

        $resolver = new OptionsResolver();
        $this->setDefaultSettings($resolver);

        $this->settings = $resolver->resolve($settings);
    }

    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('user', 'pass'));

        $resolver->setDefaults(array(
            'host' => 'localhost',
            'type' => 'mysql',
            'tmp'  => sys_get_temp_dir() . '/' . uniqid('backup_')
        ));

        $resolver->setAllowedValues(array(
            'type' => array('mysql', 'pgsql', 'sqlite'),
        ));

        $resolver->setAllowedTypes(array(
            'user' => 'string',
            'pass' => 'string',
            'host' => 'string',
            'type' => 'string',
        ));

        $resolver->setNormalizers(array(
            'tmp' => function (Options $options, $value) {
                return rtrim($value) . '/';
            },
        ));
    }

    protected function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'include-tables'             => array(),
            'exclude-tables'             => array(),
            'compress'                   => 'None',
            'no-data'                    => false,
            'add-drop-database'          => false,
            'add-drop-table'             => false,
            'single-transaction'         => true,
            'lock-tables'                => false,
            'add-locks'                  => true,
            'extended-insert'            => true,
            'disable-foreign-keys-check' => false
        ));

        $resolver->setAllowedValues(array(
            'compress' => array('NONE', 'GZIP', 'BZIP2'),
        ));

        $resolver->setAllowedTypes(array(
            'include-tables'             => 'array',
            'exclude-tables'             => 'array',
            'compress'                   => 'string',
            'no-data'                    => 'bool',
            'add-drop-database'          => 'bool',
            'add-drop-table'             => 'bool',
            'single-transaction'         => 'bool',
            'lock-tables'                => 'bool',
            'add-locks'                  => 'bool',
            'extended-insert'            => 'bool',
            'disable-foreign-keys-check' => 'bool'
        ));

        $resolver->setNormalizers(array(
            'compress' => function (Options $options, $value) {
                return strtoupper($value);
            },
        ));
    }

    public function includeDatabase($db, array $settings = array())
    {
        $resolver = new OptionsResolver();
        $this->setDefaultSettings($resolver);

        $this->databases[$db] = $resolver->resolve($settings);
        return $this;
    }

    public function excludeDatabase($db)
    {
        $this->databases[$db] = false;
        return $this;
    }

    public function backup()
    {
        if (empty(array_filter($this->databases))) {
            if ($this->type == 'mysql') {
                $pdo = new \PDO("mysql:host={$this->host};", $this->user, $this->pass);

                foreach ($pdo->query('SHOW DATABASES') as $db) {
                    if ( ! array_key_exists($db['Database'], $this->databases)) {
                        $this->databases[$db['Database']] = array();
                    }
                }
            } else {
                throw new \Exception('Backing up all databases is not yet implemented in the given DB type: ' . $this->type);
            }
        }

        $result = array();

        foreach ($this->databases as $name => $settings) {
            if ($settings === false) {
                continue;
            }

            $settings = array_merge($this->settings, $settings);
            $dump = new Mysqldump($name, $this->user, $this->pass, $this->host, $this->type, $settings);

            $path = $this->options['tmp'] . "$name.sql";
            $dump->start($path);
            $result[] = $path . $this->getExt();
        }

        return $this->result = $result;
    }

    public function cleanup()
    {
        foreach ($this->result as $file) {
            unlink($file);
        }

        // Remove temp directory if empty
        @rmdir($this->options['tmp']);
    }

    private function getExt()
    {
        switch ($this->settings['compress']) {
            case 'GZIP':
                return '.gz';
                break;
            case 'BZIP2':
                return '.bz2';
                break;
            default:
                break;
        }
    }
}
