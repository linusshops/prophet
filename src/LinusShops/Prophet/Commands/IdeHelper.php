<?php
/**
 *
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2016-03-15
 */

namespace LinusShops\Prophet\Commands;

use LinusShops\Contexts\Web;
use LinusShops\Prophet\Command;
use LinusShops\Prophet\Framework;
use LinusShops\Prophet\Frameworks\Repository;
use LinusShops\Prophet\Injector;
use LinusShops\Prophet\ProphetCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class IdeHelper extends ProphetCommand
{
    protected function configure()
    {
        $this
            ->setName('generate:ide-helper')
            ->setDescription('Generate an IDE helper file for autocomplete')
            ->addOption(
                'path',
                'p',
                InputOption::VALUE_REQUIRED,
                'Path to the helper file',
                '.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $classesByNamespace = [];

        //Magento classes need to be available to generate helper for override classes.
        Injector::bootMagento();

        /** @var \ReflectionClass $class */
        foreach ($this->getClassesToGenerate() as $class) {
            $namespace = $class->getNamespaceName();
            if (!isset($classesByNamespace[$namespace])) {
                $classesByNamespace[$namespace] = [];
            }

            $classesByNamespace[$namespace][] = $class;
        }

        file_put_contents(
            $input->getOption('path').'/__prophet_ide_helper.php',
            $this->buildIdeHelper($classesByNamespace)
        );
    }

    public function getClassesToGenerate()
    {
        return array_merge(
            [],
            $this->getFrameworkClassesToGenerate(),
            $this->getOverrideClasses()
        );
    }

    public function getFrameworkClassesToGenerate()
    {
        $frameworks = Repository::get()->getFrameworks();
        $classes = [];

        /** @var Framework $framework */
        foreach ($frameworks as $framework) {
            foreach ($framework->getIdeHelperClasses() as $c) {
                require($framework->getPath().'/vendor/autoload.php');
                $classes[] = new \ReflectionClass($c);
            }
        }

        return $classes;
    }

    public function getOverrideClasses()
    {
        return [
            new \ReflectionClass('LinusShops\Prophet\Injectable\Overrides\Request'),
            new \ReflectionClass('LinusShops\Prophet\Injectable\Overrides\Response')
        ];
    }

    public function buildIdeHelper($classesByNamespace)
    {
        $fileContents = "<?php \n die('This file is for autocomplete only and should not be included');\n";

        foreach ($classesByNamespace as $namespace => $classes) {
            ob_start();
            include(PROPHET_ROOT_DIR.'/src/LinusShops/Prophet/Templates/ide_helper_namespace.php');
            $fileContents .= ob_get_clean();
        }

        return $fileContents;
    }

    public function makeParameterString(\ReflectionMethod $method)
    {
        $parameters = array();

        foreach ($method->getParameters() as $parameter) {
            $parameters[] = '$'.$parameter->name;
        }

        return implode(',', $parameters);
    }
}
