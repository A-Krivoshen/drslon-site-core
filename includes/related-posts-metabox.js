document.addEventListener('DOMContentLoaded', function () {
	function initKRVRP(wrap) {
		if (wrap._krvInit) return;
		wrap._krvInit = true;

		var searchInput = wrap.querySelector('.krv-rp-search');
		var resultsDiv = wrap.querySelector('.krv-rp-results');
		var selectedUl = wrap.querySelector('.krv-rp-selected');
		var hiddenInput = wrap.querySelector('input[name="krv_related_posts_ids"]');
		var timer = null;

		function getSelectedIds() {
			var val = hiddenInput.value.trim();
			return val ? val.split(',').map(function (s) { return parseInt(s, 10); }).filter(function (n) { return n > 0; }) : [];
		}

		function addPost(id, title) {
			var ids = getSelectedIds();
			if (ids.indexOf(id) !== -1) return;
			ids.push(id);
			hiddenInput.value = ids.join(',');
			var li = document.createElement('li');
			li.setAttribute('data-id', id);
			li.innerHTML = '<span>' + title.replace(/</g, '&lt;') + '</span><a class="krv-rp-remove" title="Убрать">&times;</a>';
			selectedUl.appendChild(li);
		}

		function removePost(id) {
			var ids = getSelectedIds().filter(function (n) { return n !== id; });
			hiddenInput.value = ids.join(',');
			var li = selectedUl.querySelector('li[data-id="' + id + '"]');
			if (li) li.remove();
		}

		selectedUl.addEventListener('click', function (e) {
			if (e.target.classList.contains('krv-rp-remove')) {
				removePost(parseInt(e.target.closest('li').getAttribute('data-id'), 10));
			}
		});

		searchInput.addEventListener('input', function () {
			clearTimeout(timer);
			var q = searchInput.value.trim();
			if (q.length < 2) {
				resultsDiv.style.display = 'none';
				resultsDiv.innerHTML = '';
				return;
			}
			timer = setTimeout(function () {
				resultsDiv.innerHTML = '<div class="krv-rp-loading">Поиск...</div>';
				resultsDiv.style.display = 'block';
				var data = new FormData();
				data.append('action', 'krv_search_posts');
				data.append('q', q);
				data.append('_ajax_nonce', krvRP.nonce);
				fetch(krvRP.ajaxUrl, { method: 'POST', body: data })
					.then(function (r) { return r.json(); })
					.then(function (res) {
						if (!res.success || !res.data.length) {
							resultsDiv.innerHTML = '<div class="krv-rp-loading">Ничего не найдено</div>';
							return;
						}
						var html = '';
						var selected = getSelectedIds();
						res.data.forEach(function (p) {
							var disabled = selected.indexOf(p.id) !== -1;
							html += '<div class="krv-rp-result" data-id="' + p.id + '" data-title="' + p.title.replace(/"/g, '&quot;') + '" style="' + (disabled ? 'opacity:0.5' : '') + '">' + p.title + (disabled ? ' \u2713' : '') + '</div>';
						});
						resultsDiv.innerHTML = html;
					})
					.catch(function () {
						resultsDiv.innerHTML = '<div class="krv-rp-loading">Ошибка</div>';
					});
			}, 300);
		});

		resultsDiv.addEventListener('click', function (e) {
			var item = e.target.closest('.krv-rp-result');
			if (!item) return;
			addPost(parseInt(item.getAttribute('data-id'), 10), item.getAttribute('data-title'));
			item.style.opacity = '0.5';
			item.textContent = item.getAttribute('data-title') + ' \u2713';
		});
	}

	// Initialize all wraps on page.
	function scanAndInit() {
		document.querySelectorAll('.krv-rp-wrap').forEach(initKRVRP);
	}

	scanAndInit();

	// Re-scan when Gutenberg loads/reloads meta boxes.
	if (typeof MutationObserver !== 'undefined') {
		var obs = new MutationObserver(scanAndInit);
		obs.observe(document.body, { childList: true, subtree: true });
	}
});
