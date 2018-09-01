@managing_article_categories
Feature: Editing a article category
    In order to change a blog article category
    As an Administrator
    I want to be able to edit a article category

    Background:
        Given I am logged in as an administrator
        And the store operates on a single channel in "United States"
        And there is an existing article category with "sylius" code

    @ui
    Scenario: Renaming a article category
        Given I want to modify the "sylius" article category
        When I rename it to "Sylius Framework"
        And I save my changes
        Then I should be notified that it has been successfully edited
