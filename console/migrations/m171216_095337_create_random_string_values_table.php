<?php

use common\models\helpers\RandomStringValue;
use yii\db\Migration;

/**
 * Handles the creation of table `random_string_values`.
 */
class m171216_095337_create_random_string_values_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('random_string_values', [
            'id'          => $this->primaryKey(),
            'entity_type' => $this->string(),
            'value'       => $this->string(),
        ], $tableOptions);


        $imgs = [
            'http://mirpozitiva.ru/uploads/posts/2017-05/1496160781_begemotik.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471433750_10.jpg',
            'http://mirpozitiva.ru/uploads/posts/2017-05/1496160764_deva_na_velosipede.jpg',
            'http://mirpozitiva.ru/uploads/posts/2017-05/1496160754_utro.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434359_013.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434372_08.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434380_011.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434387_07.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434412_01.jpg',
            'http://mirpozitiva.ru/uploads/posts/2017-05/1496160735_kapkeiki.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434379_02.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434400_03.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434341_04.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434405_021.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434371_020.jpg',
            'http://mirpozitiva.ru/uploads/posts/2017-05/1496160823_kotik.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434429_028.jpg',
            'http://mirpozitiva.ru/uploads/posts/2017-05/1496160759_slonik.jpg',
            'http://mirpozitiva.ru/uploads/posts/2017-05/1496160818_sova.jpg',
            'http://mirpozitiva.ru/uploads/posts/2017-05/1496160739_sovi.jpg',
            'http://mirpozitiva.ru/uploads/posts/2017-05/1496160782_tortik.jpg',
            'http://mirpozitiva.ru/uploads/posts/2017-05/1496160773_sladkie.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434387_030.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/medium/1471434417_035.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/1471434894_-6.jpg',
            'http://mirpozitiva.ru/uploads/posts/2016-08/1471434869_-18.jpg',
        ];


        foreach ($imgs as $img) {
            $randomStringValue = new RandomStringValue();
            $randomStringValue->setAttributes([
                'entity_type' => RandomStringValue::ENTITY_TYPE_BDAY,
                'value'       => $img,
            ]);
            $randomStringValue->save();
        }
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('random_string_values');
    }
}
