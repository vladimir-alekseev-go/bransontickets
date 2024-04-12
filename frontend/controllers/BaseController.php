<?php

namespace frontend\controllers;

interface BaseController
{
    public const LAYOUT_STATIC_PAGE = 'static-page';
    public const LAYOUT_ITEM_DETAIL = 'item-detail';
    public const LAYOUT_MAIN_LIST = 'main-list';
    public const LAYOUT_EMPTY = 'empty';
    public const LAYOUT_PRINT = 'print';
}
