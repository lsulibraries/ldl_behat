@javascript @theme @blackbox @newspaper

Feature:
  In order to ensure that the presentation of newspaper content items
  works as intended in the LDL
  We need to test it...

    Scenario: 
      Given I am viewing pid 'lsu-ag-sugar:bulletin'
      Given I click on the element with xpath "//span[@class='publication-year' and contains(text(), '1983')]"
      #When I click on the element with xpath "//div[@class='month-issues-container' and div[@class='date-month' and contains(text(), '04')]]/following-sibling::div//td[@class=' highlight']/div[@class='circleDay']"
      Then I should see "APR 1983"
      Then I should see "SELECT A DAY"
      When I click "15"
      Then I should see "Prev"
      Then I should see "Issue"
      Then I should see "Next"
      Then I should see "All Issues"
      Then I should find xpath "//div[@id=BookReader']"
      Given breakpoint

    Scenario: Check that we can click through a newspaper in the sample data set.
      Given I am on the homepage
      Given I click on the element with xpath "//div[contains(text(), 'Louisiana State University')]"
      Given I click 'lsu-ag'
      Given I click on the element with xpath "//div[@class='institution-collection-list-item-label' and contains(text(), 'LSU Ag Center Sugar Bulletins')]"
      When I click on the element with xpath "//a[@title='LSU Ag Center Sugar Bulletins']"
      Then I should see "This newspaper contains 20 issues across 1 years"
      Then I should see "1983"
      Then I should see "Step 1: Select Year"

    Scenario: Ensure that all months are present for a given year. 
      Given I am viewing pid 'lsu-ag-sugar:bulletin'
      Given I click on the element with xpath "//span[@class='publication-year' and contains(text(), '1983')]"
      Then I should see "Jan"
      Then I should see "Feb"
      Then I should see "Mar"
      Then I should see "Apr"
      Then I should see "May"
      Then I should see "Jun"
      Then I should see "Jul"
      Then I should see "Aug"
      Then I should see "Sep"
      Then I should see "Oct"
      Then I should see "Nov"
      Then I should see "Dec"
