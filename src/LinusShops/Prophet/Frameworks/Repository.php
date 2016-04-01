<?php
/**
 *
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2016-04-01
 */

namespace LinusShops\Prophet\Frameworks;

use LinusShops\Prophet\Framework;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class Repository
{
    private static $instance = null;
    
    private $frameworks = [];

    private function __construct()
    {
        $this->load();
    }

    private function load()
    {
        foreach ($this->getFrameworkDirectories() as $dir) {
            $frameworkConfig = include($dir.'/framework.php');
            $this->frameworks[] = new Framework($frameworkConfig);
        }
    }

    /**
     * @return Finder
     */
    private function getFrameworkDirectories()
    {
        $dirs = scandir($this->getFrameworkPath());
        $cleanDirs = array();
        foreach ($dirs as $dir) {
            if (!in_array($dir, array('.', '..')) && is_dir($this->getFrameworkPath().'/'.$dir)) {
                $cleanDirs[] = $this->getFrameworkPath().'/'.$dir;
            }
        }

        return $cleanDirs;
    }

    private function getFrameworkPath()
    {
        return PROPHET_ROOT_DIR.'/frameworks';
    }
    
    public static function get()
    {
        if (empty($instance)) {
            self::$instance = new Repository();
        }

        return self::$instance;
    }

    public function getFrameworks()
    {
        return $this->frameworks;
    }
}
