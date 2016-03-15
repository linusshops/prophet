# prophet

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

As Magento was never designed with testing in mind, certain classes and states are
very difficult to test. Prophet solves this by creating a custom class mechanism
that essentially creates class rewrites and overrides that will only be used in
the test execution context, and NEVER during normal execution.  This ensures that
your testing system can never adversely affect your site when not executing via Prophet.

##Impetus

As mentioned above, Magento was never designed with unit testing in mind (and according to
the core team, does not have automated tests to this day).  This problem has been tackled
before by other packages, but all of them require installing a module to handle testing, or
modifying core.

This is non-ideal, as it is injecting code that could potentially cause conflicts. Ideally,
a testing system should not have any chance of changing the functionality of the system under
test unless it is being executed in the test context.

Prophet seeks to solve this problem by focusing on testing on a module basis, and by focusing
on the Firegento/Composer Magento ecosystem.  Prophet expects tests to be written on a module
by module basis, and seeks to execute them with the modules in isolation.  Since test code
is confined to a test directory of a module, it is not ever going to be invoked by Magento
under normal execution conditions.

Prophet's footprint becomes only the config files and the test cases the developer
creates- it adds nothing to Magento core or the local code pool.

##Installation

Prophet should be installed via composer.  It is recommended to install it globally.

`composer global require linusshops/prophet`

For best results, your Magento installation should be managed with magento-composer-installer.

##Commands

Prophet must be executed from the command line at the Magento root.

`prophet`: Run PHPUnit tests for modules defined in prophet.json (this is an alias of `scry:phpunit`).

`prophet validate`: Confirm that all modules in prophet.json are testable.

`prophet show`: Display all modules in prophet.json, and their available tests.

`prophet init`: Initialize any modules in prophet.json that do not have the expected test structure.

`prophet analyze`: Search your vendor directory for testable modules, and attempt to create a prophet.json

`prophet inspect`: Bootstrap Magento and then start PsySH in that context. Useful for REPL testing.

`prophet list`: View all commands

`prophet scry:phpunit`: Same as the basic `prophet` invocation.

`prophet scry:behat`: Run functional tests using Behat. Requires phantomjs and selenium-server.

`prophet generate:ide-helper`: Create a hint file to include in your project for
autocomplete of contexts and functions provided when executing tests via Prophet.

`prophet help [command]`: View help for a specific command.

For troubleshooting, you can increase verbosity with `-vvv`, as with any symfony/console app.

##Debug Helper

Prophet has [PsySh](http://psysh.org) support built in for inspecting variables. You can
break into Psysh with `PD::inspect($context)`. $context can be an array of variables, or
just one variable. Psysh `list` command will show you all the variables in your context.

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
will still use the Varien autoloader; the custom autoloaders just have priority.

## Custom Classes

Prophet includes some classes for testing that can be used in place of the regular classes.
See the overrides dir. Generally, they extend the existing class, and rewrite methods that are
problematic for testing, without changing the expected interface.

These classes can be instantiated in your tests using the Classes helper.  Remember, Prophet executes
in the same context as your tests, so therefore all of its helpers and classes are available in your
test cases!

You can also include custom test classes by adding them to a tests/classes directory. Prophet
injects a general autoloader that will look in this directory for a class before anything else.

Prophet loaders have priority over the Varien autoloader.  Essentially, this allows you to do
*testing-specific rewrites and overrides*, without any risk of it being used in normal execution.

##Controller test example

```
public function testRecentAction()
{
    $request = PD::getRequest();
    $request->setMethod('GET');

    $response = PD::getResponse();

    $controller = new Linus_Garage_SampleController($request, $response);
    $controller->indexAction();
    $headers = $controller->getResponse()->getHeaders();
    $this->assertEquals($headers[0]['name'], 'Content-Type');
    $this->assertEquals($headers[0]['value'], 'application/json');

    $body = $controller->getResponse()->getBody();
    $json = json_decode($body, true);
    $this->assertTrue($json !== false);

    $this->assertArrayHasKey('orders', $json);
}
```

## Behat

http://dannorth.net/whats-in-a-story/

Prophet can run functional tests using Behat and Mink.

Prophet does not configure any browsers or drivers; this is all configured
via your behat.yml file. If you have not already configured something, the 
[elgalu/selenium](https://github.com/elgalu/docker-selenium) docker container
 provides a preconfigured Selenium hub with Firefox and Chrome.
 
Recommended to use Parallels instead of Virtualbox if using docker-machine, unless you like tests running as slow as molasses. Also recommended to give the docker-machine at least 4GB of RAM to satiate Chrome's hunger for memory.

https://github.com/Parallels/docker-machine-parallels/
 
 TODO: add instructions on setting up elgalu container with prophet and behat, setting up docker-machine- write script
 
 ```bash
 #Create the docker container (assuming you already pulled elgalu/selenium
docker run --rm --name=grid --add-host='develop.vagrant.dev:192.168.80.80' -p 4444:24444 -p 5920:25900 \
   -v /dev/shm:/dev/shm -e VNC_PASSWORD=hola elgalu/selenium
 
 #View the browser live during a test
open vnc://:hola@$(docker-machine ip default):5920
 ```

Prophet makes all of the contexts from [linusshops/behat-contexts](https://github.com/linusshops/behat-contexts) available to Behat via its custom loaders. See the behat-contexts repository for more information. Remember that you can generate an ide helper file to give you autocomplete (`prophet generate:ide-helper`).

## Author

[Samuel Schmidt](https://github.com/dersam)

## Contributors

[Dane MacMillan](https://github.com/danemacmillan)

## License

This tool was created by Linus Shops and licensed under the [MIT License](http://opensource.org/licenses/MIT).
