@reading_articles
Feature: Reading articles
    In order to discover the products of the store
    As a Visitor
    I want to see the products and images an article shows

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

    @ui
    Scenario: Seeing an image inside an article
        Given the "Brewing guide" article shows the image "mugs.jpg" described as "Two mugs of coffee"
        When I read the "Brewing guide" article
        Then I should see the image "Two mugs of coffee" in it

    @ui
    Scenario: Seeing a gallery inside an article
        Given the "Brewing guide" article shows a gallery of "mugs.jpg" and "ford.jpg"
        When I read the "Brewing guide" article
        Then I should see a gallery of 2 images in it
