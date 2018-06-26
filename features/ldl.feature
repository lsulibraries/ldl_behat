@ldl
@javascript
@api
Feature: API example
  In order to show functionality added by the api
  As a trainer
  I need to use the step definitions it supports

  Scenario: Create users
    Given I am logged in as an "administrator"
    When I visit '/lsu'
    Then I should see "Louisiana State University"
