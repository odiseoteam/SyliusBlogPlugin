@reading_articles
Feature: Reading articles
    In order to discover the products of the store
    As a Visitor
    I want to see the products an article shows

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Coffee mug"
        And the blog has a published article "Brewing guide"
        And the "Brewing guide" article shows the product "Coffee mug"

    @ui
    Scenario: Seeing a product inside an article
        When I read the "Brewing guide" article
        Then I should see the product "Coffee mug" in it

    @ui
    Scenario: Not seeing a product that is no longer sold
        Given the product "Coffee mug" has been disabled
        When I read the "Brewing guide" article
        Then I should not see the product "Coffee mug" in it
