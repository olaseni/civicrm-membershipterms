DROP TABLE IF EXISTS `civicrm_membershipterms`;

-- /*******************************************************
-- *
-- * civicrm_membershipterms
-- *
-- * FIXME
-- *
-- *******************************************************/
CREATE TABLE `civicrm_membershipterms` (


  `id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Unique MembershipTerms ID',
  `contact_id` int unsigned NOT NULL   COMMENT 'FK to Contact',
  `modifier_contact_id` int unsigned NOT NULL   COMMENT 'FK to Modifier Contact',
  `membership_id` int unsigned NOT NULL   COMMENT 'FK to Membership table',
  `contribution_id` int unsigned NULL   COMMENT 'FK to Contribution table',
  `start_date` date    COMMENT 'Beginning of current uninterrupted membership term.',
  `end_date` date    COMMENT 'Current membership term expire date.',
  `number_of_terms` int unsigned NOT NULL   COMMENT '...'
  ,
  PRIMARY KEY (`id`)


  ,          CONSTRAINT FK_civicrm_membershipterms_contact_id FOREIGN KEY (`contact_id`) REFERENCES `civicrm_contact`(`id`) ON DELETE CASCADE,          CONSTRAINT FK_civicrm_membershipterms_modifier_contact_id FOREIGN KEY (`modifier_contact_id`) REFERENCES `civicrm_contact`(`id`) ON DELETE CASCADE,          CONSTRAINT FK_civicrm_membershipterms_membership_id FOREIGN KEY (`membership_id`) REFERENCES `civicrm_membership`(`id`) ON DELETE CASCADE,          CONSTRAINT FK_civicrm_membershipterms_contribution_id FOREIGN KEY (`contribution_id`) REFERENCES `civicrm_contribution`(`id`) ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;