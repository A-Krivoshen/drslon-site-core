/**
 * Related posts picker for project CPT.
 * Saves immediately via admin-ajax on every add/remove (does not rely on Gutenberg REST).
 */
(function () {
	'use strict';

	var META_KEY = (typeof krvRP !== 'undefined' && krvRP.metaKey) ? krvRP.metaKey : 'related_posts';
	var i18n = (typeof krvRP !== 'undefined' && krvRP.i18n) ? krvRP.i18n : {};

	function t(key, fallback) {
		return i18n[key] || fallback;
	}

	function isGutenberg() {
		return !!(
			typeof wp !== 'undefined' &&
			wp.data &&
			typeof wp.data.dispatch === 'function' &&
			document.body &&
			document.body.classList.contains('block-editor-page')
		);
	}

	function syncToGutenberg(ids) {
		if (!isGutenberg()) {
			return;
		}
		try {
			var meta = {};
			meta[META_KEY] = ids.slice();
			wp.data.dispatch('core/editor').editPost({ meta: meta });
		} catch (e) {
			// Optional path — AJAX is authoritative.
		}
	}

	function escapeHtml(str) {
		return String(str)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;');
	}

	function showResults(el) {
		el.style.display = 'block';
		el.removeAttribute('hidden');
	}

	function hideResults(el) {
		el.style.display = 'none';
		el.setAttribute('hidden', 'hidden');
		el.innerHTML = '';
	}

	function resolvePostId(wrap) {
		var fromWrap = parseInt(wrap.getAttribute('data-post-id') || '0', 10);
		if (fromWrap > 0) {
			return fromWrap;
		}
		if (typeof krvRP !== 'undefined' && krvRP.postId) {
			var fromCfg = parseInt(krvRP.postId, 10);
			if (fromCfg > 0) {
				return fromCfg;
			}
		}
		if (isGutenberg()) {
			try {
				var id = parseInt(wp.data.select('core/editor').getCurrentPostId(), 10);
				if (id > 0) {
					return id;
				}
			} catch (e) {
				/* ignore */
			}
		}
		var input = document.getElementById('post_ID');
		if (input && input.value) {
			var fromInput = parseInt(input.value, 10);
			if (fromInput > 0) {
				return fromInput;
			}
		}
		return 0;
	}

	function initKRVRP(wrap) {
		if (wrap.getAttribute('data-krv-init') === '1') {
			return;
		}

		var searchInput = wrap.querySelector('.krv-rp-search');
		var resultsDiv = wrap.querySelector('.krv-rp-results');
		var selectedUl = wrap.querySelector('.krv-rp-selected');
		var hiddenInput = wrap.querySelector('input[name="krv_related_posts_ids"]');
		var statusEl = wrap.querySelector('.krv-rp-status');
		if (!searchInput || !resultsDiv || !selectedUl || !hiddenInput) {
			return;
		}

		wrap.setAttribute('data-krv-init', '1');

		var timer = null;
		var saveTimer = null;
		var saveSeq = 0;

		function setStatus(msg, isError) {
			if (!statusEl) {
				return;
			}
			statusEl.textContent = msg || '';
			statusEl.style.color = isError ? '#b32d2e' : '#646970';
		}

		function getSelectedIds() {
			var val = (hiddenInput.value || '').trim();
			if (!val) {
				return [];
			}
			return val
				.split(',')
				.map(function (s) {
					return parseInt(s, 10);
				})
				.filter(function (n) {
					return n > 0;
				});
		}

		/**
		 * Authoritative save: admin-ajax → update_post_meta.
		 * @param {number[]} ids
		 */
		function saveViaAjax(ids) {
			var postId = resolvePostId(wrap);
			if (!postId) {
				setStatus(t('needSave', 'Сначала сохраните проект'), true);
				return;
			}

			if (typeof krvRP === 'undefined' || !krvRP.ajaxUrl) {
				setStatus(t('configError', 'Ошибка конфигурации'), true);
				return;
			}

			var seq = ++saveSeq;
			setStatus(t('saving', 'Сохранение…'), false);

			var data = new FormData();
			data.append('action', 'krv_save_related_posts');
			data.append('post_id', String(postId));
			data.append('ids', ids.join(','));
			data.append('_ajax_nonce', krvRP.nonce || '');

			fetch(krvRP.ajaxUrl, {
				method: 'POST',
				body: data,
				credentials: 'same-origin',
			})
				.then(function (r) {
					return r.json().then(function (json) {
						return { ok: r.ok, json: json };
					});
				})
				.then(function (res) {
					if (seq !== saveSeq) {
						return; // outdated response
					}
					if (!res.json || !res.json.success) {
						var msg =
							(res.json && res.json.data && res.json.data.message) ||
							t('saveError', 'Не удалось сохранить');
						setStatus(msg, true);
						return;
					}
					var saved = (res.json.data && res.json.data.ids) || ids;
					hiddenInput.value = saved.join(',');
					syncToGutenberg(saved);
					setStatus(t('saved', 'Сохранено') + ' (' + saved.length + ')', false);
				})
				.catch(function () {
					if (seq !== saveSeq) {
						return;
					}
					setStatus(t('saveError', 'Не удалось сохранить'), true);
				});
		}

		function setIds(ids) {
			hiddenInput.value = ids.join(',');
			syncToGutenberg(ids);
			// Debounce rapid add/remove clicks.
			clearTimeout(saveTimer);
			saveTimer = setTimeout(function () {
				saveViaAjax(ids);
			}, 200);
		}

		function addPost(id, title) {
			var ids = getSelectedIds();
			if (ids.indexOf(id) !== -1) {
				return;
			}
			ids.push(id);
			setIds(ids);

			var li = document.createElement('li');
			li.setAttribute('data-id', String(id));
			li.innerHTML =
				'<span>' +
				escapeHtml(title) +
				'</span><a href="#" class="krv-rp-remove" title="Убрать" aria-label="Убрать">&times;</a>';
			selectedUl.appendChild(li);
		}

		function removePost(id) {
			var ids = getSelectedIds().filter(function (n) {
				return n !== id;
			});
			setIds(ids);
			var li = selectedUl.querySelector('li[data-id="' + id + '"]');
			if (li) {
				li.remove();
			}
		}

		selectedUl.addEventListener('click', function (e) {
			var remove = e.target.closest('.krv-rp-remove');
			if (!remove) {
				return;
			}
			e.preventDefault();
			var li = remove.closest('li');
			if (!li) {
				return;
			}
			removePost(parseInt(li.getAttribute('data-id'), 10));
		});

		searchInput.addEventListener('input', function () {
			clearTimeout(timer);
			var q = searchInput.value.trim();
			if (q.length < 2) {
				hideResults(resultsDiv);
				return;
			}

			timer = setTimeout(function () {
				if (typeof krvRP === 'undefined' || !krvRP.ajaxUrl) {
					resultsDiv.innerHTML =
						'<div class="krv-rp-loading">' +
						escapeHtml(t('configError', 'Ошибка конфигурации')) +
						'</div>';
					showResults(resultsDiv);
					return;
				}

				resultsDiv.innerHTML =
					'<div class="krv-rp-loading">' + escapeHtml(t('searching', 'Поиск...')) + '</div>';
				showResults(resultsDiv);

				var data = new FormData();
				data.append('action', 'krv_search_posts');
				data.append('q', q);
				data.append('_ajax_nonce', krvRP.nonce || '');

				fetch(krvRP.ajaxUrl, {
					method: 'POST',
					body: data,
					credentials: 'same-origin',
				})
					.then(function (r) {
						if (!r.ok) {
							throw new Error('HTTP ' + r.status);
						}
						return r.json();
					})
					.then(function (res) {
						if (!res || !res.success) {
							resultsDiv.innerHTML =
								'<div class="krv-rp-loading">' +
								escapeHtml(t('error', 'Ошибка')) +
								'</div>';
							showResults(resultsDiv);
							return;
						}
						if (!res.data || !res.data.length) {
							resultsDiv.innerHTML =
								'<div class="krv-rp-loading">' +
								escapeHtml(t('none', 'Ничего не найдено')) +
								'</div>';
							showResults(resultsDiv);
							return;
						}
						var html = '';
						var selected = getSelectedIds();
						res.data.forEach(function (p) {
							var id = parseInt(p.id, 10);
							var disabled = selected.indexOf(id) !== -1;
							html +=
								'<div class="krv-rp-result" data-id="' +
								id +
								'" data-title="' +
								escapeHtml(p.title) +
								'"' +
								(disabled ? ' style="opacity:0.5;pointer-events:none"' : '') +
								'>' +
								escapeHtml(p.title) +
								(disabled ? ' \u2713' : '') +
								'</div>';
						});
						resultsDiv.innerHTML = html;
						showResults(resultsDiv);
					})
					.catch(function () {
						resultsDiv.innerHTML =
							'<div class="krv-rp-loading">' + escapeHtml(t('error', 'Ошибка')) + '</div>';
						showResults(resultsDiv);
					});
			}, 300);
		});

		resultsDiv.addEventListener('click', function (e) {
			var item = e.target.closest('.krv-rp-result');
			if (!item) {
				return;
			}
			addPost(parseInt(item.getAttribute('data-id'), 10), item.getAttribute('data-title'));
			item.style.opacity = '0.5';
			item.style.pointerEvents = 'none';
			item.textContent = item.getAttribute('data-title') + ' \u2713';
		});
	}

	function scanAndInit() {
		document.querySelectorAll('.krv-rp-wrap').forEach(initKRVRP);
	}

	function boot() {
		scanAndInit();
		if (typeof MutationObserver !== 'undefined' && document.body) {
			var obs = new MutationObserver(function () {
				scanAndInit();
			});
			obs.observe(document.body, { childList: true, subtree: true });
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
