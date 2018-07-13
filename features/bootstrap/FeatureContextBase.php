<?php

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Drupal\DrupalExtension\Context\DrushContext;
use Drupal\DrupalUserManager;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\MinkExtension\Context;
use IslandoraApi\Util;


/**
 * Defines application features from the specific context.
 */
class FeatureContextBase extends RawDrupalContext implements SnippetAcceptingContext {

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct() {

  }


  /**
   *
   * @Given I am viewing pid :pid
   * @Then I view pid :pid
   */
  public function iVisitPid($pid) {
    $path = '/islandora/object/' . $pid;
    $this->getSession()->visit($this->locatePath($path));
  }

  /**
   * Click on the element with the provided xpath query
   *
   * Posted by Abu Ashraf Masnun, retrieved 2018-06-27
   * http://masnun.com/2012/11/28/behat-and-mink-finding-and-clicking-with-xpath-and-jquery-like-css-selector.html
   * @When /^I click on the element with xpath "([^"]*)"$/
   */
  public function iClickOnTheElementWithXPath($xpath) {
    // Get the mink session.
    $session = $this->getSession();
    // Runs the actual query and returns the element.
    $element = $session->getPage()->find(
        'xpath',
        $session->getSelectorsHandler()->selectorToXpath('xpath', $xpath)
    );
    // Errors must not pass silently.
    if (NULL === $element) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate XPath: "%s"', $xpath));
    }

    // Ok, let's click on it.
    $element->click();
  }

  /**
   * Find whether an xpath exists in the page.
   *
   * Adapted from code posted by Abu Ashraf Masnun, retrieved 2018-06-27
   * http://masnun.com/2012/11/28/behat-and-mink-finding-and-clicking-with-xpath-and-jquery-like-css-selector.html
   *
   * @When /^I should find xpath "([^"]*)"$/
   */
  public function iShouldFindXPath($xpath) {

    // Errors must not pass silently.
    if (!$this->xpathExists($xpath)) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate XPath: "%s"', $xpath));
    }
  }

  /**
   * Find whether an xpath exists in the page.
   *
   * Adapted from code posted by Abu Ashraf Masnun, retrieved 2018-06-27
   * http://masnun.com/2012/11/28/behat-and-mink-finding-and-clicking-with-xpath-and-jquery-like-css-selector.html
   *
   * @When /^I should not find xpath "([^"]*)"$/
   */
  public function iShouldNotFindXPath($xpath) {

    // Errors must not pass silently.
    if ($this->xpathExists($xpath)) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate XPath: "%s"', $xpath));
    }
  }

  /**
   * Helper fn for xpath exists.
   *
   * Adapted from code posted by Abu Ashraf Masnun, retrieved 2018-06-27
   * http://masnun.com/2012/11/28/behat-and-mink-finding-and-clicking-with-xpath-and-jquery-like-css-selector.html
   *
   * @param string $xpath
   *   xpath to find
   */
  public function xpathExists($xpath) {
    // Get the mink session.
    $session = $this->getSession();
    // Runs the actual query and returns the element.
    $element = $session->getPage()->find(
        'xpath',
        $session->getSelectorsHandler()->selectorToXpath('xpath', $xpath)
    );
    if (NULL === $element) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * @Given /^breakpoint$/
   */
  public function breakpoint() {
    fwrite(STDOUT, "\033[s \033[93m[Breakpoint] Press \033[1;93m[RETURN]\033[0;93m to continue...\033[0m");
    while (fgets(STDIN, 1024) == '') {}
    fwrite(STDOUT, "\033[u");
    return;
  }
}
