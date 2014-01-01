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
use Psr\Log\NullLogger;
use Flysystem\Filesystem;
use Flysystem\Adapter\Local as Adapter;

class DatabaseSource extends AbstractSource implements CleanSourceInterface
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

    /**
     * Temporary filesystem
     *
     * @var Filesystem
     */
    protected $tmp;

    public function __construct(array $options, array $settings = array())
    {
        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);
        $this->options = $resolver->resolve($options);

        $this->tmp = new Filesystem(new Adapter($this->options['tmp']));

        $resolver = new OptionsResolver();
        $this->setDefaultSettings($resolver, true);
        $this->settings = $resolver->resolve($settings);

        $this->logger = new NullLogger;
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
            'type' => array('mysql', 'pgsql', 'dblib'),
        ));

        $resolver->setAllowedTypes(array(
            'username' => 'string',
            'password' => 'string',
            'host'     => 'string',
            'type'     => 'string',
        ));

        $resolver->setNormalizers(array(
            'tmp' => function (Options $option, $value) {
                return rtrim($value) . '/';
            },
        ));
    }

    /**
     * Set default dump settings
     *
     * @param OptionsResolverInterface $resolver
     * @param boolean                  $global
     */
    protected function setDefaultSettings(OptionsResolverInterface $resolver, $global = false)
    {
        if ($global) {
            $resolver->setDefaults(array(
                'include-tables'             => array(),
                'exclude-tables'             => array(),
                'compress'                   => 'None',
                'no-data'                    => false,
                'add-drop-database'          => false,
                'add-drop-table'             => false,
                'single-transaction'         => false,
                'lock-tables'                => false,
                'add-locks'                  => true,
                'extended-insert'            => true,
                'disable-foreign-keys-check' => false,
            ));
        } else {
            $resolver->setDefaults($this->settings);
        }

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
            'compress' => function (Options $option, $value) {
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

        if (is_array($db)) {
            foreach ($db as $d => $settings) {
                if (is_int($d)) {
                    $d = $settings;
                    $settings = array();
                }

                $this->databases[$d] = $resolver->resolve($settings);
            }
        } else {
            $this->databases[$db] = $resolver->resolve($settings);
        }

        return $this;
    }

    /**
     * Add excluded database
     *
     * @param  string         $db Database name
     * @return DatabaseSource
     */
    public function excludeDatabase($db)
    {
        if (is_array($db)) {
            foreach ($db as $d) {
                $this->databases[$d] = false;
            }
        } else {
            $this->databases[$db] = false;
        }


        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function backup()
    {
        $databases = array_filter($this->databases, function ($var) {
            return $var !== false;
        });

        // Get all databases if none or only excludes defined
        // (This only works with SUPER access)
        if (empty($databases)) {
            $this->logger->debug('No database included, backing up all');
            $this->getDatabases();
        }

        $result = array();

        foreach ($this->databases as $name => $settings) {
            if ($settings === false) {
                $this->logger->debug('Skipping database: ' . $name, compact('name'));
                continue;
            }

            $this->logger->debug('Backing up database: ' . $name, compact('name', 'settings'));
            $dump = new Mysqldump(
                $name,
                $this->options['username'],
                $this->options['password'],
                $this->options['host'],
                $this->options['type'],
                $settings
            );

            $path = $this->options['tmp'] . $name . '.sql';
            $dump->start($path);
            $result[] = $path . $this->getExt($settings);
        }

        return $this->result = $result;
    }

    /**
     * {@inheritdoc}
     */
    public function cleanup()
    {
        $this->tmp->deleteDir('');
    }

    /**
     * Get file extension based on compression
     *
     * @return string
     */
    private function getExt(array $settings)
    {
        switch ($settings['compress']) {
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

    /**
     * Fetch databases from DB
     *
     * This only works with SUPER access on MySQL
     */
    private function getDatabases()
    {
        if ($this->options['type'] == 'mysql') {
            $pdo = new \PDO(
                'mysql:host=' . $this->options['host'] . ';',
                $this->options['username'],
                $this->options['password']
            );

            foreach ($pdo->query('SHOW DATABASES') as $db) {
                $db = $db['Database'];

                if (array_key_exists($db, $this->databases)) {
                    continue;
                }

                $this->includeDatabase($db);
            }
        } else {
            $msg = 'Backing up all databases is not yet implemented in the given DB type: ' . $this->options['type'];
            throw new \Exception($msg);
        }
    }
}
