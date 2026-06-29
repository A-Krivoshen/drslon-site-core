(function () {
	'use strict';

	var widget = document.querySelector('.krv-price-widget');

	if (!widget) {
		return;
	}

	var nav = widget.querySelector('.krv-anchor-nav');
	var navLinks = nav ? Array.prototype.slice.call(nav.querySelectorAll('a[href^="#"]')) : [];
	var mobileCta = widget.querySelector('.krv-mobile-cta');
	var hero = widget.querySelector('.krv-hero');

	function setNavActive(id) {
		if (!navLinks.length) {
			return;
		}

		navLinks.forEach(function (link) {
			var target = link.getAttribute('href').slice(1);
			link.classList.toggle('is-active', target === id);
		});
	}

	if (navLinks.length) {
		var sections = navLinks
			.map(function (link) {
				var id = link.getAttribute('href').slice(1);
				var section = widget.querySelector('#' + id);
				return section ? { id: id, el: section } : null;
			})
			.filter(Boolean);

		if (sections.length && 'IntersectionObserver' in window) {
			var observer = new IntersectionObserver(
				function (entries) {
					var visible = entries
						.filter(function (entry) {
							return entry.isIntersecting;
						})
						.sort(function (a, b) {
							return b.intersectionRatio - a.intersectionRatio;
						});

					if (visible[0]) {
						setNavActive(visible[0].target.id);
					}
				},
				{
					root: null,
					rootMargin: '-35% 0px -55% 0px',
					threshold: [0, 0.15, 0.35, 0.55],
				}
			);

			sections.forEach(function (section) {
				observer.observe(section.el);
			});
		}

		navLinks.forEach(function (link) {
			link.addEventListener('click', function () {
				var id = link.getAttribute('href').slice(1);
				setNavActive(id);
			});
		});
	}

	if (mobileCta && hero && 'IntersectionObserver' in window) {
		var heroObserver = new IntersectionObserver(
			function (entries) {
				var entry = entries[0];
				mobileCta.classList.toggle('is-visible', entry && !entry.isIntersecting);
			},
			{
				root: null,
				threshold: 0,
			}
		);

		heroObserver.observe(hero);
	}

	var tablist = widget.querySelector('.krv-prices-tabs');

	if (tablist) {
		var tabs = Array.prototype.slice.call(tablist.querySelectorAll('[role="tab"]'));
		var panels = Array.prototype.slice.call(widget.querySelectorAll('.krv-prices-panel'));

		function activateTab(tab) {
			var panelId = tab.getAttribute('aria-controls');

			tabs.forEach(function (item) {
				var selected = item === tab;
				item.setAttribute('aria-selected', selected ? 'true' : 'false');
				item.setAttribute('tabindex', selected ? '0' : '-1');
				item.classList.toggle('is-active', selected);
			});

			panels.forEach(function (panel) {
				var active = panel.id === panelId;
				panel.classList.toggle('is-active', active);
				panel.hidden = !active;
			});
		}

		tabs.forEach(function (tab, index) {
			tab.addEventListener('click', function () {
				activateTab(tab);
			});

			tab.addEventListener('keydown', function (event) {
				var nextIndex = index;

				if (event.key === 'ArrowRight') {
					nextIndex = (index + 1) % tabs.length;
				} else if (event.key === 'ArrowLeft') {
					nextIndex = (index - 1 + tabs.length) % tabs.length;
				} else {
					return;
				}

				event.preventDefault();
				tabs[nextIndex].focus();
				activateTab(tabs[nextIndex]);
			});
		});
	}
})();