Feature: 
  In order to manage my tests
  As a user
  I must be able to display modules with available frameworks

  Scenario: Run the show command with a module in prophet.yml
    When I run the show command
    Then I should see the sample module with phpunit enabled
