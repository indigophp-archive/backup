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

/**
 * Database Source Test
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class DatabaseSourceTest extends SourceTest
{
    public function setUp()
    {
        $options = array(
            'host'     => $GLOBALS['db_host'],
            'username' => $GLOBALS['db_username'],
            'password' => $GLOBALS['db_password'],
            'type'     => $GLOBALS['db_type'],
        );

        $this->source = new DatabaseSource($options);
        $this->source->includeDatabase($GLOBALS['db_name']);
    }

    public function testIncludeDatabase()
    {
        $this->assertInstanceOf(
            get_class($this->source),
            $this->source->includeDatabase('test')
        );

        $this->assertInstanceOf(
            get_class($this->source),
            $this->source->includeDatabase(array('test'))
        );

        $this->assertInstanceOf(
            get_class($this->source),
            $this->source->includeDatabase('test')
        );
    }

    public function testExcludeDatabase()
    {
        $this->assertInstanceOf(
            get_class($this->source),
            $this->source->excludeDatabase('no_test')
        );

        $this->assertInstanceOf(
            get_class($this->source),
            $this->source->excludeDatabase(array('no_test'))
        );

        $this->assertInstanceOf(
            get_class($this->source),
            $this->source->excludeDatabase('no_test')
        );
    }
}
