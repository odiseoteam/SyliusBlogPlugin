@managing_articles
Feature: Managing articles
    In order to tell stories about the store
    As an Administrator
    I want to write, edit and delete articles

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui
    Scenario: Adding an article
        When I want to write a new article
        And I specify its code as "BREWING_GUIDE"
        And I title it "Brewing guide" in "English (United States)"
        And I set its slug to "brewing-guide" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the article "Brewing guide" should appear in the list
        And the "Brewing guide" article should be a draft

    @ui
    Scenario: A new article starts in every channel
        Given the store also operates on another channel named "Mobile"
        When I want to write a new article
        Then it should be available in the "United States" channel
        And it should be available in the "Mobile" channel

    @ui @javascript
    Scenario: Generating the slug from the title
        When I want to write a new article
        And I specify its code as "BREWING_GUIDE"
        And I title it "Café brewing guide" in "English (United States)"
        And I generate its slug in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the "Café brewing guide" article should have the slug "cafe-brewing-guide" in "English (United States)"

    @ui
    Scenario: Editing an article
        Given the blog has an article "Brewing guide"
        When I want to modify the "Brewing guide" article
        And I title it "Coffee brewing guide" in "English (United States)"
        And I set its excerpt to "Everything about brewing." in "English (United States)"
        And I mark it as featured
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the article "Coffee brewing guide" should appear in the list
        And the "Coffee brewing guide" article should be featured
        And the "Coffee brewing guide" article should have the excerpt "Everything about brewing." in "English (United States)"

    @ui
    Scenario: The code cannot be changed
        Given the blog has an article "Brewing guide"
        When I want to modify the "Brewing guide" article
        Then I should not be able to edit its code

    @ui
    Scenario: Taking an article out of every channel
        Given the blog has an article "Brewing guide"
        When I want to modify the "Brewing guide" article
        And I make it unavailable in the "United States" channel
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Brewing guide" article should not be available in any channel

    @ui @javascript
    Scenario: Classifying an article
        Given the blog has a category "Coffee"
        And the blog has a category "Tea"
        And the blog has a tag "Brewing"
        And the blog has an article "Brewing guide"
        When I want to modify the "Brewing guide" article
        And I add it to the "Tea" category
        And I set its main category to "Coffee"
        And I tag it with "Brewing"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the main category of the "Brewing guide" article should be "Coffee"
        And the "Brewing guide" article should be in the "Coffee" and "Tea" categories
        And the "Brewing guide" article should be tagged with "Brewing"

    @ui
    Scenario: Deleting an article
        Given the blog has an article "Brewing guide"
        When I delete the "Brewing guide" article
        Then I should be notified that it has been successfully deleted
        And the article "Brewing guide" should not appear in the list

    @ui
    Scenario: Trying to use a code that is already taken
        Given the blog has an article "Brewing guide"
        When I want to write a new article
        And I specify its code as "BREWING_GUIDE"
        And I title it "Brewing again" in "English (United States)"
        And I set its slug to "brewing-again" in "English (United States)"
        And I try to add it
        Then I should be notified that the code is already used
        And the article "Brewing again" should not appear in the list

    @ui
    Scenario: Trying to use a slug that is already taken in the same language
        Given the blog has an article "Brewing guide"
        When I want to write a new article
        And I specify its code as "TEA_GUIDE"
        And I title it "Tea guide" in "English (United States)"
        And I set its slug to "brewing-guide" in "English (United States)"
        And I try to add it
        Then I should be notified that the slug is already used in "English (United States)"
        And the article "Tea guide" should not appear in the list

    @ui
    Scenario: Trying to save content that is not JSON
        Given the blog has an article "Brewing guide"
        When I want to modify the "Brewing guide" article
        And I set its content to "<p>Hello</p>" in "English (United States)"
        And I try to save my changes
        Then I should be notified that the content must be valid JSON in "English (United States)"

    @ui
    Scenario: Searching articles by title
        Given the blog has an article "Brewing guide"
        And the blog has an article "Tea time"
        When I browse articles
        And I search for "brew"
        Then I should see 1 article in the list
        And I should see the article "Brewing guide" in the list

    @ui
    Scenario: Filtering articles by state
        Given the blog has an article "Brewing guide"
        And the blog has a published article "Tea time"
        When I browse articles
        And I choose to see published articles
        And I filter
        Then I should see 1 article in the list
        And I should see the article "Tea time" in the list

    @ui
    Scenario: Filtering articles by channel
        Given the blog has an article "Brewing guide"
        And the store also operates on another channel named "Mobile"
        And the blog has an article "Tea time" in the "Mobile" channel
        When I browse articles
        And I choose to see articles of the "Mobile" channel
        And I filter
        Then I should see 1 article in the list
        And I should see the article "Tea time" in the list

    @ui
    Scenario: Filtering articles by publication date
        Given the blog has an article "Brewing guide" published on "2026-03-10"
        And the blog has an article "Tea time" published on "2026-05-20"
        When I browse articles
        And I choose to see articles published from "2026-05-01" to "2026-05-31"
        And I filter
        Then I should see 1 article in the list
        And I should see the article "Tea time" in the list

    @ui @javascript
    Scenario: Filtering articles by category
        Given the blog has a category "Coffee"
        And the blog has an article "Brewing guide" in the "Coffee" category
        And the blog has an article "Tea time"
        When I browse articles
        And I choose to see articles of the "Coffee" category
        And I filter
        Then I should see 1 article in the list
        And I should see the article "Brewing guide" in the list

    @ui
    Scenario: The author linked to me signs my new article
        Given the blog has an author "Jane Doe" linked to my account
        When I want to write a new article
        And I specify its code as "BREWING_GUIDE"
        And I title it "Brewing guide" in "English (United States)"
        And I set its slug to "brewing-guide" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the "Brewing guide" article should be signed by "Jane Doe"

    @ui @javascript
    Scenario: Signing an article with several authors
        Given the blog has an author "Jane Doe"
        And the blog has an author "John Smith"
        And the blog has an article "Brewing guide"
        When I want to modify the "Brewing guide" article
        And I add the author "John Smith"
        And I add the author "Jane Doe"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Brewing guide" article should be signed by "John Smith" and "Jane Doe"

    @ui @javascript
    Scenario: Changing the order of the authors
        Given the blog has an author "Jane Doe"
        And the blog has an author "John Smith"
        And the blog has an article "Brewing guide" signed by "Jane Doe" and "John Smith"
        When I want to modify the "Brewing guide" article
        And I remove the author "Jane Doe"
        And I add the author "Jane Doe"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Brewing guide" article should be signed by "John Smith" and "Jane Doe"

    @ui @javascript
    Scenario: Filtering articles by author
        Given the blog has an author "Jane Doe"
        And the blog has an article "Brewing guide" signed by "Jane Doe"
        And the blog has an article "Tea time"
        When I browse articles
        And I choose to see articles of the author "Jane Doe"
        And I filter
        Then I should see 1 article in the list
        And I should see the article "Brewing guide" signed by "Jane Doe" in the list

    @ui
    Scenario: Adding an image with its alt text
        Given the blog has an article "Brewing guide"
        When I want to modify the "Brewing guide" article
        And I attach the "mugs.jpg" image
        And I describe the image as "Two mugs of coffee" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Brewing guide" article should have an image
        And the image of the "Brewing guide" article should be described as "Two mugs of coffee" in "English (United States)"
        And I should not be warned that the image has no alt text

    @ui
    Scenario: Being warned that the image has no alt text
        Given the blog has an article "Brewing guide" with the "mugs.jpg" image
        When I want to modify the "Brewing guide" article
        Then I should be warned that the image has no alt text in "English (United States)"

    @ui
    Scenario: Removing the image of an article
        Given the blog has an article "Brewing guide" with the "mugs.jpg" image
        When I want to modify the "Brewing guide" article
        And I remove its image
        Then the "Brewing guide" article should have no image
