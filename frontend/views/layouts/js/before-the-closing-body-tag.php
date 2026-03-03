<?php
if (YII_ENV === "prod") {
    $this->registerJs('
    const script = document.createElement("script");
    script.async = true;
    script.src = "https://get.chattilive.ai/widgets/js/a1364392-b0d7-408f-af59-1261d4985e2f";
    script.setAttribute("data-settings", \'{"debug":false}\');
    document.body.appendChild(script);
    ');
}

