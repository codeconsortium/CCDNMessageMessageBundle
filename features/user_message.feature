Feature: User Folder Traversal
	In order to list and manage messages.
	As an User
	I want to be able to view folders and their messages.

    Background:
        Given I am logged in as "user"
        And there are following users defined:
          | name  | email          | password | enabled  | role      |
          | user1 | user1@foo.com  | root     | 1        | ROLE_USER |
		  | user2 | user2@foo.com  | root     | 1        | ROLE_USER |
		  | user3 | user3@foo.com  | root     | 1        | ROLE_USER |
        And there are following messages defined:
          | subject  | body  |from  | to    | folder |
		  | subject1 | body1 |user1 | user1 | inbox  |
		  | subject2 | body2 |user2 | user2 | inbox  |
		  | subject3 | body3 |user3 | user3 | inbox  |

	Scenario: See Messages list filtered by folder
        Given I am on "/en/messages"

