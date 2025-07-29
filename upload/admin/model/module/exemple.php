<?php

require_once DIR_SYSTEM . 'library/gbitstudio/orm.php';

use GbitStudio\ORM;

class ModelModuleExemple extends Model {

    private $tableName = 'exemple';

    public function migrate() {
        try {
            // Проверяем существование таблицы
            if (!$this->tableExists($this->tableName)) {
                $this->createTable();
            }
        } catch (\Exception $e) {
            throw new \Exception("Migration failed: " . $e->getMessage());
        }
    }

    public function unmigrate() {
        try {
            $this->dropTable();
        } catch (\Exception $e) {
            throw new \Exception("Unmigration failed: " . $e->getMessage());
        }
    }

    private function tableExists($tableName) {
        $pdo = ORM::getConnection()->getPdo();
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([DB_PREFIX . $tableName]);
        return $stmt->rowCount() > 0;
    }

    private function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . $this->tableName . "` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `description` text,
            `status` tinyint(1) DEFAULT 1,
            `date_added` datetime DEFAULT CURRENT_TIMESTAMP,
            `date_modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX `idx_name` (`name`),
            INDEX `idx_status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        ORM::getConnection()->getPdo()->exec($sql);
    }

    private function dropTable() {
        $sql = "DROP TABLE IF EXISTS `" . DB_PREFIX . $this->tableName . "`";
        ORM::getConnection()->getPdo()->exec($sql);
    }

    // CRUD операции
    public function addRecord($data) {
        return ORM::table($this->tableName)->insert($data);
    }

    public function getRecords() {
        return ORM::table($this->tableName)->get();
    }

    public function getRecord($id) {
        return ORM::table($this->tableName)->where('id', $id)->first();
    }

    public function updateRecord($id, $data) {
        return ORM::table($this->tableName)->where('id', $id)->update($data);
    }

    public function deleteRecord($id) {
        return ORM::table($this->tableName)->where('id', $id)->delete();
    }

    // Дополнительные методы
    public function getActiveRecords() {
        return ORM::table($this->tableName)->where('status', 1)->get();
    }

    public function searchByName($name) {
        return ORM::table($this->tableName)
            ->where('name', 'LIKE', '%' . $name . '%')
            ->get();
    }
}