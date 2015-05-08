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
