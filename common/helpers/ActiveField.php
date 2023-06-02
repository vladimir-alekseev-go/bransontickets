<?php

namespace common\helpers;

use yii\bootstrap\Html;

class ActiveField extends \yii\bootstrap\ActiveField
{
    public $labelTag = false;

    public function checkboxList($items, $options = [])
    {
        $labelTag = $this->labelTag;
        if (!isset($options['item'])) {
            $itemsDisplayCount = $options['itemsDisplayCount'] ?? count($items);
            $options['item'] = static function ($index, $label, $name, $checked, $value) use (
                $items,
                $options,
                $itemsDisplayCount,
                $labelTag
            ) {
                $id = str_replace(['[', ']'], '-', $name) . $index;
                if (!empty($labelTag)) {
                    $classTag = 'tag-' . strtolower(str_replace(' ', '-', $value));
                    $label = '<span class="tag ' . $classTag . '">' . $label . '</span>';
                }
                $html = "";
                if ($index === $itemsDisplayCount) {
                    $html .= '<div class="more-elem-filter">';
                }
                $html .= Html::checkbox(
                        $name,
                        $checked,
                        [
                            'id' => $id,
                            'value' => $value,
                            'label' => null,
                            'class' => '',
                        ]
                    ) . Html::label($label, $id, $options['labelOptions'] ?? []);

                if (count($items) > $itemsDisplayCount && $index === count($items) - 1) {
                    $html .= '</div><a class="show-more-filter">Show ';
                    $html .= '<span class="txt-filter more">more (+' . (count($items) - $itemsDisplayCount) . ')</span>';
                    $html .= '<span class="txt-filter less">less (-' . (count($items) - $itemsDisplayCount) . ')</span>';
                    $html .= '</a>';
                }
                return $html;
            };
        }
        parent::checkboxList($items, $options);
        return $this;
    }
}
