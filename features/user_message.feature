Feature: User Message Management
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

	Scenario: Send message to another user
        Given I am on "/en/messages"
		And I follow "Compose"
        And I fill in "Message[send_to]" with "user2"
        And I fill in "Message[subject]" with "RE: how are you?"
        And I fill in "Message[body]" with "hey, how have you been?"
		And I press "Send Message"
		And I should see envelope "RE: how are you?"

	Scenario: Preview message to another user
        Given I am on "/en/messages"
		And I follow "Compose"
        And I fill in "Message[send_to]" with "user2"
        And I fill in "Message[subject]" with "RE: how are you?"
        And I fill in "Message[body]" with "hey, how have you been?"
		And I press "Preview"
		And I should see message preview "RE: how are you?"

	Scenario: Open Message from folder
        Given I am on "/en/messages"
		And I should see envelope "subject_f1_t1_1"
		And I follow "subject_f1_t1_1"
		And I should see message "body1"

	Scenario: Reply to Message
        Given I am on "/en/messages"
		And I should see envelope "subject_f2_t1_1"
		And I follow "subject_f2_t1_1"
		And I should see message "body2"
		And I follow "Reply"
        And I fill in "Message[send_to]" with "user2"
        And I fill in "Message[subject]" with "RE: RE: how are you?"
        And I fill in "Message[body]" with "hey, how have you been?"
		And I press "Send"
		And I should see envelope "RE: RE: how are you?"

	Scenario: Forward to Message
        Given I am on "/en/messages"
		And I should see envelope "subject_f2_t1_1"
		And I follow "subject_f2_t1_1"
		And I should see message "body2"
		And I follow "Forward"
        And I fill in "Message[send_to]" with "user2"
        And I fill in "Message[subject]" with "FWD: RE: how are you?"
        And I fill in "Message[body]" with "hey, how have you been?"
		And I press "Send"
		And I should see envelope "FWD: RE: how are you?"

	Scenario: Mark message as unread
        Given I am on "/en/messages"
		And I should see envelope "subject_f1_t1_1"
		And I follow "subject_f1_t1_1"
		And I should see message "body1"
		And I follow "Mark Unread"
		And I should see envelope "subject_f1_t1_1" is unread

	Scenario: Delete message
        Given I am on "/en/messages"
		And I should see envelope "subject_f1_t1_1"
		And I follow "subject_f1_t1_1"
		And I should see message "body1"
		And I follow "Delete"
		And I should not see envelope "subject_f1_t1_1"
