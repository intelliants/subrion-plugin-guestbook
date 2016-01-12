<?php
//##copyright##

$iaDb->setTable('guestbook');

if (iaView::REQUEST_JSON == $iaView->getRequestType())
{
	$iaGrid = $iaCore->factory('grid', iaCore::ADMIN);

	switch ($pageAction)
	{
		case iaCore::ACTION_READ:
			$params = array();
			if (isset($_GET['text']) && $_GET['text'])
			{
				$stmt = '(`author_name` LIKE :text OR `body` LIKE :text OR `email` LIKE :text OR `author_url` LIKE :text)';
				$iaDb->bind($stmt, array('text' => '%' . $_GET['text'] . '%'));

				$params[] = $stmt;
			}

			$output = $iaGrid->gridRead($_GET,
				array('member_id', 'author_name', 'body', 'email', 'date', 'status'),
				array('status' => 'equal'),
				$params
			);

			break;

		case iaCore::ACTION_EDIT:
			$output = $iaGrid->gridUpdate($_POST);

			break;

		case iaCore::ACTION_DELETE:
			$output = $iaGrid->gridDelete($_POST);
/*
			if ($output['result'])
			{
				$iaLog = $iaCore->factory('log');
				foreach ($_POST['id'] as $id)
				{
					$iaLog->write(iaLog::ACTION_DELETE, array('item' => 'guestbook', 'name' => '', 'id' => (int)$id));
				}
			}*/
	}

	$iaView->assign($output);
}

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	if (iaCore::ACTION_EDIT == $pageAction && isset($iaCore->requestPath[0]))
	{
		if (iaCore::ACTION_EDIT == $pageAction && !isset($iaCore->requestPath[0]))
		{
			return iaView::errorPage(iaView::ERROR_NOT_FOUND);
		}

		iaBreadcrumb::replaceEnd(iaLanguage::get('edit'));

		$guestbook = array(
			'status' => iaCore::STATUS_ACTIVE
		);

		if (iaCore::ACTION_EDIT == $pageAction)
		{
			$id = (int)$iaCore->requestPath[0];
			$guestbook = $iaDb->row(iaDb::ALL_COLUMNS_SELECTION, iaDb::convertIds($id));
		}

		$guestbook = array(
			'id' => isset($id) ? $id : 0,
			'author_name' => iaUtil::checkPostParam('author_name', $guestbook),
			'email' => iaUtil::checkPostParam('email', $guestbook),
			'member_id' => iaUtil::checkPostParam('member_id', $guestbook),
			'author_url' => iaUtil::checkPostParam('author_url', $guestbook),
			'body' => iaUtil::checkPostParam('body', $guestbook),
			'status' => iaUtil::checkPostParam('status', $guestbook),
			'date' => iaUtil::checkPostParam('date', $guestbook),
		);

		if (isset($_POST['save']))
		{
			iaUtil::loadUTF8Functions('ascii', 'validation', 'bad');

			$error = false;
			$messages = array();

			if (utf8_is_valid($guestbook['author_name']))
			{
				$guestbook['author_name'] = utf8_bad_replace($guestbook['author_name']);
			}

			if (isset($_POST['status']))
			{
				$guestbook['status'] = isset($_POST['status']) && !empty($_POST['status']) && in_array($_POST['status'], array(iaCore::STATUS_ACTIVE, iaCore::STATUS_INACTIVE)) ? $_POST['status'] : 'inactive';
			}

			if (isset($_POST['email']) && iaValidate::isEmail($_POST['email']))
			{
				$guestbook['email'] = $_POST['email'];
			}

			if (!$error && iaCore::ACTION_EDIT == $pageAction)
			{
				$id = $guestbook['id'] = (int)$iaCore->requestPath[0];
				$result = $iaDb->update($guestbook);
				$messages[] = iaLanguage::get('saved');

				if ($result)
				{
					$iaCore->factory('log')->write(iaLog::ACTION_UPDATE, array('item' => '', 'name' => iaLanguage::get('guestbook_message'), 'id' => $id, 'module' => 'guestbook'));
				}
			}

			$iaView->setMessages($messages, $error ? iaView::ERROR : iaView::SUCCESS);
			iaUtil::go_to(IA_ADMIN_URL . 'guestbook/');
		}

		$iaView->assign('guestbook', $guestbook);
		$iaView->display('index');
	}
	else
	{
		$iaView->grid('_IA_URL_plugins/guestbook/js/admin/index');
	}
}

$iaDb->resetTable();