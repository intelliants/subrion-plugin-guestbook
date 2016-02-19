<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2016 Intelliants, LLC <http://www.intelliants.com>
 *
 * This file is part of Subrion.
 *
 * Subrion is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Subrion is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Subrion. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @link http://www.subrion.org/
 *
 ******************************************************************************/

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	$iaDb->setTable('guestbook');

	if (isset($_POST['action']))
	{
		$error = false;
		$messages = array();
		$entry = array();

		if (iaCore::ACTION_ADD == $_POST['action'])
		{
			$iaUtil = $iaCore->factory('util');
			iaUtil::loadUTF8Functions('ascii', 'validation', 'bad', 'utf8_to_ascii');

			if (!iaUsers::hasIdentity() && !iaValidate::isCaptchaValid())
			{
				$error = true;
				$messages[] = iaLanguage::get('confirmation_code_incorrect');
				$data = $_POST;
			}

			// checking author
			if (isset($_POST['author']) && $_POST['author'])
			{
				$entry['author_name'] = $_POST['author'];

				/** check for author name **/
				if (!$entry['author_name'])
				{
					$error = true;
					$messages[] = iaLanguage::get('error_gb_author');
				}
				elseif (!utf8_is_valid($entry['author_name']))
				{
					$entry['author_name'] = utf8_bad_replace($entry['author_name']);
				}
			}
			else
			{
				$error = true;
				$messages[] = iaLanguage::get('error_gb_author');
			}

			$photo = isset($_FILES['photo']) ? $_FILES['photo'] : null;
			if (!empty($photo['name']) && !in_array($photo['type'], array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png')))
			{
				$error = true;
				$messages[] = iaLanguage::get('unsupported_image_type');
			}

			// checking email
			if (isset($_POST['email']) && $_POST['email'])
			{
				$entry['email'] = $_POST['email'];

				if (!iaValidate::isEmail($entry['email']))
				{
					$error = true;
					$messages[] = iaLanguage::get('error_gb_email');
				}
			}
			else
			{
				$error = true;
				$messages[] = iaLanguage::get('error_gb_email');
			}

			// checking email
			if (isset($_POST['aurl']) && !empty($_POST['aurl']) && 'http://' != $_POST['aurl'])
			{
				$entry['author_url'] = $_POST['aurl'];
				if (!iaValidate::isUrl($entry['author_url']))
				{
					$error = true;
					$messages[] = iaLanguage::get('error_url');
				}
			}

			// checking body
			$entry['body'] = $_POST['message'];

			if (!utf8_is_valid($entry['body']))
			{
				$entry['body'] = utf8_bad_replace($entry['body']);
			}

			$length = utf8_is_ascii($entry['body'])
				? strlen($entry['body'])
				: utf8_strlen($entry['body']);

			if ($iaCore->get('gb_min_chars') > 0)
			{
				if ($length < $iaCore->get('gb_min_chars'))
				{
					$error = true;
					$messages[] = iaLanguage::getf('error_min_gb', array('length' => $iaCore->get('gb_min_chars')));
				}
			}

			if ($iaCore->get('gb_max_chars') > 0)
			{
				if ($length > $iaCore->get('gb_max_chars'))
				{
					$error = true;
					$messages[] = iaLanguage::getf('error_max_gb', array('length' => $iaCore->get('gb_max_chars')));
				}
			}

			if (empty($entry['body']))
			{
				$error = true;
				$messages[] = iaLanguage::get('error_gb');
			}
			else
			{
				$entry['body'] = $iaUtil->safeHTML($entry['body']);
			}

			if (!$error)
			{
				$iaPicture = $iaCore->factory('picture');
				$tok = 'photo_' . iaUtil::generateToken();

				$imageInfo = array(
					'image_width' => 500,
					'image_height' => 500,
					'resize_mode' => iaPicture::CROP
				);

				$name = $iaPicture->processImage($photo, 'guestbook/', $tok, $imageInfo);
				$entry['avatar'] = $name;
				$entry['member_id'] = iaUsers::hasIdentity() ? iaUsers::getIdentity()->id : 0;
				$entry['sess_id'] = session_id();
				$entry['ip'] = $iaCore->util()->getIp();
				$entry['status'] = $iaCore->get('gb_auto_approval') ? iaCore::STATUS_ACTIVE : iaCore::STATUS_INACTIVE;

				$id = $iaDb->insert($entry, array('date' => iaDb::FUNCTION_NOW));
				unset($entry);

				if ($id)
				{
					$iaCore->factory('log')->write(iaLog::ACTION_CREATE, array('item' => '', 'name' => iaLanguage::get('guestbook_message'), 'id' => $id, 'path' => 'guestbook'));
				}

				$messages[] = iaLanguage::get('message_added') . (!$iaCore->get('gb_auto_approval') ? ' ' . iaLanguage::get('message_approval') : '');
			}
		}

		$iaView->setMessages($messages, $error ? iaView::ERROR : iaView::SUCCESS);
	}

	$total = $iaDb->one(iaDb::STMT_COUNT_ROWS, "`status`='active'");

	$page = isset($_GET['page']) ? $_GET['page'] : 1;
	$limit = $iaCore->get('gb_messages_per_page');
	if ($page > $total / $limit && $page < 0 || !is_numeric($page))
	{
		$page = 1;
	}
	$start = ($page - 1) * $limit;

	$sql = "SELECT g.*, IF (g.`member_id` > 0, if (a.`fullname` <> '', a.`fullname`, a.`username`), g.`author_name`) author, a.`username` username
			FROM `" . $iaCore->iaDb->prefix . "guestbook` g
			LEFT JOIN `" . $iaCore->iaDb->prefix . "members` a ON (g.`member_id` = a.`id`)
		WHERE g.`status` = 'active' "
			. (iaUsers::hasIdentity() ? "OR g.`status` = '" . iaCore::STATUS_INACTIVE . "' AND g.`member_id` = '" . iaUsers::getIdentity()->id . "'" : '')
			. "OR g.`status` = '" . iaCore::STATUS_INACTIVE . "' AND g.`sess_id` = '" . session_id() . "'
		ORDER BY  g.`date` DESC"
		. ($limit ? " LIMIT $start, $limit" : '');

	$messages = $iaDb->getAll($sql);

	$iaView->assign('aTemplate', IA_URL . 'guestbook/?page={page}');
	$iaView->assign('body', isset($entry['body']) ? $entry['body'] : '');
	$iaView->assign('guestbook', $messages);
	$iaView->assign('sess_id', session_id());
	$iaView->assign('total_messages', $total);

	$iaView->display('index');

	$iaDb->resetTable();
}