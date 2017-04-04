<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2017 Intelliants, LLC <https://intelliants.com>
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
 * @link https://subrion.org/
 *
 ******************************************************************************/

class iaBackendController extends iaAbstractControllerModuleBackend
{
    protected $_name = 'guestbook';

    protected $_table = 'guestbook';

    protected $_gridFilters = ['status' => self::EQUAL];
    protected $_gridQueryMainTableAlias = 'g';


    public function __construct()
    {
        parent::__construct();

        $this->setHelper($this->_iaCore->factoryPlugin($this->getModuleName(), iaCore::ADMIN));
    }

    protected function _indexPage(&$iaView)
    {
        $iaView->grid('_IA_URL_modules/' . $this->getModuleName() . '/js/admin/index');
    }

    protected function _gridQuery($columns, $where, $order, $start, $limit)
    {
        $sql =
            'SELECT SQL_CALC_FOUND_ROWS '
            . 'g.`id`, g.`author_name`, g.`email`, g.`date`, g.`status`, '
            . '1 `update`, 1 `delete` '
            . 'FROM `:prefix:table` g '
            . 'WHERE :where :order '
            . 'LIMIT :start, :limit';

        $sql = iaDb::printf($sql, [
            'prefix' => $this->_iaDb->prefix,
            'table' => $this->getTable(),
            'where' => $where ? $where : iaDb::EMPTY_CONDITION,
            'order' => $order,
            'start' => $start,
            'limit' => $limit
        ]);

        return $this->_iaDb->getAll($sql);
    }

    protected function _preSaveEntry(array &$entry, array $data, $action)
    {
        parent::_preSaveEntry($entry, $data, $action);

        if (isset($_FILES['avatar']['error']) && !$_FILES['avatar']['error']) {
            try {
                $iaField = $this->_iaCore->factory('field');

                $path = $iaField->uploadImage($_FILES['avatar'], 100, 100, 100, 100, 'crop');

                empty($entry['avatar']) || $iaField->deleteUploadedFile('avatar', $this->getTable(), $this->getEntryId(), $entry['avatar']);
                $entry['avatar'] = $path;
            } catch (Exception $e) {
                $this->addMessage($e->getMessage(), false);
            }
        }

        return !$this->getMessages();
    }

    protected function _assignValues(&$iaView, array &$entryData)
    {

    }
}

