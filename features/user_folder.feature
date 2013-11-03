Feature: User Folder Traversal
	In order to list and manage messages.
	As an User
	I want to be able to view folders and their messages.

    Background:
        Given I am logged in as "user1"
        And there are following users defined:
          | name  | email          | password | enabled  | role      |
          | user1 | user1@foo.com  | root     | 1        | ROLE_USER |
		  | user2 | user2@foo.com  | root     | 1        | ROLE_USER |
		  | user3 | user3@foo.com  | root     | 1        | ROLE_USER |
        And there are following messages defined:
          | subject         | body  | from  | to    | folder |
		  | subject_f1_t1_1 | body1 | user1 | user1 | inbox  |
		  | subject_f2_t1_1 | body2 | user2 | user1 | inbox  |
		  | subject_f3_t1_1 | body3 | user3 | user1 | inbox  |
		  | subject_f1_t2_1 | body1 | user1 | user2 | inbox  |
		  | subject_f2_t2_1 | body2 | user2 | user2 | inbox  |
		  | subject_f3_t2_1 | body3 | user3 | user2 | inbox  |
		  | subject_f1_t3_1 | body1 | user1 | user3 | inbox  |
		  | subject_f2_t3_1 | body2 | user2 | user3 | inbox  |
		  | subject_f3_t3_1 | body3 | user3 | user3 | inbox  |
		  | subject_f1_t1_2 | body1 | user1 | user1 | sent   |
		  | subject_f2_t1_2 | body2 | user2 | user1 | sent   |
		  | subject_f3_t1_2 | body3 | user3 | user1 | sent   |
		  | subject_f1_t2_2 | body1 | user1 | user2 | sent   |
		  | subject_f2_t2_2 | body2 | user2 | user2 | sent   |
		  | subject_f3_t2_2 | body3 | user3 | user2 | sent   |
		  | subject_f1_t3_2 | body1 | user1 | user3 | sent   |
		  | subject_f2_t3_2 | body2 | user2 | user3 | sent   |
		  | subject_f3_t3_2 | body3 | user3 | user3 | sent   |

	Scenario: See Messages list filtered by inbox folder
        Given I am on "/en/messages"
		And I should see envelope "subject_f1_t1_1"
		And I should see envelope "subject_f2_t1_1"
		And I should see envelope "subject_f3_t1_1"
		And I should not see envelope "subject_f1_t2_1"
		And I should not see envelope "subject_f2_t2_1"
		And I should not see envelope "subject_f3_t2_1"
		And I should not see envelope "subject_f1_t3_1"
		And I should not see envelope "subject_f2_t3_1"
		And I should not see envelope "subject_f3_t3_1"
		And I should not see envelope "subject_f1_t1_2"
		And I should not see envelope "subject_f2_t1_2"
		And I should not see envelope "subject_f3_t1_2"
		And I should not see envelope "subject_f1_t2_2"
		And I should not see envelope "subject_f2_t2_2"
		And I should not see envelope "subject_f3_t2_2"
		And I should not see envelope "subject_f1_t3_2"
		And I should not see envelope "subject_f2_t3_2"
		And I should not see envelope "subject_f3_t3_2"

	Scenario: See Messages list filtered by sent folder
        Given I am on "/en/messages/folder/sent"
		And I should see envelope "subject_f1_t1_1"
		And I should not see envelope "subject_f2_t1_1"
		And I should not see envelope "subject_f3_t1_1"
		And I should see envelope "subject_f1_t2_1"
		And I should not see envelope "subject_f2_t2_1"
		And I should not see envelope "subject_f3_t2_1"
		And I should see envelope "subject_f1_t3_1"
		And I should not see envelope "subject_f2_t3_1"
		And I should not see envelope "subject_f3_t3_1"
		And I should see envelope "subject_f1_t1_2"
		And I should see envelope "subject_f2_t1_2"
		And I should see envelope "subject_f3_t1_2"
		And I should see envelope "subject_f1_t2_2"
		And I should not see envelope "subject_f2_t2_2"
		And I should not see envelope "subject_f3_t2_2"
		And I should see envelope "subject_f1_t3_2"
		And I should not see envelope "subject_f2_t3_2"
		And I should not see envelope "subject_f3_t3_2"

	Scenario: Mark messages as read filtered by inbox folder
        Given I am on "/en/messages"
		And I should see envelope "subject_f1_t1_1"
		And I should see envelope "subject_f2_t1_1"
		And I should see envelope "subject_f3_t1_1"
		And I check envelope "subject_f2_t1_1"
		And I press "submit[mark_as_read]"
		And I should see envelope "subject_f1_t1_1" is unread
		And I should see envelope "subject_f2_t1_1" is read
		And I should see envelope "subject_f3_t1_1" is unread

	Scenario: Mark messages as unread filtered by inbox folder
        Given I am on "/en/messages"
		And I should see envelope "subject_f1_t1_1"
		And I should see envelope "subject_f2_t1_1"
		And I should see envelope "subject_f3_t1_1"
		And I check envelope "subject_f2_t1_1"
		And I press "submit[mark_as_read]"
		And I should see envelope "subject_f1_t1_1" is unread
		And I should see envelope "subject_f2_t1_1" is read
		And I should see envelope "subject_f3_t1_1" is unread
		And I check envelope "subject_f2_t1_1"
		And I press "submit[mark_as_unread]"
		And I should see envelope "subject_f1_t1_1" is unread
		And I should see envelope "subject_f2_t1_1" is unread
		And I should see envelope "subject_f3_t1_1" is unread

	Scenario: Move messages to another folder
        Given I am on "/en/messages"
		And I should see envelope "subject_f1_t1_1"
		And I should see envelope "subject_f2_t1_1"
		And I should see envelope "subject_f3_t1_1"
		And I check envelope "subject_f2_t1_1"
		And I select "Junk" from "select_move_to"
		And I press "submit[move_to]"
		And I should see envelope "subject_f1_t1_1"
		And I should not see envelope "subject_f2_t1_1"
		And I should see envelope "subject_f3_t1_1"
        Given I am on "/en/messages/folder/junk"
		And I should not see envelope "subject_f1_t1_1"
		And I should see envelope "subject_f2_t1_1"
		And I should not see envelope "subject_f3_t1_1"
		
	Scenario: Delete messages
        Given I am on "/en/messages"
		And I should see envelope "subject_f1_t1_1"
		And I should see envelope "subject_f2_t1_1"
		And I should see envelope "subject_f3_t1_1"
		And I check envelope "subject_f2_t1_1"
		And I press "submit[delete]"
		And I should see envelope "subject_f1_t1_1"
		And I should not see envelope "subject_f2_t1_1"
		And I should see envelope "subject_f3_t1_1"
		Given I am on "/en/messages/folder/trash"
		And I should not see envelope "subject_f1_t1_1"
		And I should see envelope "subject_f2_t1_1"
		And I should not see envelope "subject_f3_t1_1"

	Scenario: Empty deleted messages
        Given I am on "/en/messages"
		And I should see envelope "subject_f1_t1_1"
		And I should see envelope "subject_f2_t1_1"
		And I should see envelope "subject_f3_t1_1"
		And I check envelope "subject_f2_t1_1"
		And I press "submit[delete]"
		And I should see envelope "subject_f1_t1_1"
		And I should not see envelope "subject_f2_t1_1"
		And I should see envelope "subject_f3_t1_1"
		Given I am on "/en/messages/folder/trash"
		And I should not see envelope "subject_f1_t1_1"
		And I should see envelope "subject_f2_t1_1"
		And I should not see envelope "subject_f3_t1_1"
		And I check envelope "subject_f2_t1_1"
		And I press "submit[delete]"
		And I should not see envelope "subject_f1_t1_1"
		And I should not see envelope "subject_f2_t1_1"
		And I should not see envelope "subject_f3_t1_1"
