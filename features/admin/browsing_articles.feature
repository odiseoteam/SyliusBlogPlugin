@managing_articles
Feature: Browsing articles
    In order to show the blog articles created
    As an Administrator
    I want to be able to browse articles

    Background:
        Given the store has "article1" and "article2" articles
        And I am logged in as an administrator
        And the store operates on a single channel in "United States"

    @ui
    Scenario: Browsing defined articles
        When I want to browse articles
        Then I should see 2 articles in the list
