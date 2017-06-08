# nps-flash-briefings
PHP code that calls the National Park Service (NPS) API, consumes data for a park, and outputs that data in JSON that is properly formatted for use by Alexa flash briefings. 
* park-alert-flash-briefing.php - Parks are selected by passing in a single URL variable (e.g., ?park=acad). URL variable must be a valid alphacode for a national park site. All active alerts for the designated park are returned.
* nps-news-flash-briefing.php - Five newest news items are pulled from NPS API and returned.

For more information about the NPS API, visit https://developer.nps.gov/api/index.htm.
