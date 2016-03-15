<?php
/**
 *
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2016-03-15
 */

namespace LinusShops\Prophet\Commands;

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
        echo $this->makeHelperClass('LinusShops\Contexts\Web');
    }

    public function makeHelperClass($fullClassName)
    {
        $class = new \ReflectionClass($fullClassName);
        $classNamespace = $class->getNamespaceName();
        $className = $class->getName();
        $parentClassName = $class->getParentClass()->getName();

        $classHeader = <<<CLASS
namespace {$classNamespace};
class {$className} extends {$parentClassName}
{
CLASS;

        /** @var \ReflectionMethod $method */
        foreach ($class->getMethods() as $method) {
            $comment = $method->getDocComment();
            $parameters = $method->getParameters();
            $name = $method->getName();
        }

        return $classHeader.'}';
    }
}
