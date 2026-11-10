@managing_authors
Feature: Managing authors
    In order to sign articles
    As an Administrator
    I want to add, edit and delete authors

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui
    Scenario: Adding an author
        When I want to create a new author
        And I specify its code as "JANE"
        And I name it "Jane Doe"
        And I set its slug to "jane-doe"
        And I add it
        Then I should be notified that it has been successfully created
        And the author "Jane Doe" should have the slug "jane-doe"

    @ui @javascript
    Scenario: Generating the slug from the name
        When I want to create a new author
        And I specify its code as "JANE"
        And I name it "Jane Döe"
        And I generate its slug
        And I add it
        Then I should be notified that it has been successfully created
        And the author "Jane Döe" should have the slug "jane-doe"

    @ui @javascript
    Scenario: Adding social links in order
        When I want to create a new author
        And I specify its code as "JANE"
        And I name it "Jane Doe"
        And I set its slug to "jane-doe"
        And I add a "LinkedIn" link to "https://www.linkedin.com/in/jane"
        And I add a "GitHub" link to "https://github.com/jane"
        And I add it
        Then I should be notified that it has been successfully created
        And the author "Jane Doe" should have the links "linkedin: https://www.linkedin.com/in/jane" and "github: https://github.com/jane" in this order

    @ui @javascript
    Scenario: Removing a social link
        Given the blog has an author "Jane Doe" with the links "linkedin: https://www.linkedin.com/in/jane" and "github: https://github.com/jane"
        When I want to modify the "Jane Doe" author
        And I remove its first link
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the author "Jane Doe" should have the link "github: https://github.com/jane"

    @ui
    Scenario: Adding an author with an avatar
        When I want to create a new author
        And I specify its code as "JANE"
        And I name it "Jane Doe"
        And I set its slug to "jane-doe"
        And I attach the "mugs.jpg" avatar
        And I add it
        Then I should be notified that it has been successfully created
        And the author "Jane Doe" should have an avatar

    @ui
    Scenario: Removing the avatar of an author
        Given the blog has an author "Jane Doe" with the "mugs.jpg" avatar
        When I want to modify the "Jane Doe" author
        And I remove its avatar
        Then the author "Jane Doe" should have no avatar

    @ui
    Scenario: The code cannot be changed
        Given the blog has an author "Jane Doe"
        When I want to modify the "Jane Doe" author
        Then I should not be able to edit its code

    @ui
    Scenario: Trying to use a code that is already taken
        Given the blog has an author "Jane Doe"
        When I want to create a new author
        And I specify its code as "JANE_DOE"
        And I name it "Another Jane"
        And I set its slug to "another-jane"
        And I try to add it
        Then I should be notified that the code is already used

    @ui
    Scenario: Trying to use a slug that is already taken
        Given the blog has an author "Jane Doe"
        When I want to create a new author
        And I specify its code as "JANE_2"
        And I name it "Another Jane"
        And I set its slug to "jane-doe"
        And I try to add it
        Then I should be notified that the slug is already used

    @ui
    Scenario: Deleting an author
        Given the blog has an author "Jane Doe"
        When I delete the "Jane Doe" author
        Then I should be notified that it has been successfully deleted
        And the author "Jane Doe" should not exist

    @ui
    Scenario: An author that signs articles cannot be deleted
        Given the blog has an author "Jane Doe"
        And the author "Jane Doe" signs the article "Brewing guide"
        When I delete the "Jane Doe" author
        Then I should be notified that the author signs articles
        And the author "Jane Doe" should exist
