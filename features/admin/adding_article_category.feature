@managing_article_categories
Feature: Adding a new article category
    In order to create some article categories to be use in the articles and be showed on the store
    As an Administrator
    I want to add a new article category to the system

    Background:
        Given I am logged in as an administrator
        And the store operates on a single channel in "United States"

    @ui
    Scenario: Adding a new article category
        Given I want to add a new article category
        When I fill the code with "sylius"
        And I fill the slug with "sylius"
        And I fill the title with "Sylius"
        And I add it
        Then I should be notified that it has been successfully created
        And the article category "sylius" should appear in the admin

    @ui
    Scenario: Trying to add a new article category with empty fields
        Given I want to add a new article category
        When I fill the code with "sylius"
        And I add it
        Then I should be notified that the form contains invalid fields

    @ui
    Scenario: Trying to add a article category with existing code
        Given there is an existing article category with "sylius" code
        And I want to add a new article category
        When I fill the code with "sylius"
        And I add it
        Then I should be notified that there is already an existing article category with provided code
