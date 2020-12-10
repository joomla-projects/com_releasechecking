DROP TABLE IF EXISTS `#__release_checking_release_check`;
DROP TABLE IF EXISTS `#__release_checking_joomla_version`;
DROP TABLE IF EXISTS `#__release_checking_context`;
DROP TABLE IF EXISTS `#__release_checking_action`;


--
-- Always insure this column rules is reversed to Joomla defaults on uninstall. (as on 1st Dec 2020)
--
ALTER TABLE `#__assets` CHANGE `rules` `rules` varchar(5120) NOT NULL COMMENT 'JSON encoded access control.';
