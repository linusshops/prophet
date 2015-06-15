# prophet
Magento module testing

[![Build Status](https://travis-ci.org/linusshops/prophet.svg)](https://travis-ci.org/linusshops/prophet)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/linusshops/prophet/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/linusshops/prophet/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/linusshops/prophet/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/linusshops/prophet/?branch=develop)

Objective: provide a test harness that does not require modifying Magento core,
and allows testing by module.  The only thing that should exist is config files,
and the tests themselves.

Prophet accomplishes this by finding Mage.php and the bootstrapping functions
for the Varien autoloader, and instantiates them. By doing this, it is able to
instantiate the Magento environment in which tests can be run. It then loads
PHPUnit and executes tests for the provided module list.

To ensure that the test context for each module's tests is clean, it creates
a subprocess of prophet to run the module tests.  This ensures that there is no
shared state between module test suites.

##Installation

Prophet should be installed via composer.  It is recommended to install it globally.

`composer global require linusshops/prophet`

##Commands

`prophet`: Run tests for modules defined in prophet.json.

`prophet validate`: Confirm that all modules in prophet.json are testable.

`prophet init`: Initialize any modules in prophet.json that do not have the expected test structure.

`prophet analyze`: Search your vendor directory for testable modules, and attempt to create a prophet.json

`prophet list`: View all commands

`prophet help [command]`: View help for a specific command.

##Events and Bootstrapping

Events can be listened for using symfony/event-dispatcher.

`prophet.premodule`: Fired right before the module test suite begins.
`prophet.postmodule`: Fired right after the module test suite is completed (regardless of success or failure).

```
$dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();

 $dispatcher->addListener('prophet.premodule', function (\Symfony\Component\EventDispatcher\Event $event) {
     echo "PREMODULE";
 });

 $dispatcher->addListener('prophet.premodule', function (\Symfony\Component\EventDispatcher\Event $event) {
     echo "POSTMODULE";
 });
```

Prophet will check for the existence of a tests/ProphetEvents.php file in your module. If it exists, it
will be included.  The check for this occurs immediately prior to the registration of custom autoloaders
and the firing of the premodule event.

This file is included, so it is executed within the same context as Prophet. It can serve as a module
bootstrap file, though it is not recommended as the premodule event should be used for this, or
the PHPUnit setUp functions.

##Autoloader

The Varien autoloader is not able to directly load controllers.  To allow these controllers to be
instantiated for testing, Prophet creates its own autoloader functions, and prepends them
on the autoloader stack.  This means that Prophet's autoloader has priority over the Varien autoloader.

This is due to the fact that the Varien autoloader dies if it can't instantiate the class.  Prophet
will fail over to the Varien autloader in the event it cannot find anything.

Eventually, this will be abstracted in such a way that any class can be intercepted by Prophet's autoloader.

## Custom Classes

Prophet includes some classes for testing that can be used in place of the regular classes.  See the overrides dir.

These classes can be instantiated in your tests using the Classes helper.

## Author

[Samuel Schmidt](https://github.com/dersam)

## Contributors

[Dane MacMillan](https://github.com/danemacmillan)

## License

This tool was created by Linus Shops and licensed under the [MIT License](http://opensource.org/licenses/MIT).
