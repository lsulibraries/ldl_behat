@javascript
@namespace_homepage
@ldl
@islandora
@api

Feature: In order to ensure the correct function of namespace_homepage, let's test it.

    Scenario: Assuming the sample data is ingested, add a thumbnail to the LSU homepage.
      Given users:
      | name     | roles | field_namespace |
      | jdo | Collection admin | lsu |
#      Given I am logged in as a user with the "Collection admin" role(s) and I have the following fields:
#      | name | value |
#      | field_namespace | lsu |
      Given I am logged in as "jdo"
      Given I am on "/lsu"
      Then I should see "EDIT SETTINGS"
      When I click "Edit Settings"
      Then I should see "Institution name"
      Given breakpoint