@javascript
@islandora_elide_download
@ldl
@api

Feature:
    In order to ensure the correct function of the elide_download module
    As a developer
    I need to be sure that it fulfills the requirements.


    Scenario: Check visibility after drush enable/elide

        # Ensure the DB is cleared
        Given I run drush 'sql-query "DELETE FROM islandora_elide_download"'
        Given I view pid 'latech-cmprt:24'
        Then I should see "Download"

        Given I run drush "ied elide --pid=latech-cmprt:24"
        Given the cache has been cleared
        When I view pid 'latech-cmprt:24'
        Then I should not see "Download"

    Scenario: Ensure that a checkbox exists in <item>/manage/properties
        Given I am logged in as an "administrator"
        When I visit '/islandora/object/latech-cmprt:24/manage/properties'
        Then I should find xpath "//input[@id='edit-toggle-elide-dl']"

    Scenario: Check visibility of download link after UI toggle.

        # Ensure the DB is cleared
        Given I run drush 'sql-query "DELETE FROM islandora_elide_download"'
        Given I view pid 'latech-cmprt:24'
        Then I should see "Download"
        Given I am logged in as an 'administrator'
        Given I visit '/islandora/object/latech-cmprt:24/manage/properties'
        Given I check the box 'edit-toggle-elide-dl'
        Given I press the 'Update Properties' button
        When I view pid 'latech-cmprt:24'
        Then I should not see "Download"

        Given I am not logged in
        When I view pid 'latech-cmprt:24'
        Then I should not see "Download"
