var data = [
	{
		title: '07:30 PM',
		url: 'https://google.com/',
		start: '2023-07-04'
	},
	{
		title: '07:30 PM',
		url: 'https://google.com/',
		start: '2023-07-11'
	},
	{
		title: '07:30 PM',
		url: 'https://google.com/',
		start: '2023-07-07'
	},
	{
		title: '07:30 PM',
		url: 'https://google.com/',
		start: '2023-07-14'
	},
	{
		title: '07:30 PM',
		url: 'https://google.com/',
		start: '2023-07-21'
	},
	{
		title: '07:30 PM',
		url: 'https://google.com/',
		start: '2023-07-28'
	},
	{
		title: '07:30 PM',
		url: 'https://google.com/',
		start: '2023-07-05'
	},
	{
		title: '07:30 PM',
		url: 'https://google.com/',
		start: '2023-07-12'
	},
	{
		title: '07:30 PM',
		url: 'https://google.com/',
		start: '2023-07-20'
	},
	{
		title: '07:30 PM',
		url: 'https://google.com/',
		start: '2023-07-24'
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
