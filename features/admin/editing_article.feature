@managing_articles
Feature: Editing a article
    In order to change a blog article
    As an Administrator
    I want to be able to edit a article

    Background:
        Given I am logged in as an administrator
        And the store operates on a single channel in "United States"
        And there is an existing article with "article1" code

    @ui
    Scenario: Renaming a article
        Given I want to modify the "article1" article
        When I rename it to "Article Edited"
        And I save my changes
        Then I should be notified that it has been successfully edited
