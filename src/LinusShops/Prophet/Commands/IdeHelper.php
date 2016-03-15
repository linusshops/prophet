<?php
/**
 *
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2016-03-15
 */

namespace LinusShops\Prophet\Commands;

use LinusShops\Contexts\Web;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class IdeHelper extends Command
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
        return [
            new \ReflectionClass('LinusShops\Contexts\Web')
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
