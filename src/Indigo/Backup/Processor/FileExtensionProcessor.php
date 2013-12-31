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

class FileExtensionProcessor implements ProcessorInterface
{
    protected $extensions = array();

    protected $blacklist = true;

    public function __construct($ext, $blacklist = true)
    {
        $this->blacklist = $blacklist;
        $this->addExtension($ext);
    }

    public function addExtension($ext, $blacklist = null)
    {
        if (is_array($ext)) {
            foreach ($ext as $e => $b) {
                if (is_int($e)) {
                    $this->addExtension($b);
                } else {
                    $this->addExtension($e, $b);
                }
            }
        } else {
            is_null($blacklist) and $blacklist = $this->blacklist;
            $this->extensions[$ext] = $blacklist;
        }

        return $this;
    }

    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * {@inheritdoc}
     */
    public function process(array $files)
    {
        // PHP 5.3 compliance
        $ext       = $this->extensions;
        $blacklist = $this->blacklist;

        return array_filter($files, function ($file) use ($ext, $blacklist) {
            $file = pathinfo($file, PATHINFO_EXTENSION);

            return isset($ext[$file]) ? ! $ext[$file] : $blacklist;
        });
    }
}
