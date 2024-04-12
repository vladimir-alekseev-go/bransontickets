<?php

namespace frontend\controllers;

interface BaseController
{
    public const LAYOUT_MAIN        = 'main';
    public const LAYOUT_STATIC_PAGE = 'static-page';
    public const LAYOUT_GENERAL     = 'general';
    public const LAYOUT_PROFILE     = 'profile';
    public const LAYOUT_FULL        = 'full';
    public const LAYOUT_POPUP       = 'popup';
}
