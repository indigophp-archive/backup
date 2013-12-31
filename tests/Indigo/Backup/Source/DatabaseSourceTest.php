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
 * Backup Test
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class DatabaseSourceTest extends \PHPUnit_Framework_TestCase
{
	protected $options = array();

	public function setUp()
	{
		$this->options = array(
			'host' => $GLOBALS['db_host'],
			'username' => $GLOBALS['db_username'],
			'password' => $GLOBALS['db_password'],
			'type' => $GLOBALS['db_type'],
		);
	}

	public function testInstance()
	{
		$source = new DatabaseSource($this->options);

		$this->assertInstanceOf('Indigo\\Backup\\Source\\DatabaseSource', $source);
	}
}