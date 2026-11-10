@managing_tags
Feature: Managing tags
    In order to group articles by topic
    As an Administrator
    I want to add, edit and delete tags

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a tag
        When I want to create a new tag
        And I specify its code as "BREWING"
        And I name it "Brewing" in "English (United States)"
        And I set its slug to "brewing" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the tag "Brewing" should appear in the list

    @ui @javascript
    Scenario: Generating the slug from the name
        When I want to create a new tag
        And I specify its code as "BREWING_TIPS"
        And I name it "Café brewing tips" in "English (United States)"
        And I generate its slug in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the "Café brewing tips" tag should have the slug "cafe-brewing-tips" in "English (United States)"

    @ui
    Scenario: Renaming a tag
        Given the blog has a tag "Brewing"
        When I want to modify the "Brewing" tag
        And I name it "Coffee brewing" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the tag "Coffee brewing" should appear in the list

    @ui
    Scenario: The code cannot be changed
        Given the blog has a tag "Brewing"
        When I want to modify the "Brewing" tag
        Then I should not be able to edit its code

    @ui
    Scenario: Deleting a tag
        Given the blog has a tag "Brewing"
        When I delete the "Brewing" tag
        Then I should be notified that it has been successfully deleted
        And the tag "Brewing" should not appear in the list

    @ui
    Scenario: Trying to use a code that is already taken
        Given the blog has a tag "Brewing"
        When I want to create a new tag
        And I specify its code as "BREWING"
        And I name it "Brewing again" in "English (United States)"
        And I set its slug to "brewing-again" in "English (United States)"
        And I try to add it
        Then I should be notified that the code is already used
        And the tag "Brewing again" should not appear in the list

    @ui
    Scenario: Trying to use a slug that is already taken in the same language
        Given the blog has a tag "Brewing"
        When I want to create a new tag
        And I specify its code as "COFFEE"
        And I name it "Coffee" in "English (United States)"
        And I set its slug to "brewing" in "English (United States)"
        And I try to add it
        Then I should be notified that the slug is already used in "English (United States)"
        And the tag "Coffee" should not appear in the list