//$iaDb->setTable('guestbook');
//
//if (iaView::REQUEST_JSON == $iaView->getRequestType()) {
//    $iaGrid = $iaCore->factory('grid', iaCore::ADMIN);
//
//    switch ($pageAction) {
//        case iaCore::ACTION_READ:
//            $params = array();
//            if (isset($_GET['text']) && $_GET['text']) {
//                $stmt = '(`author_name` LIKE :text OR `body` LIKE :text OR `email` LIKE :text OR `author_url` LIKE :text)';
//                $iaDb->bind($stmt, array('text' => '%' . $_GET['text'] . '%'));
//
//                $params[] = $stmt;
//            }
//
//            $output = $iaGrid->gridRead($_GET,
//                array('member_id', 'author_name', 'body', 'email', 'date', 'status'),
//                array('status' => 'equal'),
//                $params
//            );
//
//            break;
//
//        case iaCore::ACTION_EDIT:
//            $output = $iaGrid->gridUpdate($_POST);
//
//            break;
//
//        case iaCore::ACTION_DELETE:
//            $output = $iaGrid->gridDelete($_POST);
//        /*
//                    if ($output['result'])
//                    {
//                        $iaLog = $iaCore->factory('log');
//                        foreach ($_POST['id'] as $id)
//                        {
//                            $iaLog->write(iaLog::ACTION_DELETE, array('item' => 'guestbook', 'name' => '', 'id' => (int)$id));
//                        }
//                    }*/
//    }
//
//    $iaView->assign($output);
//}
//
//if (iaView::REQUEST_HTML == $iaView->getRequestType()) {
//    if (iaCore::ACTION_EDIT == $pageAction && isset($iaCore->requestPath[0])) {
//        if (iaCore::ACTION_EDIT == $pageAction && !isset($iaCore->requestPath[0])) {
//            return iaView::errorPage(iaView::ERROR_NOT_FOUND);
//        }
//
//        iaBreadcrumb::replaceEnd(iaLanguage::get('edit'));
//
//        $guestbook = array(
//            'status' => iaCore::STATUS_ACTIVE
//        );
//
//        if (iaCore::ACTION_EDIT == $pageAction) {
//            $id = (int)$iaCore->requestPath[0];
//            $guestbook = $iaDb->row(iaDb::ALL_COLUMNS_SELECTION, iaDb::convertIds($id));
//        }
//
//        $guestbook = array(
//            'id' => isset($id) ? $id : 0,
//            'author_name' => iaUtil::checkPostParam('author_name', $guestbook),
//            'email' => iaUtil::checkPostParam('email', $guestbook),
//            'member_id' => iaUtil::checkPostParam('member_id', $guestbook),
//            'author_url' => iaUtil::checkPostParam('author_url', $guestbook),
//            'body' => iaUtil::checkPostParam('body', $guestbook),
//            'status' => iaUtil::checkPostParam('status', $guestbook),
//            'avatar' => iaUtil::checkPostParam('avatar', $guestbook),
//            'date' => iaUtil::checkPostParam('date', $guestbook),
//        );
//
//        if (isset($_POST['save'])) {
//            iaUtil::loadUTF8Functions('ascii', 'validation', 'bad');
//
//            $error = false;
//            $messages = array();
//
//            $guestbook['avatar'] = iaSanitize::html($guestbook['avatar']);
//
//            if (utf8_is_valid($guestbook['author_name'])) {
//                $guestbook['author_name'] = utf8_bad_replace($guestbook['author_name']);
//            }
//
//            if (isset($_FILES['image']['tmp_name']) && $_FILES['image']['tmp_name']) {
//                $iaPicture = $iaCore->factory('picture');
//
//                $info = array(
//                    'image_width' => 500,
//                    'image_height' => 500,
//                    'thumb_width' => 150,
//                    'thumb_height' => 150,
//                    'resize_mode' => iaPicture::CROP
//                );
//
//                if ($image = $iaPicture->processImage($_FILES['image'], '', iaUtil::generateToken(), $info)) {
//                    empty($guestbook['avatar']) || $iaPicture->delete($guestbook['avatar']); // already has an assigned image
//                    $guestbook['avatar'] = $image;
//                }
//            }
//
//
//            if (isset($_POST['status'])) {
//                $guestbook['status'] = isset($_POST['status']) && !empty($_POST['status']) && in_array($_POST['status'],
//                    array(iaCore::STATUS_ACTIVE, iaCore::STATUS_INACTIVE)) ? $_POST['status'] : 'inactive';
//            }
//
//            if (isset($_POST['email']) && iaValidate::isEmail($_POST['email'])) {
//                $guestbook['email'] = $_POST['email'];
//            }
//
//            if (!$error && iaCore::ACTION_EDIT == $pageAction) {
//                $id = $guestbook['id'] = (int)$iaCore->requestPath[0];
//                $result = $iaDb->update($guestbook);
//                $messages[] = iaLanguage::get('saved');
//
//                if ($result) {
//                    $iaCore->factory('log')->write(iaLog::ACTION_UPDATE, array(
//                        'item' => '',
//                        'name' => iaLanguage::get('guestbook_message'),
//                        'id' => $id,
//                        'module' => 'guestbook'
//                    ));
//                }
//            }
//
//            $iaView->setMessages($messages, $error ? iaView::ERROR : iaView::SUCCESS);
//            iaUtil::go_to(IA_ADMIN_URL . 'guestbook/');
//        }
//
//        $iaView->assign('guestbook', $guestbook);
//        $iaView->display('index');
//    } else {
//        $iaView->grid('_IA_URL_modules/guestbook/js/admin/index');
//    }
//}
//
//$iaDb->resetTable();