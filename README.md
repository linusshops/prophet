# prophet
Magento module testing

Objective: provide a test harness that does not require modifying Magento core,
and allows testing by module.  The only thing that should exist is config files,
and the tests themselves.

##Commands

_prophet scry_: Run tests for modules defined in prophet.json.
_prophet validate_: Confirm that all modules in prophet.json are testable.
