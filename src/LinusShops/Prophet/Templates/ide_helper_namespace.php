<?php
/**
 * Template for classes in the IDE helper
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2016-03-15
 */

/** @var \ReflectionClass[] $classes */
/** @var string $namespace */
?>

namespace <?= $namespace ?> {

<?php foreach ($classes as $class) : ?>
    class <?= $class->getShortName() ?> extends \<?= $class->getParentClass()->getName() ?>
    {

    }
<?php endforeach ?>
}
