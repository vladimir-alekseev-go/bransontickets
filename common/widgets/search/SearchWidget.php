<?php

namespace common\widgets\search;

use common\models\TrAttractions;
use common\models\TrPosPlHotels;
use common\models\TrShows;
use common\models\TrTheaters;
use common\widgets\vacationPackagesList\VacationPackagesListWidget;
use Yii;
use yii\base\Widget;
use yii\db\ActiveQuery;

class SearchWidget extends Widget
{
    public $query;

    public $shows = [];
    public $attractions = [];
    public $hotels = [];
    public $packages = [];

    public $VPLWidget;

    public $formatWrap = '<span class="sq">%s</span>';
    public $searchForm;

    public function init()
    {
        $this->searchForm = new SearchForm;
        $this->searchForm->load(Yii::$app->getRequest()->get());

        if (!empty($this->searchForm->q) && $this->searchForm->validate()) {
            $this->query = $this->searchForm->q;
            $this->search();
            $this->formatSearchResult();
        }
    }

    protected function search(): void
    {
        $this->shows = TrShows::getAvailable()->with(
            [
                'preview',
                'availablePricesByRange' => static function (ActiveQuery $query) {
                    $query->groupBy(['start', 'id_external'])->orderBy(['start' => SORT_ASC]);
                }
            ]
        )->joinWith(['theatre'])->andFilterWhere(
            [
                'or',
                ['like', TrShows::tableName() . '.name', $this->query],
                ['like', TrShows::tableName() . '.name', ' ' . $this->query . ' '],
                ['like', TrShows::tableName() . '.name', '% ' . $this->query, false],
                ['like', TrShows::tableName() . '.name', $this->query . ' %', false],
                ['like', TrTheaters::tableName() . '.name', '%' . $this->query . '%', false],
                ['like', TrShows::tableName() . '.description', ' ' . $this->query . ' '],
                ['like', TrShows::tableName() . '.description', $this->query . ' %', false],
                ['like', TrShows::tableName() . '.description', '% ' . $this->query, false],
            ]
        )->all();

        $this->attractions = TrAttractions::getAvailable()->with(
            [
                'preview',
                'availablePricesByRange' => static function (ActiveQuery $query) {
                    $query->groupBy(['start', 'id_external'])->orderBy(['start' => SORT_ASC]);
                }
            ]
        )->joinWith(['theatre'])->andFilterWhere(
            [
                'or',
                ['like', TrAttractions::tableName() . '.name', $this->query],
                ['like', TrAttractions::tableName() . '.name', ' ' . $this->query . ' '],
                ['like', TrAttractions::tableName() . '.name', '% ' . $this->query, false],
                ['like', TrAttractions::tableName() . '.name', $this->query . ' %', false],
                ['like', TrTheaters::tableName() . '.name', '%' . $this->query . '%', false],
                ['like', TrAttractions::tableName() . '.description', ' ' . $this->query . ' '],
                ['like', TrAttractions::tableName() . '.description', $this->query . ' %', false],
                ['like', TrAttractions::tableName() . '.description', '% ' . $this->query, false],
            ]
        )->all();

        $this->hotels = TrPosPlHotels::getActive()->with(['preview'])->andFilterWhere(
            [
                'or',
                ['like', 'name', $this->query],
                ['like', 'name', ' ' . $this->query . ' '],
                ['like', 'name', '% ' . $this->query, false],
                ['like', 'name', $this->query . ' %', false],
                ['like', 'description', ' ' . $this->query . ' '],
                ['like', 'description', $this->query . ' %', false],
                ['like', 'description', '% ' . $this->query, false],
            ]
        )->all();

        $this->initVacationPackagesListWidget();
        $this->VPLWidget->search->title = $this->query;
    }

    protected function initVacationPackagesListWidget()
    {
        $this->VPLWidget = new VacationPackagesListWidget(['layout' => 'search', 'pageSize' => 0]);
    }

    protected function formatSearchResult()
    {
        foreach ($this->shows as $item) {
            $this->prepareItem($item);
        }

        foreach ($this->attractions as $item) {
            $this->prepareItem($item);
        }

        foreach ($this->hotels as $item) {
            $this->prepareItem($item);
        }
    }

    protected function prepareItem($item)
    {
        $item->name = $this->wrap($item->name);
        $item->description = $this->wrap($this->prepareSearchResult($item->description));
        if (!empty($item->theatre)) {
            if (!$item->theatre->isAttributeChanged('name')) {
                $item->theatre->name = $this->wrap($item->theatre->name);
            }
        }

        return $item;
    }

    protected function prepareSearchResult($str, $len = 50)
    {
        $str = strip_tags(htmlspecialchars_decode($str));
        if (empty($this->query)) {
            return $str;
        }
        $ar = explode($this->query, $str);
        $ar = array_splice($ar, 0, 20);
        if ($ar) {
            foreach ($ar as $k => &$it) {
                if ($k === 0 && strlen($it) > $len) {
                    $it = '...' . substr($it, -$len);
                } elseif ($k === count($ar) - 1 && strlen($it) > $len) {
                    $it = substr($it, 0, $len) . '...';
                } elseif (strlen($it) > $len * 2) {
                    $it = substr($it, 0, $len) . ' ... ' . substr($it, -$len);
                }
            }
            unset($it);
            $str = implode($this->query, $ar);
        }

        return $str;
    }

    public function run()
    {
        return '';
    }

    public function wrap($str)
    {
        return str_replace($this->query, sprintf($this->formatWrap, $this->query), $str);
    }
}
