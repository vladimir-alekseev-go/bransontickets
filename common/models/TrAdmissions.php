<?php

namespace common\models;

use common\tripium\Tripium;
use yii\db\ActiveQuery;
use yii\helpers\Json;

class TrAdmissions extends _source_TrAdmissions
{

    public $errors_add = [];
    public $errors_update = [];
    public $added = [];
    public $updated = [];

    public function getSourceData(): ?array
    {
        return (new Tripium)->getAttractions();
    }

    public function updateFromTripium(): bool
    {
        $hashes = self::find()->select('id, id_external, id_external_item, hash_summ')->asArray()->all();
        $tmp = [];
        foreach ($hashes as $it) {
            $tmp[$it['id_external_item'] . '_' . $it['id_external']] = $it;
        }
        $hashes = $tmp;
        unset($tmp);

        $tripium = new Tripium;
        $tripiumData = $tripium->getAttractions();

        if ($tripium->statusCode !== Tripium::STATUS_CODE_SUCCESS) {
            return false;
        }

        foreach ($tripiumData as $data) {
            $data['id'] = (int)$data['id'];
            if ($data['admissions']) {
                foreach ($data['admissions'] as $admission) {

                    $dataAdmission = [
                        'id_external' => $admission['id'],
                        'id_external_item' => $data['id'],
                        'name' => trim($admission['name']),
//						'inclusions'       => $admission['inclusions'],
//						'exclusions'       => $admission['exclusions'],
                    ];
                    $dataAdmission["hash_summ"] = md5(Json::encode($dataAdmission));
                    $hash = $data['id'] . '_' . $admission['id'];

                    /** @var TrAdmissions $model */
                    if (empty($hashes[$hash])) {
                        $model = new self;
                        $model->setAttributes($dataAdmission);
                        if ($model->save()) {
                            $this->added[] = $model->id_external;
                        } else {
                            $err = $model->getErrors();
                            if ($err) {
                                $this->errors_add[] = $err;
                            }
                        }
                    } else if ($dataAdmission["hash_summ"] !== $hashes[$hash]["hash_summ"]) {
                        $model = self::find()
                            ->where(
                                ["id_external" => $admission['id'], "id_external_item" => $data['id']]
                            )
                            ->one();
                        $model->setAttributes($dataAdmission);
                        if ($model->save()) {
                            $this->updated[] = $model->id_external;
                        } else {
                            $err = $model->getErrors();
                            if ($err) {
                                $this->errors_update[] = $err;
                            }
                        }
                    }
                    unset($hashes[$hash]);
                }
            }
        }

        if (!empty($hashes)) {
            foreach ($hashes as $it) {
                self::deleteAll(
                    "id_external = '" . $it["id_external"] . "' and id_external_item = " . $it["id_external_item"]
                );
            }
        }
        return true;
    }

    public function getActivePrices(): ActiveQuery
    {
        return $this->getTrAttractionsPrices()
            ->andOnCondition(['or', 'available > 0', 'free_sell=1']);
    }

    public function getPrices(): ActiveQuery
    {
        return $this->getTrAttractionsPrices();
    }
}
