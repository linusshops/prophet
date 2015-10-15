<?php
/**
 * Provides additional context helpers when using Mink in Behat via Prophet
 *
 * Your feature contexts should extend this.
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @date 2015-10-15
 * @company Linus Shops
 */

namespace LinusShops\Prophet\Context;


use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\WebAssert;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Testwork\Tester\Result\TestResult;

class ProphetContext extends MinkContext
{
    public function waitFor ($lambda, $wait = 60)
    {
        for ($i = 0; $i < $wait; $i++)
        {
            try {
                if ($lambda($this)) {
                    return true;
                }
            } catch (Exception $e) {
                // do nothing
            }

            sleep(1);
        }

        throw new Exception(
            "Step timed out after $wait seconds"
        );
    }

    /**
     * https://blogs.library.ucsf.edu/ckm/2014/05/14/see-your-failures-taking-screenshots-with-phantomjs-running-behat-tests/
     * @AfterStep
     * @param AfterStepScope $event
     */
    public function takeScreenshotAfterFailedStep(AfterStepScope $event)
    {
        $result = $event->getTestResult();
        if (!$result->isPassed()) {
            if ($this->getSession()->getDriver() instanceof Selenium2Driver) {
                $stepText = $event->getStep()->getText();
                $fileTitle = preg_replace("#[^a-zA-Z0-9\._-]#", '', $stepText);
                $fileName = '/tmp' . DIRECTORY_SEPARATOR . $fileTitle . '.png';
                $screenshot = $this->getSession()->getDriver()->getScreenshot();
                file_put_contents($fileName, $screenshot);
                print "Screenshot for '{$stepText}' placed in {$fileName}\n";
            }
        }
    }

    /**
     * Set the size of the window
     *
     * @Given /^the viewport has width "(?P<width>[^"]+)" and height "(?P<height>[^"]+)"$/
     */
    public function setViewportSize($width, $height)
    {
        $this->getSession()->getDriver()->resizeWindow($width, $height);
    }

    /**
     * @Given /^I wait "([^"]*)" seconds$/
     */
    public function iWaitSeconds($seconds)
    {
        sleep($seconds);
    }

    /**
     * @Given /^I wait until selector "([^"]*)" exists$/
     */
    public function iWaitUntilISee($selector)
    {
        /** @var $context ProphetContext */
        $this->waitFor(function($context) use ($selector)
        {
            /** @var WebAssert $session */
            $session = $context->assertSession();
            try {
                $session->elementExists('css', $selector, $context->getSession()->getPage());
            } catch (ElementNotFoundException $e) {
                return false;
            }

            return true;
        }, 10);
    }
}
