Feature: Exchange
  In order to manage exchanges
  As a user
  I need to be able manage exchanges through REST API

  Background:
    Given I add "Content-Type" header equal to "application/ld+json"

  Scenario: Create an exchange
    Given there is a business partner with data:
      | name                       | status | legalForm                 | address          | city   | zip  | country |
      | AMNIS Treasury Services AG | active | limited_liability_company | Baslerstrasse 60 | Zürich | 8048 | CH      |
    Given there is an account with data:
      | currency | businessPartner          | balance |
      | EUR      | /api/business_partners/1 | 0       |
      | CHF      | /api/business_partners/1 | 1000    |
    Given I add "Content-Type" header equal to "application/ld+json"
    And I send a POST request to "/api/exchanges" with body:
    """
      {
        "fromAmount": "500",
        "date": "2024-07-12T09:08:32.563Z",
        "fromCurrency": "CHF",
        "toCurrency": "EUR",
        "businessPartner": "/api/business_partners/1"
      }
    """
    Then the response status code should be 201
    And the JSON node "@id" should be equal to the string "/api/exchanges/1"
    And the JSON node "fromAmount" should be equal to "500"
    And the JSON node "fromCurrency" should be equal to "CHF"
    And the JSON node "toAmount" should be equal to "550"
    And the JSON node "toCurrency" should be equal to "EUR"
    And the JSON node "exchangeRate" should be equal to "1.1"
    And the JSON node "date" should be equal to the string "2024-07-12T09:08:32+00:00"
    And the JSON node "executed" should be false
    And the JSON node "businessPartner" should be equal to the string "/api/business_partners/1"
    When I send a GET request to "/api/accounts/1"
    Then the response status code should be 200
    And the JSON node "balance" should be equal to "0"
    And the JSON node "currency" should be equal to "EUR"
    When I send a GET request to "/api/accounts/2"
    Then the response status code should be 200
    And the JSON node "balance" should be equal to "1000"
    And the JSON node "currency" should be equal to "CHF"

  Scenario: Create an exchange without enough balance
    Given there is a business partner with data:
      | name                       | status | legalForm                 | address          | city   | zip  | country |
      | AMNIS Treasury Services AG | active | limited_liability_company | Baslerstrasse 60 | Zürich | 8048 | CH      |
    Given there is an account with data:
      | currency | businessPartner          | balance |
      | EUR      | /api/business_partners/1 | 0       |
      | CHF      | /api/business_partners/1 | 0       |
    Given I add "Content-Type" header equal to "application/ld+json"
    And I send a POST request to "/api/exchanges" with body:
    """
      {
        "fromAmount": "500",
        "date": "2024-07-12T09:08:32.563Z",
        "fromCurrency": "CHF",
        "toCurrency": "EUR",
        "businessPartner": "/api/business_partners/1"
      }
    """
    Then the response status code should be 400
    And the JSON node "detail" should be equal to the string "You do not have enough money for a payout"
    When I send a GET request to "/api/accounts/1"
    Then the response status code should be 200
    And the JSON node "balance" should be equal to "0"
    And the JSON node "currency" should be equal to "EUR"
    When I send a GET request to "/api/accounts/2"
    Then the response status code should be 200
    And the JSON node "balance" should be equal to "0"
    And the JSON node "currency" should be equal to "CHF"

  Scenario: Create an exchange without account
    Given there is a business partner with data:
      | name                       | status | legalForm                 | address          | city   | zip  | country |
      | AMNIS Treasury Services AG | active | limited_liability_company | Baslerstrasse 60 | Zürich | 8048 | CH      |
    Given there is an account with data:
      | currency | businessPartner          | balance |
      | EUR      | /api/business_partners/1 | 0       |
    Given I add "Content-Type" header equal to "application/ld+json"
    And I send a POST request to "/api/exchanges" with body:
    """
      {
        "fromAmount": "500",
        "date": "2024-07-12T09:08:32.563Z",
        "fromCurrency": "CHF",
        "toCurrency": "EUR",
        "businessPartner": "/api/business_partners/1"
      }
    """
    Then the response status code should be 400
    And the JSON node "detail" should be equal to the string "No account for currency CHF."
    When I send a GET request to "/api/accounts/1"
    Then the response status code should be 200
    And the JSON node "balance" should be equal to "0"
    And the JSON node "currency" should be equal to "EUR"

  Scenario: Execute an exchange
    Given there is a business partner with data:
      | name                       | status | legalForm                 | address          | city   | zip  | country |
      | AMNIS Treasury Services AG | active | limited_liability_company | Baslerstrasse 60 | Zürich | 8048 | CH      |
    Given there is an account with data:
      | currency | businessPartner          | balance |
      | EUR      | /api/business_partners/1 | 0       |
      | CHF      | /api/business_partners/1 | 1000    |
    Given I add "Content-Type" header equal to "application/ld+json"
    And I send a POST request to "/api/exchanges" with body:
    """
      {
        "fromAmount": "500",
        "date": "2024-07-12T09:08:32.563Z",
        "fromCurrency": "CHF",
        "toCurrency": "EUR",
        "businessPartner": "/api/business_partners/1"
      }
    """
    Then the response status code should be 201
    Given I add "Content-Type" header equal to "application/merge-patch+json"
    When I send a PATCH request to "/api/exchanges/1/execute" with body:
    """
      {

      }
    """
    Then the response status code should be 200
    And the JSON node "executed" should be true
    When I send a GET request to "/api/accounts/1"
    Then the response status code should be 200
    And the JSON node "balance" should be equal to "550"
    And the JSON node "currency" should be equal to "EUR"
    When I send a GET request to "/api/accounts/2"
    Then the response status code should be 200
    And the JSON node "balance" should be equal to "500"
    And the JSON node "currency" should be equal to "CHF"

  Scenario: Execute an exchange that has already been executed
    Given there is a business partner with data:
      | name                       | status | legalForm                 | address          | city   | zip  | country |
      | AMNIS Treasury Services AG | active | limited_liability_company | Baslerstrasse 60 | Zürich | 8048 | CH      |
    Given there is an account with data:
      | currency | businessPartner          | balance |
      | EUR      | /api/business_partners/1 | 0       |
      | CHF      | /api/business_partners/1 | 1000    |
    Given I add "Content-Type" header equal to "application/ld+json"
    And I send a POST request to "/api/exchanges" with body:
    """
      {
        "fromAmount": "500",
        "date": "2024-07-12T09:08:32.563Z",
        "fromCurrency": "CHF",
        "toCurrency": "EUR",
        "businessPartner": "/api/business_partners/1"
      }
    """
    Then the response status code should be 201
    Given I add "Content-Type" header equal to "application/merge-patch+json"
    When I send a PATCH request to "/api/exchanges/1/execute" with body:
    """
      {

      }
    """
    Then the response status code should be 200
    And the JSON node "executed" should be true
    Given I add "Content-Type" header equal to "application/merge-patch+json"
    When I send a PATCH request to "/api/exchanges/1/execute" with body:
    """
      {

      }
    """
    Then the response status code should be 400
    And the JSON node "detail" should be equal to "Transaction is already executed"

  Scenario: Get a exchange
    Given there is a business partner with data:
      | name                       | status | legalForm                 | address          | city   | zip  | country |
      | AMNIS Treasury Services AG | active | limited_liability_company | Baslerstrasse 60 | Zürich | 8048 | CH      |
    Given there is an account with data:
      | currency | businessPartner          | balance |
      | EUR      | /api/business_partners/1 | 0       |
      | CHF      | /api/business_partners/1 | 1000    |
    And I send a POST request to "/api/exchanges" with body:
    """
      {
        "fromAmount": "500",
        "date": "2024-07-12T09:08:32.563Z",
        "fromCurrency": "CHF",
        "toCurrency": "EUR",
        "businessPartner": "/api/business_partners/1"
      }
    """
    Then the response status code should be 201
    When I send a GET request to "/api/exchanges/1"
    And the JSON node "@id" should be equal to the string "/api/exchanges/1"
    And the JSON node "fromAmount" should be equal to "500"
    And the JSON node "fromCurrency" should be equal to "CHF"
    And the JSON node "toAmount" should be equal to "550"
    And the JSON node "toCurrency" should be equal to "EUR"
    And the JSON node "exchangeRate" should be equal to "1.1"
    And the JSON node "date" should be equal to the string "2024-07-12T09:08:32+00:00"
    And the JSON node "executed" should be false
    And the JSON node "businessPartner" should be equal to the string "/api/business_partners/1"

  Scenario: Get a list of exchanges
    Given there is a business partner with data:
      | name                       | status | legalForm                 | address          | city   | zip  | country |
      | AMNIS Treasury Services AG | active | limited_liability_company | Baslerstrasse 60 | Zürich | 8048 | CH      |
    Given there is an account with data:
      | currency | businessPartner          | balance |
      | EUR      | /api/business_partners/1 | 0       |
      | CHF      | /api/business_partners/1 | 1000    |
    Given I add "Content-Type" header equal to "application/ld+json"
    And I send a POST request to "/api/exchanges" with body:
    """
      {
        "fromAmount": "500",
        "date": "2024-07-12T09:08:32.563Z",
        "fromCurrency": "CHF",
        "toCurrency": "EUR",
        "businessPartner": "/api/business_partners/1"
      }
    """
    Then the response status code should be 201
    Given I add "Content-Type" header equal to "application/ld+json"
    And I send a POST request to "/api/exchanges" with body:
    """
      {
        "fromAmount": "500",
        "date": "2024-07-12T09:08:32.563Z",
        "fromCurrency": "CHF",
        "toCurrency": "EUR",
        "businessPartner": "/api/business_partners/1"
      }
    """
    Then the response status code should be 201
    When I send a GET request to "/api/exchanges"
    Then the response status code should be 200
    And the JSON node "hydra:member" should have 2 elements


