@javascript @islandora_regenerate_dc @ldl @api

Feature:
  In order to ensure the correct functionality of the islandora_regenerate_dc module
  As a developer, I need to test it...

  Scenario:
    Given I am logged in as a user with the "administrator" role
    Given I am viewing the test collection
    Given I click "Manage"
    Given I click "Datastreams"
    Then I should find xpath "//td[contains(text(), 'MODS Record')]/following-sibling::td[@class='datastream-versions']/a[contains(text(), '1')]"
    Then I should find xpath "//td[contains(text(), 'DC Record')]/following-sibling::td[@class='datastream-versions']/a[contains(text(), '1')]"
    Given I click on the element with xpath "//td[@class='datstream-edit']/a"
    Given for "Collection Title" I enter "New value"
    Given I press the "Update" button
    Given I click "Manage"
    Given I click "Datastreams"
    Then I should find xpath "//td[contains(text(), 'MODS Record')]/following-sibling::td[@class='datastream-versions']/a[contains(text(), '2')]"
    Then I should find xpath "//td[contains(text(), 'DC Record')]/following-sibling::td[@class='datastream-versions']/a[contains(text(), '2')]"

  Scenario:
    Given I am logged in as a user with the "administrator" role
    Given I am viewing the test collection
    Given I click "Manage"
    Given I click "Datastreams"
    Then I should find xpath "//td[contains(text(), 'MODS Record')]/following-sibling::td[@class='datastream-versions']/a[contains(text(), '2')]"
    Then I should find xpath "//td[contains(text(), 'DC Record')]/following-sibling::td[@class='datastream-versions']/a[contains(text(), '2')]"
    Given I click on the element with xpath "//td[contains(text(), 'MODS Record')]/following-sibling::td[@class='datastream-replace']/a"
    Given I attach the file "/tmp/mods.xml" to "Upload Document"
    Given I press the "Add Contents" button
    Given I click "Manage"
    Given I click "Datastreams"
    Then I should find xpath "//td[contains(text(), 'MODS Record')]/following-sibling::td[@class='datastream-versions']/a[contains(text(), '3')]"
    Then I should find xpath "//td[contains(text(), 'DC Record')]/following-sibling::td[@class='datastream-versions']/a[contains(text(), '3')]"
    Given breakpoint
