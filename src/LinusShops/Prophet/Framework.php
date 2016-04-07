<?php
/**
 *
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2016-04-01
 */

namespace LinusShops\Prophet;

use Symfony\Component\Filesystem\Filesystem;

class Framework
{
    private $config = array();
    
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param Module $module
     * @return boolean
     */
    public function validateModule(Module $module)
    {
        return $this->validatePath($module->getPath());
    }

    public function validatePath($path)
    {
        $valid = true;

        if (isset($this->config['validation'])) {
            $valid = $this->validateFiles($path);
        }

        return $valid;
    }

    public function validateFiles($basePath)
    {
        $valid = true;

        if (isset($this->config['validation']['files'])) {
            foreach ($this->config['validation']['files'] as $file) {
                $path = $basePath.'/'.$file;
                if (!is_file($path)) {
                    $valid = false;
                    break;
                }
            }
        }

        return $valid;
    }

    public function getName()
    {
        return $this->config['name'];
    }

    public function init()
    {
        $this->initDirs();
        $this->initFiles();
    }

    protected function initDirs()
    {
        if (!isset($this->config['init']['dirs'])) {
            return;
        }

        $fs = new Filesystem();
        $fs->mkdir($this->config['init']['dirs']);
    }

    protected function initFiles()
    {
        if (!isset($this->config['init']['files'])) {
            return;
        }

        $fs = new Filesystem();
        foreach ($this->config['init']['files'] as $filename => $contents) {
            $fs->dumpFile($filename, $contents);
        }
    }

    public function getIdeHelperClasses()
    {
        return isset($this->config['ideHelperClasses']) ? $this->config['ideHelperClasses'] : [];
    }

    public function getPath()
    {
        return PROPHET_ROOT_DIR.'/frameworks/'.$this->getName();
    }
}
