<?php
/*
 * This file is part of the Indigo Backup package.
 *
 * (c) IndigoPHP Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Backup\Source;

use Clouddueling\Mysqldump\Mysqldump;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

class DatabaseSource implements SourceInterface, CleanSourceInterface
{
    /**
     * Connection options
     *
     * @var array
     */
    protected $options = array();

    /**
     * Dump settings
     *
     * @var array
     */
    protected $settings = array();

    /**
     * List of databases to operate on
     *
     * @var array
     */
    protected $databases = array();

    /**
     * Result of files
     *
     * @var array
     */
    protected $result = array();

    public function __construct(array $options, array $settings = array())
    {
        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);
        $this->options = $resolver->resolve($options);

        // Create temporary directory
        mkdir($this->options['tmp'], 0777, true);

        $resolver = new OptionsResolver();
        $this->setDefaultSettings($resolver);
        $this->settings = $resolver->resolve($settings);
    }

    /**
     * Set default connection options
     *
     * @param OptionsResolverInterface $resolver
     */
    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('username', 'password'));

        $resolver->setDefaults(array(
            'host' => 'localhost',
            'type' => 'mysql',
            'tmp'  => sys_get_temp_dir() . '/' . uniqid('backup_'),
        ));

        $resolver->setAllowedValues(array(
            'type' => array('mysql', 'pgsql', 'sqlite'),
        ));

        $resolver->setAllowedTypes(array(
            'username' => 'string',
            'password' => 'string',
            'host'     => 'string',
            'type'     => 'string',
        ));

        $resolver->setNormalizers(array(
            'tmp' => function (Options $options, $value) {
                return rtrim($value) . '/';
            },
        ));
    }

    /**
     * Set default dump settings
     *
     * @param OptionsResolverInterface $resolver
     */
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
            'disable-foreign-keys-check' => false,
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
            'disable-foreign-keys-check' => 'bool',
        ));

        $resolver->setNormalizers(array(
            'compress' => function (Options $options, $value) {
                return strtoupper($value);
            },
        ));
    }

    /**
     * Add included database
     *
     * @param  string         $db       Database name
     * @param  array          $settings Dump settings
     * @return DatabaseSource
     */
    public function includeDatabase($db, array $settings = array())
    {
        $resolver = new OptionsResolver();
        $this->setDefaultSettings($resolver);

        $this->databases[$db] = $resolver->resolve($settings);
        return $this;
    }

    /**
     * Add excluded database
     *
     * @param  string         $db       Database name
     * @return DatabaseSource
     */
    public function excludeDatabase($db)
    {
        $this->databases[$db] = false;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function backup()
    {
        // Get all databases if none or only excludes defined
        if (empty(array_filter($this->databases))) {
            if ($this->type == 'mysql') {
                $pdo = new \PDO('mysql:host=' . $this->options['host'] . ';', $this->options['username'], $this->options['password']);

                foreach ($pdo->query('SHOW DATABASES') as $db) {
                    if ( ! array_key_exists($db['Database'], $this->databases)) {
                        $this->databases[$db['Database']] = array();
                    }
                }
            } else {
                throw new \Exception('Backing up all databases is not yet implemented in the given DB type: ' . $this->options['type']);
            }
        }

        $result = array();

        // Dump databases
        foreach ($this->databases as $name => $settings) {
            if ($settings === false) {
                continue;
            }

            $settings = array_merge($this->settings, $settings);
            $dump = new Mysqldump($name, $this->options['username'], $this->options['password'], $this->options['host'], $this->options['type'], $settings);

            $path = $this->options['tmp'] . "$name.sql";
            $dump->start($path);
            $result[] = $path . $this->getExt();
        }

        return $this->result = $result;
    }

    /**
     * {@inheritdoc}
     */
    public function cleanup()
    {
        foreach ($this->result as $file) {
            unlink($file);
        }

        // Remove temp directory if empty
        @rmdir($this->options['tmp']);
    }

    /**
     * Get file extension based on compression
     *
     * @return string
     */
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
