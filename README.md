# prophet
Magento module testing

[![Build Status](https://travis-ci.org/linusshops/prophet.svg)](https://travis-ci.org/linusshops/prophet)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/linusshops/prophet/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/linusshops/prophet/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/linusshops/prophet/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/linusshops/prophet/?branch=develop)

Objective: provide a test harness that does not require modifying Magento core,
and allows testing by module.  The only thing that should exist is config files,
and the tests themselves.

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
