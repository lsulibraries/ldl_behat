@javascript @ldl @islandora_ip_embargo @api

Feature: API example
  In order to show functionality added by the api
  As a trainer
  I need to use the step definitions it supports

  Scenario: Embargo pid to an unlikely IP.

    # Delete existing embargoes.
    Given I run drush 'sql-query "DELETE FROM islandora_ip_embargo_lists"'
    Given I run drush 'sql-query "DELETE FROM islandora_ip_embargo_ip_ranges"'
    Given I run drush 'sql-query "DELETE FROM islandora_ip_embargo_embargoes"'
    Given the cache has been cleared

    # create embargo for a single item
    Given I run drush "iipeadd --list=not-my-ip --low=0.0.0.0 --high=0.0.0.1"
    Given I run drush "islandora-ip-embargo-set-embargo --list=not-my-ip --pid=latech-cmprt:24 --public_dsids=MODS,TN,DC"

    Given I am not logged in
    When I view pid "latech-cmprt:24"

    When I click on the element with xpath "//div[@class='infoToggle userSelect']"
    Then I should find xpath "//span[@class='modsTitle metadataValue' and text() = 'Grave of a German Soldier']"
    
    When I click on the element with xpath "//div[@class='imagePreview']"
    Then I should not find xpath "//div[@id='islandora-openseadragon']"

    Given I am logged in as an "administrator"
    When I view pid "latech-cmprt:24"

    When I click on the element with xpath "//div[@class='infoToggle userSelect']"
    Then I should find xpath "//span[@class='modsTitle metadataValue' and text() = 'Grave of a German Soldier']"

    When I click on the element with xpath "//div[@class='imagePreview']"
    Then I should find xpath "//div[@id='islandora-openseadragon']"

  Scenario: Embargo pid to include our IP.
    Given I run drush 'sql-query "DELETE FROM islandora_ip_embargo_lists"'
    Given I run drush 'sql-query "DELETE FROM islandora_ip_embargo_ip_ranges"'
    Given I run drush 'sql-query "DELETE FROM islandora_ip_embargo_embargoes"'
    Given the cache has been cleared

    Given I run drush "iipeadd --list=include-my-ip --low=192.168.111.0 --high=192.168.111.255"
    Given I run drush "islandora-ip-embargo-set-embargo --list=include-my-ip --pid=latech-cmprt:24 --public_dsids=MODS,TN,DC"
    
    Given I am not logged in
    When I view pid "latech-cmprt:24"
    
    When I click on the element with xpath "//div[@class='infoToggle userSelect']"
    Then I should find xpath "//span[@class='modsTitle metadataValue' and text() = 'Grave of a German Soldier']"
    
    When I click on the element with xpath "//div[@class='imagePreview']"
    Then I should find xpath "//div[@id='islandora-openseadragon']"

    Given I am logged in as an "administrator"
    When I view pid "latech-cmprt:24"

    When I click on the element with xpath "//div[@class='infoToggle userSelect']"
    Then I should find xpath "//span[@class='modsTitle metadataValue' and text() = 'Grave of a German Soldier']"

    When I click on the element with xpath "//div[@class='imagePreview']"
    Then I should find xpath "//div[@id='islandora-openseadragon']"

  Scenario: visit inst homepage
    Given I am logged in as an "administrator"
    Given I am on the homepage
    When I click on the element with xpath "//div[@class='homepageText']/a[@class='institutionLink lsuLink']"
    Then I should see "Louisiana State University"
    When I click on the element with xpath "//div[@class='institution-collection-list-item-label' and contains(text(), 'Huey P. Long')]"
    Then I should see "Huey P. Long"
