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

    protected $_gridFilters = ['body' => self::LIKE, 'status' => self::EQUAL];
    protected $_gridQueryMainTableAlias = 'g';


    public function __construct()
    {
        parent::__construct();

        $this->setHelper($this->_iaCore->factoryModule($this->getModuleName(), iaCore::ADMIN));
    }

    protected function _indexPage(&$iaView)
    {
        $iaView->grid('_IA_URL_modules/' . $this->getModuleName() . '/js/admin/index');
    }

    protected function _gridQuery($columns, $where, $order, $start, $limit)
    {
        $sql = <<<SQL
SELECT SQL_CALC_FOUND_ROWS g.`id`, g.`author_name`, g.`email`, g.`body`, g.`date`, g.`status`, 1 `update`, 1 `delete` 
  FROM `:prefix:table` g 
WHERE :where :order 
LIMIT :start, :limit
SQL;
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
