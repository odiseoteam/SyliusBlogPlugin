@managing_articles
Feature: Adding a new article
    In order to show blog articles in the store
    As an Administrator
    I want to add a new article to the system

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Adding a new article
        Given I want to add a new article
        When I fill the code with "article1"
        And I fill the slug with "article1"
        And I fill the title with "Article 1"
        And I fill the content with "<h1>Some awesome article<h1>"
        And I add it
        Then I should be notified that it has been successfully created
        And the article "article1" should appear in the admin

    @ui
    Scenario: Trying to add a new article with empty fields
        Given I want to add a new article
        When I fill the code with "article1"
        And I add it
        Then I should be notified that the form contains invalid fields

    @ui
    Scenario: Trying to add a article with existing code
        Given there is an existing article "article1" code
        And I want to add a new article
        When I fill the code with "article1"
        And I add it
        Then I should be notified that there is already an existing article with provided code
