Feature: Account
  In order to manage accounts
  As a user
  I need to be able manage transactions through REST API

  Background:
    Given I add "Content-Type" header equal to "application/ld+json"

  Scenario: Get a list of account
    Given there is a business partner with data:
      | name                       | status | legalForm                 | balance | address          | city   | zip  | country |
      | AMNIS Treasury Services AG | active | limited_liability_company | 400     | Baslerstrasse 60 | Zürich | 8048 | CH      |
      | AMNIS Europe AG            | active | limited_liability_company | 200     | Gewerbeweg 15    | Vaduz  | 9490 | LI      |
    Given there is an account with data:
      | currency | businessPartner          | balance |
      | EUR      | /api/business_partners/1 | 0       |
      | CHF      | /api/business_partners/2 | 0       |
    When I send a GET request to "/api/accounts"
    Then the response status code should be 200
    And the JSON node "hydra:member" should have 2 elements

  Scenario: Get a account
    Given there is a business partner with data:
      | name                       | status | legalForm                 | balance | address          | city   | zip  | country |
      | AMNIS Treasury Services AG | active | limited_liability_company | 400     | Baslerstrasse 60 | Zürich | 8048 | CH      |
    Given there is an account with data:
      | currency | businessPartner          | balance |
      | EUR      | /api/business_partners/1 | 100     |
    When I send a GET request to "/api/accounts/1"
    Then the response status code should be 200
    And the JSON node "@id" should be equal to the string "/api/accounts/1"
    And the JSON node "currency" should be equal to the string "EUR"
    And the JSON node "balance" should be equal to "100"
    And the JSON node "businessPartner" should be equal to the string "/api/business_partners/1"

  Scenario: Create a account
    Given there is a business partner with data:
      | name                       | status | legalForm                 | balance | address          | city   | zip  | country |
      | AMNIS Treasury Services AG | active | limited_liability_company | 400     | Baslerstrasse 60 | Zürich | 8048 | CH      |
    When I send a POST request to "/api/accounts" with body:
    """
      {
        "currency": "EUR",
        "businessPartner": "/api/business_partners/1",
        "balance": "100"
      }
    """
    Then the response status code should be 201
    And the JSON node "@id" should be equal to the string "/api/accounts/1"
    And the JSON node "currency" should be equal to the string "EUR"
    And the JSON node "balance" should be equal to "100"
    And the JSON node "businessPartner" should be equal to the string "/api/business_partners/1"

