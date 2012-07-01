set foreign_key_checks=0;

ALTER TABLE CC_Message_Folder DROP FOREIGN KEY FK_DA623A816DD3F56D;
DROP INDEX IDX_DA623A816DD3F56D ON CC_Message_Folder;
ALTER TABLE CC_Message_Folder 
	CHANGE owned_by_user_id fk_owned_by_user_id INT DEFAULT NULL, 
	CHANGE cache_read_count cached_read_count INT DEFAULT NULL, 
	CHANGE cache_unread_count cached_unread_count INT DEFAULT NULL, 
	CHANGE cache_total_message_count cached_total_message_count INT DEFAULT NULL;
ALTER TABLE CC_Message_Folder ADD CONSTRAINT FK_DA623A813BB9921A FOREIGN KEY (fk_owned_by_user_id) REFERENCES fos_user(id) ON DELETE SET NULL;
CREATE INDEX IDX_DA623A813BB9921A ON CC_Message_Folder (fk_owned_by_user_id);

ALTER TABLE CC_Message_Message DROP FOREIGN KEY FK_C9E1FDF72130303A;
ALTER TABLE CC_Message_Message DROP FOREIGN KEY FK_C9E1FDF73AEF91E7;
ALTER TABLE CC_Message_Message DROP FOREIGN KEY FK_C9E1FDF7464E68B;
ALTER TABLE CC_Message_Message DROP FOREIGN KEY FK_C9E1FDF76DD3F56D;
ALTER TABLE CC_Message_Message DROP FOREIGN KEY FK_C9E1FDF774B65DE1;
DROP INDEX IDX_C9E1FDF774B65DE1 ON CC_Message_Message;
DROP INDEX IDX_C9E1FDF73AEF91E7 ON CC_Message_Message;
DROP INDEX IDX_C9E1FDF72130303A ON CC_Message_Message;
DROP INDEX IDX_C9E1FDF76DD3F56D ON CC_Message_Message;
DROP INDEX IDX_C9E1FDF7464E68B ON CC_Message_Message;
ALTER TABLE CC_Message_Message 
	CHANGE in_folder_id fk_folder_id INT DEFAULT NULL, 
	CHANGE sent_to_user_id fk_sent_to_user_id INT DEFAULT NULL, 
	CHANGE from_user_id fk_from_user_id INT DEFAULT NULL, 
	CHANGE owned_by_user_id fk_owned_by_user_id INT DEFAULT NULL, 
	CHANGE attachment_id fk_attachment_id INT DEFAULT NULL, 
	CHANGE created_date created_date DATETIME DEFAULT NULL, 
	CHANGE read_it is_read TINYINT(1) NOT NULL, 
	CHANGE flagged is_flagged TINYINT(1) DEFAULT NULL;
ALTER TABLE CC_Message_Message ADD CONSTRAINT FK_C9E1FDF777756ED2 FOREIGN KEY (fk_folder_id) REFERENCES CC_Message_Folder(id) ON DELETE SET NULL;
ALTER TABLE CC_Message_Message ADD CONSTRAINT FK_C9E1FDF7E34CC5D2 FOREIGN KEY (fk_sent_to_user_id) REFERENCES fos_user(id) ON DELETE SET NULL;
ALTER TABLE CC_Message_Message ADD CONSTRAINT FK_C9E1FDF7ED3223 FOREIGN KEY (fk_from_user_id) REFERENCES fos_user(id) ON DELETE SET NULL;
ALTER TABLE CC_Message_Message ADD CONSTRAINT FK_C9E1FDF73BB9921A FOREIGN KEY (fk_owned_by_user_id) REFERENCES fos_user(id) ON DELETE SET NULL;
ALTER TABLE CC_Message_Message ADD CONSTRAINT FK_C9E1FDF7602E9349 FOREIGN KEY (fk_attachment_id) REFERENCES CC_Component_Attachment(id) ON DELETE SET NULL;
CREATE INDEX IDX_C9E1FDF777756ED2 ON CC_Message_Message (fk_folder_id);
CREATE INDEX IDX_C9E1FDF7E34CC5D2 ON CC_Message_Message (fk_sent_to_user_id);
CREATE INDEX IDX_C9E1FDF7ED3223 ON CC_Message_Message (fk_from_user_id);
CREATE INDEX IDX_C9E1FDF73BB9921A ON CC_Message_Message (fk_owned_by_user_id);
CREATE INDEX IDX_C9E1FDF7602E9349 ON CC_Message_Message (fk_attachment_id);

ALTER TABLE CC_Message_Registry DROP FOREIGN KEY FK_2EB44AD56DD3F56D;
DROP INDEX IDX_2EB44AD56DD3F56D ON CC_Message_Registry;
ALTER TABLE CC_Message_Registry 
	CHANGE owned_by_user_id fk_owned_by_user_id INT DEFAULT NULL,
	CHANGE cacheunreadmessagescount cached_unread_message_count INT NOT NULL;
ALTER TABLE CC_Message_Registry ADD CONSTRAINT FK_2EB44AD53BB9921A FOREIGN KEY (fk_owned_by_user_id) REFERENCES fos_user(id) ON DELETE SET NULL;
CREATE INDEX IDX_2EB44AD53BB9921A ON CC_Message_Registry (fk_owned_by_user_id);

set foreign_key_checks=1;