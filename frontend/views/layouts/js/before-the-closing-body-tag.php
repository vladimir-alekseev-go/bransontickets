<?php
if (YII_ENV === "prod") {
    $this->registerJs('
    const script = document.createElement("script");
    script.async = true;
    script.src = "https://get.chattilive.ai/widgets/js/a1364e54-a1bf-4331-b2af-bbdce0d7254d";
    script.setAttribute("data-settings", \'{"debug":false}\');
    document.body.appendChild(script);
    ');
}

