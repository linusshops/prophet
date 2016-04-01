<?php
/**
 *
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2016-04-01
 */

namespace LinusShops\Prophet;

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
}
