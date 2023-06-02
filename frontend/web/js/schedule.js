var data = [
	{
		title: '07:30 PM',
		start: '2023-05-04'
	},
	{
		title: '07:30 PM',
		url: 'https://google.com/',
		start: '2023-05-11'
	},
	{
		title: '07:30 PM',
		start: '2023-05-07'
	},
	{
		title: '07:30 PM',
		url: 'https://google.com/',
		start: '2023-05-14'
	},
	{
		title: '07:30 PM',
		start: '2023-05-21'
	},
	{
		title: '07:30 PM',
		url: 'https://google.com/',
		start: '2023-05-28'
	},
	{
		title: '07:30 PM',
		start: '2023-05-05'
	},
	{
		title: '07:30 PM',
		url: 'https://google.com/',
		start: '2023-05-12'
	},
	{
		title: '07:30 PM',
		start: '2023-05-19'
	},
	{
		title: '07:30 PM',
		url: 'https://google.com/',
		start: '2023-05-22'
	},
];

$(document).ready(function() {
	$("#schedule-tab").on("click", function () {
		setTimeout(function () {
			var calendarEl = document.getElementById('calendar');

			var calendar = new FullCalendar.Calendar(calendarEl, {
				initialView: 'dayGridMonth',
				headerToolbar: {
					left: 'prev',
					center: 'title',
					right: 'next'
				},
				editable: false,
				contentHeight: 705,
				events: data
			});

			calendar.render();
		}, 1);    
	});
});
