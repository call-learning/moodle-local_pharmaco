@core @core_admin
Feature: Set course in the enva plugin settings
  In order to set course in the enva plugin settings value
  As an admin
  I need to set admin setting value and verify it is applied

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course fullname | C_shortname | 0 |
    And I log in as "admin"
    When I navigate to "Add a new user" node in "Site administration>Plugins>Local Plugins"
    And I set the following fields to these values:
      | Username                        | testmultiemailuser1             |
      | Choose an authentication method | Manual accounts                 |
      | New password                    | test@User1                      |
      | First name                      | Test                            |
      | Surname                         | Multi1                          |
      | Email address                   | testmultiemailuser@example.com  |
    And I press "Create user"
    And I should see "Test Multi1"
    And I press "Add a new user"
    And I navigate to "Plugins > Local Plugins > Browse list of users" in site administration
    And I should see "Course fullname"
    And I should not see "C_shortname Course fullname"

  Scenario: set admin value with full name
    Given the following config values are set as admin:
      | courselistshortnames | 1 |
    And I am on site homepage
    Then I should see "C_shortname Course fullname"

  Scenario: set admin value with short name
    Given the following config values are set as admin:
      | courselistshortnames | 1 |
    And I am on site homepage
    Then I should see "C_shortname Course fullname"
