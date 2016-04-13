<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Symfony\Component\Process\Process;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    use \LinusShops\Contexts\Generic;

    protected $lastOutput;

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

    public function getMagentoPath()
    {
        return './testbed';
    }
    
    public function runCommand($command)
    {
        $process = new Process($command);
        $process->mustRun();
        return $process->getOutput();
    }

    /**
     * @When /^I run the show command$/
     */
    public function iRunTheShowCommand()
    {
        $this->lastOutput = $this->runCommand("./prophet show --path={$this->getMagentoPath()}");
    }

    /**
     * @Then /^I should see the sample module with phpunit enabled$/
     */
    public function iShouldSeeTheSampleModuleWithPhpunitEnabled()
    {
        $this->assertRegex('/phpunit/', $this->lastOutput);
    }
}
