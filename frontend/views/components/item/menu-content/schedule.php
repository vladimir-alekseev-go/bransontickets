<?php

use common\models\TrShows;
use yii\helpers\Url;
use yii\web\View;
use yii\web\YiiAsset;

/**
 * @var TrShows $model
 */

?>

<div id="calendar"></div>

<?php
$script = <<< JS

function openPopupSchedule(url)
{
	$.getJSON(url, function(data){
	    
	    var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev',
                center: 'title',
                right: 'next'
            },
            editable: false,
            contentHeight: 850,
            events: data.events
        });
        calendar.render();
	    
	    /*$("#calendar-loader-img").hide();
        $('#calendar').fullCalendar({
            header: {
                left: 'prev',
                center: 'title',
                right: 'next'
            },
            // defaultDate: data.defaultDate,
            editable: false,
            contentHeight: 750,
            eventLimit: false,
            events: data.events
        })
        // .fullCalendar('gotoDate', data.gotoDate)
        ;*/
	 	try{
	 	    setTimeout(function(){
	 		    // $('#js-data-container-schedule .scrollbar-inner').scrollbar();
		        // $('.fc-next-button').trigger('click');
                // $('.fc-prev-button').trigger('click');
                $('#schedule').removeClass('active').removeClass('opacity-0');
	 	    }, 100)
	 	    // setTimeout(function(){
	 		//     // $('#js-data-container-schedule .scrollbar-inner').scrollbar();
		    //     $('.fc-next-button').trigger('click');
            //     $('.fc-prev-button').trigger('click');
	 	    // }, 1000)
	 	} catch(e) {}
	 });
	 return false;
}
JS;
?>
<?php
$this->registerJsFile("/js/moment.js", ['depends' => YiiAsset::class]);
$this->registerJsFile("/js/fullcalendar.min.js", ['depends' => YiiAsset::class]);
$this->registerCssFile("/css/fullcalendar.min.css");
$this->registerJs($script, View::POS_END);

$controller = $this->context->module->controller->id;
if (Yii::$app->getRequest()->get('calendar-date')) {
    $url = Url::to([$controller . '/schedule', 'code' => $model->code, 'date' => Yii::$app->getRequest()->get('calendar-date')]);
    $this->registerJs('openPopupSchedule("' . $url . '")', View::POS_END);
} else {
    $url = Url::to([$controller . '/schedule', 'code' => $model->code]);
    $this->registerJs('openPopupSchedule("' . $url . '")', View::POS_END);
}
?>
