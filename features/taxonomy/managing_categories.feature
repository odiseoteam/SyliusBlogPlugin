@managing_categories
Feature: Managing categories
    In order to organize articles in a tree of topics
    As an Administrator
    I want to add, edit, reorder and delete categories

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a category
        When I want to create a new category
        And I specify its code as "COFFEE"
        And I name it "Coffee" in "English (United States)"
        And I set its slug to "coffee" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the "Coffee" category should exist

    @ui
    Scenario: Adding a subcategory
        Given the blog has a category "Coffee"
        When I want to create a new category inside "Coffee"
        And I specify its code as "BREWING"
        And I name it "Brewing" in "English (United States)"
        And I set its slug to "brewing" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the "Brewing" category should be inside "Coffee"

    @ui
    Scenario: Adding a category with an image
        When I want to create a new category
        And I specify its code as "COFFEE"
        And I name it "Coffee" in "English (United States)"
        And I set its slug to "coffee" in "English (United States)"
        And I attach the "mugs.jpg" image
        And I add it
        Then I should be notified that it has been successfully created
        And the "Coffee" category should have an image

    @ui
    Scenario: Removing the image of a category
        Given the blog has a category "Coffee" with the "mugs.jpg" image
        When I want to modify the "Coffee" category
        And I remove its image
        Then the "Coffee" category should have no image

    @ui
    Scenario: Trying to use a code that is already taken
        Given the blog has a category "Coffee"
        When I want to create a new category
        And I specify its code as "COFFEE"
        And I name it "More coffee" in "English (United States)"
        And I set its slug to "more-coffee" in "English (United States)"
        And I try to add it
        Then I should be notified that the code is already used

    @ui @javascript
    Scenario: Seeing the categories as a tree
        Given the blog has a category "Coffee"
        And the "Coffee" category has a subcategory "Brewing"
        And the blog has a category "Tea"
        When I want to create a new category
        Then the tree should list "Coffee", "Brewing" and "Tea" in this order

    @ui @javascript
    Scenario: Moving a category down
        Given the blog has a category "Coffee"
        And the blog has a category "Tea"
        When I want to create a new category
        And I move the "Coffee" category down
        Then the tree should list "Tea" and "Coffee" in this order

    @ui @javascript
    Scenario: Deleting a category deletes its subcategories
        Given the blog has a category "Coffee"
        And the "Coffee" category has a subcategory "Brewing"
        And the blog has a category "Tea"
        When I delete the "Coffee" category
        Then I should be notified that it has been successfully deleted
        And the "Coffee" category should not exist
        And the "Brewing" category should not exist
        But the "Tea" category should exist
