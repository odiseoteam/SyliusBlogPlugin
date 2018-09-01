@managing_article_categories
Feature: Browsing articles categories
    In order to show the blog article categoriess created
    As an Administrator
    I want to be able to browse article categories

    Background:
        Given the store has "sylius" and "symfony" article categories
        And I am logged in as an administrator
        And the store operates on a single channel in "United States"

    @ui
    Scenario: Browsing defined article categories
        When I want to browse article categories
        Then I should see 2 article categories in the list
