(function () {
	'use strict';

	var config = window.drslonViewBeacon;

	if (!config || !config.url || !config.action || !config.post_id || !config.token || navigator.webdriver) {
		return;
	}

	var sent = false;
	var timer = null;

	function sendView() {
		if (sent) {
			return;
		}

		sent = true;

		if (timer) {
			window.clearTimeout(timer);
		}

		var payload = new FormData();
		payload.append('action', config.action);
		payload.append('post_id', String(config.post_id));
		payload.append('token', config.token);

		if (navigator.sendBeacon && navigator.sendBeacon(config.url, payload)) {
			return;
		}

		if (window.fetch) {
			window.fetch(config.url, {
				method: 'POST',
				body: payload,
				credentials: 'same-origin',
				keepalive: true,
			}).catch(function () {});
		}
	}

	function scheduleView() {
		timer = window.setTimeout(sendView, 1200);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', scheduleView, { once: true });
	} else {
		scheduleView();
	}

	document.addEventListener('visibilitychange', function () {
		if (document.visibilityState === 'hidden') {
			sendView();
		}
	});
})();
