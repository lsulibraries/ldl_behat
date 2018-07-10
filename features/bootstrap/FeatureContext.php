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
class FeatureContext extends RawDrupalContext implements SnippetAcceptingContext {

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct() {
  }


  static $baseCollection = 'testing-testing-123:collection';
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
  * @Given I create a new collection :pid
  */
  public function iCreateANewCollection($pid) {
    module_load_include('inc', 'islandora_utils', 'includes/util');
    $title = $this->getRandom()->word(4);
    $descript = $this->getRandom()->paragraphs(2);
    $user_mgr = $this->getUserManager();
    $ca = $user_mgr->getCurrentUser();
    $user = user_load($ca->uid);
    try {
      islandora_utils_ingest_collection($title, $descript, $pid, 'islandora:root', array(), $user);
    }
    catch (Exception $e) {

    }
  }

  /**
   * @Given I am viewing the test collection
   */
  public function iAmViewingTheTestCollection()
  {
      $path = '/islandora/object/' . self::$baseCollection;
      $this->getSession()->visit($this->locatePath($path));
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

  /**
   * @BeforeSuite
   */
  public static function setup() {
    $admin = self::getAdminUser();
    self::createCollection($admin);
    self::createModsTmpFile();
  }

  /**
   * @AfterSuite
   */
  public static function teardown() {
    global $user; 
    $original_user = $user; 
    $old_state = drupal_save_session(); 
    drupal_save_session(FALSE); 
    $user = $behat = user_load_by_name('behat');

    self::deleteCollection(self::$baseCollection);
    $user = $original_user; 
    drupal_save_session($old_state); 

    if($behat) {
      user_delete($behat->uid);
    }
  }
  
  static function createCollection($user, \stdClass $collection = NULL) {
    module_load_include('inc', 'islandora_utils', 'includes/util');
    if (!$collection) {
      $collection = new stdClass();
      $collection->title = 'Test Collection';
      $collection->descript = "This is a description for a test collection";
      $collection->pid = self::$baseCollection;
    }
    islandora_utils_ingest_collection($collection->title, $collection->descript, $collection->pid, 'islandora:root', array(), $user);
  }

  public static function deleteCollection($pid) {
    $object = islandora_object_load($pid);
    if ($object) {
      batch_set(islandora_basic_collection_delete_children_batch($object));
      islandora_delete_object($object);
    }
  }
  
  public static function getAdminUser() {
    $user_exists = user_load_by_name('behat');
    if ($user_exists) {
      user_delete($user_exists->uid);
    }
    $account = new stdClass();
    $account->name = 'behat';
    $account->pass = 'behat';
    $account->email = 'behat@example.com';
    $user = user_save($account);
    $role = user_role_load_by_name("administrator");
    user_multiple_role_edit(array($user->uid), 'add_role', $role->rid);
    return user_load_by_name('behat');
  }

  public static function createModsTmpFile() {
    $mods = <<<EOF
<?xml version="1.0"?>
<mods xmlns="http://www.loc.gov/mods/v3" xmlns:mods="http://www.loc.gov/mods/v3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xlink="http://www.w3.org/1999/xlink">
  <titleInfo>
    <title>Replacement Value</title>
  </titleInfo>
  <name type="personal">
    <namePart></namePart>
    <role>
      <roleTerm authority="marcrelator" type="text"></roleTerm>
    </role>
  </name>
  <typeOfResource collection="yes"></typeOfResource>
  <genre authority="lctgm"></genre>
  <originInfo>
    <dateCreated></dateCreated>
  </originInfo>
  <language>
    <languageTerm authority="iso639-2b" type="code">eng</languageTerm>
  </language>
  <abstract>This is a description for a test collection</abstract>
  <identifier type="local"></identifier>
  <physicalDescription>
    <form authority="marcform"></form>
    <extent></extent>
  </physicalDescription>
  <note></note>
  <accessCondition></accessCondition>
  <subject>
    <topic></topic>
    <geographic></geographic>
    <temporal></temporal>
    <hierarchicalGeographic>
      <continent></continent>
      <country></country>
      <province></province>
      <region></region>
      <county></county>
      <city></city>
      <citySection></citySection>
    </hierarchicalGeographic>
    <cartographics>
      <coordinates></coordinates>
    </cartographics>
  </subject>
</mods> 
EOF;
  file_put_contents('/tmp/mods.xml', $mods);
  }

}
