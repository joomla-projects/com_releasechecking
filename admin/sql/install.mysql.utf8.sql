CREATE TABLE IF NOT EXISTS `#__releasechecking_release_check` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`asset_id` INT(10) unsigned NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
	`action` INT(11) NOT NULL DEFAULT 0,
	`context` INT(11) NOT NULL DEFAULT 0,
	`created_by` INT(11) NOT NULL DEFAULT 0,
	`joomla_version` INT(11) NOT NULL DEFAULT 0,
	`outcome` TINYINT(1) NOT NULL DEFAULT -1,
	`params` text NULL,
	`published` TINYINT(3) NOT NULL DEFAULT 1,
	`modified_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`checked_out` int(11) unsigned NOT NULL DEFAULT 0,
	`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`version` INT(10) unsigned NOT NULL DEFAULT 1,
	`hits` INT(10) unsigned NOT NULL DEFAULT 0,
	`access` INT(10) unsigned NOT NULL DEFAULT 0,
	`ordering` INT(11) NOT NULL DEFAULT 0,
	PRIMARY KEY  (`id`),
	KEY `idx_context` (`context`),
	KEY `idx_action` (`action`),
	KEY `idx_outcome` (`outcome`),
	KEY `idx_created_by` (`created_by`),
	KEY `idx_access` (`access`),
	KEY `idx_checkout` (`checked_out`),
	KEY `idx_modifiedby` (`modified_by`),
	KEY `idx_state` (`published`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__releasechecking_joomla_version` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`asset_id` INT(10) unsigned NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
	`alias` CHAR(64) NOT NULL DEFAULT '',
	`name` VARCHAR(255) NOT NULL DEFAULT '',
	`params` text NULL,
	`published` TINYINT(3) NOT NULL DEFAULT 1,
	`created_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`modified_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`checked_out` int(11) unsigned NOT NULL DEFAULT 0,
	`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`version` INT(10) unsigned NOT NULL DEFAULT 1,
	`hits` INT(10) unsigned NOT NULL DEFAULT 0,
	`access` INT(10) unsigned NOT NULL DEFAULT 0,
	`ordering` INT(11) NOT NULL DEFAULT 0,
	PRIMARY KEY  (`id`),
	KEY `idx_name` (`name`),
	KEY `idx_alias` (`alias`),
	KEY `idx_access` (`access`),
	KEY `idx_checkout` (`checked_out`),
	KEY `idx_createdby` (`created_by`),
	KEY `idx_modifiedby` (`modified_by`),
	KEY `idx_state` (`published`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__releasechecking_context` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`asset_id` INT(10) unsigned NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
	`alias` CHAR(64) NOT NULL DEFAULT '',
	`name` VARCHAR(255) NOT NULL DEFAULT '',
	`params` text NULL,
	`published` TINYINT(3) NOT NULL DEFAULT 1,
	`created_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`modified_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`checked_out` int(11) unsigned NOT NULL DEFAULT 0,
	`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`version` INT(10) unsigned NOT NULL DEFAULT 1,
	`hits` INT(10) unsigned NOT NULL DEFAULT 0,
	`access` INT(10) unsigned NOT NULL DEFAULT 0,
	`ordering` INT(11) NOT NULL DEFAULT 0,
	PRIMARY KEY  (`id`),
	KEY `idx_name` (`name`),
	KEY `idx_alias` (`alias`),
	KEY `idx_access` (`access`),
	KEY `idx_checkout` (`checked_out`),
	KEY `idx_createdby` (`created_by`),
	KEY `idx_modifiedby` (`modified_by`),
	KEY `idx_state` (`published`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `#__releasechecking_action` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`asset_id` INT(10) unsigned NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
	`alias` CHAR(64) NOT NULL DEFAULT '',
	`context` INT(11) NOT NULL DEFAULT 0,
	`description` TEXT NOT NULL,
	`name` VARCHAR(255) NOT NULL DEFAULT '',
	`params` text NULL,
	`published` TINYINT(3) NOT NULL DEFAULT 1,
	`created_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`modified_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`checked_out` int(11) unsigned NOT NULL DEFAULT 0,
	`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`version` INT(10) unsigned NOT NULL DEFAULT 1,
	`hits` INT(10) unsigned NOT NULL DEFAULT 0,
	`access` INT(10) unsigned NOT NULL DEFAULT 0,
	`ordering` INT(11) NOT NULL DEFAULT 0,
	PRIMARY KEY  (`id`),
	KEY `idx_name` (`name`),
	KEY `idx_context` (`context`),
	KEY `idx_alias` (`alias`),
	KEY `idx_access` (`access`),
	KEY `idx_checkout` (`checked_out`),
	KEY `idx_createdby` (`created_by`),
	KEY `idx_modifiedby` (`modified_by`),
	KEY `idx_state` (`published`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `#__releasechecking_joomla_version`
--

INSERT INTO `#__releasechecking_joomla_version` (`id`, `alias`, `name`, `published`) VALUES
(1, '5-0-1-rc2', '5.0.1 Release Candidate 2', 1),
(2, '5-0-1', '5.0.1', 1),
(3, '5-1-0-alpha1', '5.1.0 Alpha1', 1),
(4, '5-1-0-alpha2', '5.1.0 Alpha2', 1),
(5, '5-0-2-rc1', '5.0.2 Release Candidate 1', 1),
(6, '5-0-2-rc2', '5.0.2 Release Candidate 2', 1),
(7, '4-4-2-rc1', '4.4.2 Release Candidate 1', 1),
(8, '5-0-2', '5.0.2', 1),
(9, '4-4-2', '4.4.2', 1),
(10, '5-1-0-alpha3', '5.1.0 Alpha3', 1);

--
-- Dumping data for table `#__releasechecking_context`
--

INSERT INTO `#__releasechecking_context` (`id`, `alias`, `name`, `published`, `created`) VALUES
(1, 'com_installer', 'com_installer', 1, '2020-10-31 14:20:29'),
(2, 'com_users', 'com_users', 1, '2020-10-31 20:21:11'),
(3, 'com_menu', 'com_menu', 1, '2020-10-31 20:21:31'),
(4, 'com_categories-and-com_content', 'com_categories and com_content', 1, '2020-10-31 20:21:48'),
(5, 'com_fields', 'com_fields', 1, '2020-10-31 20:22:20'),
(6, 'com_tags', 'com_tags', 1, '2020-10-31 20:22:37'),
(7, 'com_media', 'com_media', 1, '2020-10-31 20:22:56'),
(9, 'com_plugins-com_installer', 'com_plugins (+ com_installer)', 1, '2020-10-31 20:25:17'),
(10, 'com_modules', 'com_modules', 1, '2020-10-31 20:25:32'),
(11, 'com_templates', 'com_templates', 1, '2020-10-31 20:25:50'),
(12, 'com_languages', 'com_languages', 1, '2020-10-31 20:26:10'),
(13, 'com_config', 'com_config', 1, '2020-10-31 20:26:43'),
(14, 'editors', 'Editors', 1, '2020-10-31 20:26:59'),
(15, 'com_admin-and-com_cache', 'com_admin and com_cache', 1, '2020-10-31 20:27:36'),
(16, 'com_search', 'com_search', 1, '2020-10-31 20:27:59'),
(17, 'com_finder', 'com_finder', 1, '2020-10-31 20:28:53'),
(18, 'com_redirect', 'com_redirect', 1, '2020-10-31 20:29:11'),
(19, 'com_weblinks', 'com_weblinks', 1, '2020-10-31 20:29:54'),
(20, 'com_privacy', 'com_privacy', 1, '2020-10-31 20:30:15'),
(21, 'com_actionlog', 'com_actionlog', 1, '2020-10-31 20:30:48'),
(22, 'com_login', 'com_login', 1, '2020-10-31 20:31:03'),
(23, 'com_contenthistory', 'com_contenthistory', 1, '2020-10-31 20:31:17'),
(24, 'com_contact', 'com_contact', 1, '2020-10-31 20:31:36'),
(25, 'com_checkin', 'com_checkin', 1, '2020-10-31 20:31:52'),
(26, 'com_banners', 'com_banners', 1, '2020-10-31 20:32:11'),
(27, 'com_mailto-only-frontend', 'com_mailto (only frontend)', 1, '2020-10-31 20:32:47'),
(28, 'category-filter-in-the-featured-view', 'category filter in the featured view', 1, '2020-10-31 20:33:08'),
(29, 'whitespace-characters-etc-new-users', 'whitespace characters etc new users', 1, '2020-10-31 20:33:31'),
(30, 'com_fields-sql-field', 'com_fields SQL field', 1, '2020-10-31 20:33:51'),
(31, 'beez3-and-protostar-forms,-button-groups', 'beez3 and protostar forms, button groups', 1, '2020-10-31 20:34:18'),
(32, 'template-crop-and-resize-image-functionality', 'template crop and resize image functionality', 1, '2020-10-31 20:34:37'),
(33, 'superusers-edit-templates-in-backend', 'superusers edit templates in backend', 1, '2020-10-31 20:35:04'),
(34, 'com_joomlaupdate', 'com_joomlaupdate', 1, '2020-10-31 20:35:28'),
(35, 'recaptcha', 'Recaptcha', 1, '2020-10-31 20:35:46');

--
-- Dumping data for table `#__releasechecking_action`
--

INSERT INTO `#__releasechecking_action` (`id`, `alias`, `context`, `description`, `name`, `published`) VALUES
(1, 'installing-joomla', 1, '', 'Installing Joomla', 1),
(2, 'adding-new-user-backend', 2, '', 'Adding new user (Backend)', 1),
(3, 'amending-user-details', 2, '', 'Amending user details', 1),
(4, 'amending-user-access-privileges', 2, '', 'Amending user access privileges', 1),
(5, 'deleting-user', 2, '', 'Deleting user', 1),
(6, 'creating-new-user-group', 2, '', 'Creating new User Group', 1),
(7, 'configuring-viewing-access-levels-for-new-user-group', 2, '', 'Configuring Viewing Access Levels for new User Group', 1),
(8, 'deleting-user-group', 2, '', 'Deleting User Group', 1),
(9, 'email-notifications', 2, '', 'Email notifications', 1),
(10, 'creating-new-menu', 3, '', 'Creating new Menu', 1),
(11, 'linking-module-to-new-menu', 3, '', 'Linking Module to new Menu', 1),
(12, 'create-new-menu-item', 3, '', 'Create new Menu Item', 1),
(13, 'deleting-menu-item', 3, '', 'Deleting Menu Item', 1),
(14, 'deleting-menu', 3, '', 'Deleting Menu', 1),
(15, 'empty-menu-trash', 3, '', 'Empty Menu Trash', 1),
(16, 'create-new-category', 4, '', 'Create new Category', 1),
(17, 'create-new-article', 4, '', 'Create new Article', 1),
(18, 'assign-category-to-article', 4, '', 'Assign category to article', 1),
(19, 'toggle-featured-status-on-article', 4, '', 'Toggle Featured status on article', 1),
(20, 'delete-category', 4, '', 'Delete category', 1),
(21, 'clear-category-trash', 4, '', 'Clear Category Trash', 1),
(22, 'delete-article', 4, '', 'Delete article', 1),
(23, 'clear-article-trash', 4, '', 'Clear Article Trash', 1),
(24, 'archive-article', 4, '', 'Archive Article', 1),
(25, 'un-archive-article', 4, '', 'Un-Archive Article', 1),
(26, 'check-versioning', 4, '', 'Check Versioning', 1),
(27, 'create-field-group', 5, '', 'Create Field Group', 1),
(28, 'create-field', 5, '', 'Create Field', 1),
(29, 'fill-in-field-in-an-article', 5, '', 'Fill-in field in an article', 1),
(30, 'delete-field', 5, '', 'Delete Field', 1),
(31, 'clear-field-trash', 5, '', 'Clear Field Trash', 1),
(32, 'delete-field-group', 5, '', 'Delete Field Group', 1),
(33, 'clear-field-group-trash', 5, '', 'Clear Field Group Trash', 1),
(34, 'create-new-tag', 6, '', 'Create new Tag', 1),
(35, 'assign-tag-to-article', 6, '', 'Assign tag to article', 1),
(36, 'remove-tag-from-article', 6, '', 'Remove tag from article', 1),
(37, 'delete-tag', 6, '', 'Delete Tag', 1),
(38, 'clear-tag-trash', 6, '', 'Clear Tag trash', 1),
(39, 'add-to-the-menu-system-and-check-tags-»-compact-list-of-tagged-i', 6, '', 'Add to the menu system and check Tags » Compact List of Tagged Items', 1),
(40, 'add-to-the-menu-system-and-check-tags-»-list-all-tags', 6, '', 'Add to the menu system and check Tags » List All Tags', 1),
(41, 'add-to-the-menu-system-and-check-tags-»-tagged-items', 6, '', 'Add to the menu system and check Tags » Tagged Items', 1),
(42, 'media-manager-back-end-changing-legal-extensions,-legal-image-ex', 7, '', 'Media Manager Back-end - Changing Legal Extensions, Legal Image Extensions, and Legal MIME Types', 1),
(43, 'media-manager-back-end-uploading-image', 7, '', 'Media Manager Back-end - Uploading image', 1),
(44, 'media-manager-back-end-deleting-image', 7, '', 'Media Manager Back-end - Deleting image', 1),
(45, 'media-manager-back-end-uploading-video', 7, '', 'Media Manager Back-end - Uploading video', 1),
(46, 'media-manager-back-end-deleting-video', 7, '', 'Media Manager Back-end - Deleting video', 1),
(47, 'media-manager-back-end-create-folder', 7, '', 'Media Manager Back-end - Create Folder', 1),
(48, 'media-manager-back-end-delete-folder', 7, '', 'Media Manager Back-end - Delete Folder', 1),
(49, 'drag-n-drop-adding-new-image-in-tiny-mce-specify-folder-in-plugi', 7, '', 'Drag\'n\'Drop adding new image in Tiny MCE (specify folder in plugin)', 1),
(50, 'install-a-component-via-browse-for-file', 1, '', 'Install a component via \"Browse for File\"', 1),
(51, 'install-a-component-via-drag-n-drop', 1, '', 'Install a component via Drag\'n\'Drop', 1),
(52, 'update-component', 1, '', 'Update component', 1),
(53, 'uninstall-a-component', 1, '', 'Uninstall a component', 1),
(54, 'install-a-plugin', 9, '', 'Install a plugin', 1),
(55, 'turn-on-plugin', 9, '', 'Turn on plugin', 1),
(56, 'update-plugin', 9, '', 'Update plugin', 1),
(57, 'turn-off-plugin', 9, '', 'Turn off plugin', 1),
(58, 'uninstall-a-plugin', 9, '', 'Uninstall a plugin', 1),
(59, 'install-module', 10, '', 'Install module', 1),
(60, 'assign-module-to-module-position', 10, '', 'Assign module to module position', 1),
(61, 'create-new-module-position', 10, '', 'Create new module position', 1),
(62, 'remove-module-from-module-position', 10, '', 'Remove module from module position', 1),
(63, 'delete-module', 10, '', 'Delete Module', 1),
(64, 'clear-module-trash', 10, '', 'Clear Module trash', 1),
(65, 'install-a-template', 11, '', 'Install a template', 1),
(66, 'turn-on-template', 11, '', 'Turn on template', 1),
(67, 'remove-template', 11, '', 'Remove template', 1),
(68, 'install-language-package', 12, '', 'Install Language Package', 1),
(69, 'uninstall-language-package', 12, '', 'Uninstall Language Package', 1),
(70, 'language-associations', 12, '', 'Language associations', 1),
(71, 'global-configuration-change-site-name', 13, '', 'Global Configuration: Change Site Name', 1),
(72, 'global-configuration-set-site-to-offline', 13, '', 'Global Configuration: Set site to Offline', 1),
(73, 'global-configuration-set-site-online', 13, '', 'Global Configuration: Set site Online', 1),
(74, 'global-configuration-configure-mail-settings,-and-send-test-mail', 13, '', 'Global Configuration: Configure Mail Settings, and send test mail', 1),
(75, 'install-new-editor-program', 14, '', 'Install new Editor program', 1),
(76, 'global-configuration-change-default-editor', 14, '', 'Global Configuration: Change Default Editor', 1),
(77, 'clear-cache', 15, '', 'Clear Cache', 1),
(78, 'clear-expire-cache', 15, '', 'Clear Expire Cache', 1),
(79, 'switch-on-statistics-gathering', 16, '', 'Switch on statistics gathering', 1),
(80, 'add-to-menu', 16, '', 'Add to menu', 1),
(81, 'switch-off-statistics-gathering', 16, '', 'Switch off statistics gathering', 1),
(82, 'switch-on-content-smart-search-plugin', 17, '', 'Switch on Content Smart search plugin', 1),
(83, 'index-content', 17, '', 'Index content', 1),
(84, 'check-search-is-working-on-the-front', 17, '', 'Check search is working on the front', 1),
(85, 'switch-off-content-smart-search-plugin', 17, '', 'Switch off Content Smart search plugin', 1),
(86, 'switch-on-redirect-plugin', 18, '', 'Switch on Redirect Plugin', 1),
(87, 'create-a-url-to-redirect', 18, '', 'create a url to redirect', 1),
(88, 'add-a-redirect-rule,-test-it-redir', 18, '', ' Add a redirect rule, test it redir', 1),
(89, 'test-the-redirect', 18, '', 'Test the redirect', 1),
(90, 'turn-redirect-off', 18, '', 'Turn redirect off', 1),
(91, 'test-it-no-longer-works', 18, '', 'Test it no longer works', 1),
(92, 'check-if-user-activities-are-logged', 21, '', 'Check if user activities are logged', 1),
(93, 'live-update-with-custom-url-for-rc', 34, '', 'Live Update (with custom URL for RC)', 1),
(94, 'upload-update-with-zip-package', 34, '', 'Upload & Update (with ZIP package)', 1),
(95, 'make-sure-google-recaptcha-still-works', 35, '', 'Make sure Google recaptcha still works', 1);


