@managing_payment_method_mercanet_bnp_paribas
Feature: Adding a new payment method
    In order to pay for orders in different ways
    As an Administrator
    I want to add a new payment method to the registry

    Background:
        Given the store operates on a single channel in "United States"
        And adding a new channel in "France"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new Mercanet BNP Paribas payment method with result successfully
        Given I want to create a new payment method with Mercanet BNP Paribas gateway factory
        When I name it "Mercanet BNP Paribas" in "English (United States)"
        And I specify its code as "MBNPP"
        And make it available in channel "France"
        And I configure it with test Mercanet BNP Paribas credentials
        And I add it
        Then I should be notified that it has been successfully created
        And the payment method "Mercanet BNP Paribas" should appear in the registry
        And the payment method "Mercanet BNP Paribas" should be available in channel "France"
