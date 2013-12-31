<?php
/*
 * This file is part of the Indigo Backup package.
 *
 * (c) IndigoPHP Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Backup\Processor;

interface ProcessorInterface
{
    /**
     * Process list of files
     *
     * @param  array  $files
     * @return array List of files
     */
    public function process(array $files);
}
