(function ($) {
	$.fn.gCalReader = function (options) {
		// Cache the reference to $(this) for efficiency
		var $div = $(this);

		// Use destructuring to extract options with defaults
		var {
			calendarId = "loqtku98c652ie9eggq0c0mse4@group.calendar.google.com",
			apiKey = "AIzaSyApngt3tiz8wOGZU_Qc9xemzzOzWBKaQKQ",
			dateFormat = "LongDate",
			errorMsg = "No events in calendar",
			maxEvents = 30,
			futureEventsOnly = true,
			sortDescending = true,
		} = options;

		// Construct the URL using template literals for readability
		var feedUrl = `https://www.googleapis.com/calendar/v3/calendars/${encodeURIComponent(
			calendarId.trim()
		)}/events?key=${apiKey}&orderBy=startTime&singleEvents=true`;
		if (futureEventsOnly) {
			feedUrl += `&timeMin=${new Date().toISOString()}`;
		}

		// Utilize the fetch API for cleaner AJAX requests
		fetch(feedUrl)
			.then((response) => response.json())
			.then((data) => {
				// Sort events based on options
				if (sortDescending) {
					data.items.reverse();
				}
				data.items = data.items.slice(0, maxEvents);

				// Use map to transform data items into event HTML
				var eventsHTML = data.items
					.map((item) => {
						var eventdate =
							item.start.dateTime || item.start.date || "";
						var summary = item.summary || "";
						var description = item.description || "";
						var location = item.location || "";
						var formattedDate = formatDateTime(
							eventdate,
							dateFormat.trim()
						);
						var link = item.htmlLink || "";
						var bandName = summary;

						if (link) {
							bandName = `<a class="eventtitle" href="${link}" target="_blank">${bandName}</a>`;
						}

						return `
						<tr>
							<td class='p'>
							<div class="eventdate">${formattedDate}</div>
							<div class="eventtitle">${summary}</div>
							${location ? `<div class="location">${location}</div>` : ""}
							${description ? `<div class="description">${description}</div>` : ""}
							</td>
						</tr>`;
					})
					.join("");

				// Append all events HTML at once for better performance
				$div.append(eventsHTML);
			})
			.catch((error) => {
				// Handle errors
				$div.append(`<p>${errorMsg} | ${error}</p>`);
			});

		// Simplified formatDate function using Date.toLocaleDateString
		function formatDateTime(strDate) {
			var date = new Date(strDate);
			var formattedDate = date.toLocaleDateString("en-UK", {
				weekday: "long",
				day: "numeric",
				month: "long",
				year: "numeric",
			});
			formattedDate = formattedDate.replace(/,/g, "");

			var hours = date.getHours();
			var minutes = date.getMinutes();
			var ampm = hours >= 12 ? "PM" : "AM";
			hours = hours % 12;
			hours = hours ? hours : 12; // Handle midnight
			minutes = minutes < 10 ? "0" + minutes : minutes;
			var formattedTime = hours + ":" + minutes + " " + ampm;

			return formattedDate + " - " + formattedTime;
		}
	};
})(jQuery);
