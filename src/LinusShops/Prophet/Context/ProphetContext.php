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
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\ResponseTextException;
use Behat\Mink\WebAssert;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Testwork\Tester\Result\TestResult;

class ProphetContext extends MinkContext
{
    public function waitFor ($lambda, $wait = 10)
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

        throw new \Exception(
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
    public function iWaitForSelector($selector)
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

    /**
     * @Then /^I wait until I see "([^"]*)"$/
     */
    public function iWaitUntilISee($text)
    {
        $this->waitFor(function($context) use ($text)
        {
            /** @var WebAssert $session */
            $session = $context->assertSession();

            try {
                $session->pageTextContains($text);
            } catch (ResponseTextException $e) {
                return false;
            }

            return true;
        }, 10);
    }

    public function waitForElement($element)
    {
        $this->waitFor(function($context) use ($element){
            /** @var $context ProphetContext */
            try {
                $context->assertElementOnPage($element);
            } catch (ElementNotFoundException $e) {
                return false;
            }

            return true;
        });
    }

    public function clickElement($element)
    {
        $this->getSession()
            ->getPage()
            ->find("css", $element)
            ->click();
    }

    public function isVisible($element)
    {
        $session = $this->getSession();
        $page = $session->getPage();

        $pageElement = $page->find('css', $element);

        return $pageElement == null ? false : $pageElement->isVisible();
    }

    public function assertIsVisible($element) {
        if (!$this->isVisible($element)) {
            throw new ExpectationException($element.' is not visible on page', $this->getSession()->getDriver());
        }
    }

    public function assertIsNotVisible($element) {
        if ($this->isVisible($element)) {
            throw new ExpectationException($element.' is not visible on page', $this->getSession()->getDriver());
        }
    }

    public function waitForVisible($element)
    {
        $this->waitFor(function($context) use ($element) {
            return $context->isVisible($element);
        });
    }
}
