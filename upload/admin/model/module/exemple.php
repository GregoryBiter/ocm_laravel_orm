<?php

$bootstrapPath = DIR_SYSTEM . 'library/laravel_orm/bootstrap.php';
if (!file_exists($bootstrapPath)) {
    die('Ошибка: Необходимо установить модуль laravel_orm. Файл ' . $bootstrapPath . ' не найден.');
}
require $bootstrapPath;

use Illuminate\Database\Capsule\Manager as Capsule;
class ModelModuleExemple extends Model {

    public function install() {
        if (!Capsule::schema()->hasTable('exemple')) {
            Capsule::schema()->create('exemple', function ($table) {
                $table->increments('id');
                $table->string('name', 255);
                $table->text('description');
            });
        }
    }
    public function uninstall() {
        Capsule::schema()->dropIfExists('exemple');
    }

    public function addProduct($data) {
        Capsule::table('exemple')->insert($data);
    }

    public function getRecords() {
        return Capsule::table('exemple')->get();
    }

    public function getRecord($id) {
        return Capsule::table('exemple')->where('id', $id)->first();
    }

    public function updateRecord($id, $data) {
        Capsule::table('exemple')->where('id', $id)->update($data);
    }

    public function deleteRecord($id) {
        Capsule::table('exemple')->where('id', $id)->delete();
    }
}