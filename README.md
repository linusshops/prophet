# prophet
Magento module testing

Objective: provide a test harness that does not require modifying Magento core,
and allows testing by module.  The only thing that should exist is config files,
and the tests themselves.

##Installation

Prophet should be installed via composer.  It is recommended to install it globally.

_composer global require linusshops/prophet_

##Commands

_prophet_: Run tests for modules defined in prophet.json.

_prophet validate_: Confirm that all modules in prophet.json are testable.

_prophet init_: Initialize any modules in prophet.json that do not have the expected test structure.
