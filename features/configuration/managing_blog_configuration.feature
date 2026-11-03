@managing_blog_configuration
Feature: Managing the blog configuration
    In order to set up the blog of each channel
    As an Administrator
    I want to configure the blog per channel

    Background:
        Given the store operates on a channel named "Web Store"
        And the store also operates on another channel named "Mobile Store"
        And I am logged in as an administrator

    @ui
    Scenario: Seeing the blog settings of every channel
        Given the blog of the "Web Store" channel lists 12 articles per page
        When I browse the blog configuration
        Then I should see 2 channels in the list
        And the "Web Store" channel should show its blog with 12 articles per page
        And the "Mobile Store" channel should show its blog with 9 articles per page

    @ui
    Scenario: Configuring the blog of a channel for the first time
        When I want to configure the blog of the "Web Store" channel
        Then the blog should be shown
        And it should list 9 articles per page
        When I set it to list 6 articles per page
        And I name the blog "Journal" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the blog of the "Web Store" channel should list 6 articles per page
        And the blog of the "Web Store" channel should be named "Journal" in "English (United States)"

    @ui
    Scenario: Hiding the blog in a channel
        Given the blog of the "Web Store" channel lists 12 articles per page
        When I want to configure the blog of the "Web Store" channel
        And I hide the blog
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the blog of the "Web Store" channel should be hidden
        And the blog of the "Web Store" channel should list 12 articles per page

    @ui
    Scenario: Trying to list no articles per page
        When I want to configure the blog of the "Web Store" channel
        And I set it to list 0 articles per page
        And I try to save my changes
        Then I should be notified that the articles per page must be between 1 and 100
        And the "Web Store" channel should have no blog configuration stored
