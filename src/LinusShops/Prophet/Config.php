<?php
/**
 * 
 *
 * @author Sam Schmidt
 * @date 2015-04-22
 * @company Linus Shops
 */

namespace LinusShops\Prophet;


use LinusShops\Prophet\Exceptions\InvalidConfigException;
use LinusShops\Prophet\Exceptions\ProphetException;

class Config
{
    private $modules;
    private $prophetFilePath;

    /**
     * Loads a prophet.json, replacing whatever is currently held by this object
     * @param string $prophet parsed JSON
     */
    public function __construct($prophet)
    {
        if (!is_array($prophet)) {
            throw new InvalidConfigException('Config::loadConfig expects an array.');
        }

        if (isset($prophet['modules'])) {
            foreach ($prophet['modules'] as $definition) {
                $module = new Module(
                    $definition['name'],
                    $definition['path'],
                    isset($definition['options']) ? $definition['options'] : array()
                );

                $this->modules[$module->getName()] = $module;
            }
        }
    }

    public function getModule($name)
    {
        return $this->modules[$name];
    }

    public function getModuleList()
    {
        return $this->modules;
    }

    public function hasModules()
    {
        return empty($this->modules);
    }

    public function writeModule(Module $module)
    {
        $this->modules['modules'][] = array(
            'name'=>$module->getName(),
            'path'=>$module->getPath()
        );

        file_put_contents(
            $this->getProphetFilePath(),
            json_encode($this->modules)
        );

        //Config state has changed, update repository version
        ConfigRepository::setConfig($this);
    }

    /**
     * @return mixed
     */
    public function getProphetFilePath()
    {
        return $this->prophetFilePath==null ?
            'prophet.json'
            : $this->prophetFilePath;
    }

    /**
     * @param mixed $prophetFilePath
     */
    public function setProphetFilePath($prophetFilePath)
    {
        $this->prophetFilePath = $prophetFilePath;
    }
}
