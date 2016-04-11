<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given /^I am in a magento root$/
     */
    public function iAmInAMagentoRoot()
    {
        
    }

    /**
     * @When /^I run the show command$/
     */
    public function iRunTheShowCommand()
    {
        throw new \Behat\Behat\Tester\Exception\PendingException();
    }

    /**
     * @Then /^I should see the no config found error$/
     */
    public function iShouldSeeTheNoConfigFoundError()
    {
        throw new \Behat\Behat\Tester\Exception\PendingException();
    }

    /**
     * @Then /^I should see the sample module with phpunit enabled$/
     */
    public function iShouldSeeTheSampleModuleWithPhpunitEnabled()
    {
        throw new \Behat\Behat\Tester\Exception\PendingException();
    }
}
