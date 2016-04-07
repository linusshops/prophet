<?php
/**
 * Template for classes in the IDE helper
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2016-03-15
 */

/** @var \LinusShops\Prophet\Commands\IdeHelper $this */
/** @var \ReflectionClass[] $classes */
/** @var string $namespace */
?>

namespace <?= $namespace ?> {

<?php foreach ($classes as $class) : ?>
    class <?= $class->getShortName() ?> <?php if ($class->getParentClass()): ?>extends \<?= $class->getParentClass()->getName() ?> <?php endif; ?>
    {
        <?php foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) : ?>
        <?= $method->getDocComment() ?>
        public function <?= $method->getShortName() ?>(<?= $this->makeParameterString($method) ?>){}
        <?php endforeach ?>
    }
<?php endforeach ?>
}
