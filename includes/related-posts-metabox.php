<?php
/**
 * Custom meta box for linking posts to project CPT.
 * Works with both Gutenberg and classic editor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register meta box on project CPT.
 */
add_action( 'add_meta_boxes_project', function () {
	add_meta_box(
		'krv_related_posts',
		'Связанные статьи',
		'krv_related_posts_metabox_render',
		'project',
		'side',
		'default'
	);
} );

/**
 * Render the meta box HTML.
 */
function krv_related_posts_metabox_render( $post ) {
	$ids = get_post_meta( $post->ID, 'related_posts', true );
	$ids = array_filter( array_map( 'intval', (array) $ids ) );

	wp_nonce_field( 'krv_related_posts_' . $post->ID, 'krv_related_posts_nonce' );
	?>
	<style>
		.krv-rp-wrap { margin-top: 8px; }
		.krv-rp-search { width: 100%; margin-bottom: 8px; }
		.krv-rp-results { max-height: 160px; overflow-y: auto; border: 1px solid #ddd; border-radius: 4px; display: none; }
		.krv-rp-results .krv-rp-result {
			padding: 6px 8px; cursor: pointer; border-bottom: 1px solid #eee;
			font-size: 13px; line-height: 1.3;
		}
		.krv-rp-results .krv-rp-result:hover { background: #f0f0f0; }
		.krv-rp-results .krv-rp-result:last-child { border-bottom: none; }
		.krv-rp-selected { list-style: none; margin: 0; padding: 0; }
		.krv-rp-selected li {
			display: flex; align-items: center; justify-content: space-between;
			padding: 5px 8px; margin-bottom: 4px; background: #f0f6fc;
			border: 1px solid #c3d4e9; border-radius: 4px; font-size: 13px;
		}
		.krv-rp-selected li .krv-rp-remove {
			cursor: pointer; color: #b32d2e; font-weight: bold; margin-left: 8px;
			text-decoration: none; line-height: 1;
		}
		.krv-rp-selected li .krv-rp-remove:hover { color: #a00; }
		.krv-rp-loading { color: #666; font-size: 12px; font-style: italic; }
	</style>
	<div class="krv-rp-wrap" id="krv-rp-wrap-<?php echo esc_attr( $post->ID ); ?>">
		<input type="text" class="krv-rp-search" placeholder="Поиск статей..." />
		<div class="krv-rp-results"></div>
		<ul class="krv-rp-selected">
			<?php foreach ( $ids as $id ) :
				$title = get_the_title( $id );
				if ( ! $title ) continue;
			?>
				<li data-id="<?php echo esc_attr( $id ); ?>">
					<span><?php echo esc_html( $title ); ?></span>
					<a class="krv-rp-remove" title="Убрать">&times;</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<input type="hidden" name="krv_related_posts_ids" value="<?php echo esc_attr( implode( ',', $ids ) ); ?>" />
	</div>
	<script>
	(function(){
		function initKRVRP(wrap) {
			var searchInput = wrap.querySelector('.krv-rp-search');
			var resultsDiv = wrap.querySelector('.krv-rp-results');
			var selectedUl = wrap.querySelector('.krv-rp-selected');
			var hiddenInput = wrap.querySelector('input[name="krv_related_posts_ids"]');
			var timer = null;

			function getSelectedIds() {
				var val = hiddenInput.value.trim();
				return val ? val.split(',').map(function(s){return parseInt(s,10)}).filter(function(n){return n>0}) : [];
			}

			function addPost(id, title) {
				var ids = getSelectedIds();
				if (ids.indexOf(id) !== -1) return;
				ids.push(id);
				hiddenInput.value = ids.join(',');
				var li = document.createElement('li');
				li.setAttribute('data-id', id);
				li.innerHTML = '<span>' + title.replace(/</g,'&lt;') + '</span><a class="krv-rp-remove" title="Убрать">&times;</a>';
				selectedUl.appendChild(li);
			}

			function removePost(id) {
				var ids = getSelectedIds().filter(function(n){return n!==id});
				hiddenInput.value = ids.join(',');
				var li = selectedUl.querySelector('li[data-id="'+id+'"]');
				if (li) li.remove();
			}

			selectedUl.addEventListener('click', function(e){
				if (e.target.classList.contains('krv-rp-remove')) {
					removePost(parseInt(e.target.closest('li').getAttribute('data-id'), 10));
				}
			});

			searchInput.addEventListener('input', function(){
			 clearTimeout(timer);
			 var q = searchInput.value.trim();
			 if (q.length < 2) { resultsDiv.style.display='none'; resultsDiv.innerHTML=''; return; }
			 timer = setTimeout(function(){
				 resultsDiv.innerHTML = '<div class="krv-rp-loading">Поиск...</div>';
				 resultsDiv.style.display='block';
				 var ajaxUrl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
				 var data = new FormData();
				 data.append('action', 'krv_search_posts');
				 data.append('q', q);
				 data.append('_ajax_nonce', '<?php echo esc_js( wp_create_nonce( 'krv_search_posts' ) ); ?>');
				 fetch(ajaxUrl, {method:'POST', body:data})
				   .then(function(r){return r.json()})
				   .then(function(res){
					   if (!res.success || !res.data.length) {
						   resultsDiv.innerHTML = '<div class="krv-rp-loading">Ничего не найдено</div>';
						   return;
					   }
					   var html = '';
					   var selected = getSelectedIds();
					   res.data.forEach(function(p){
						   var disabled = selected.indexOf(p.id) !== -1;
						   html += '<div class="krv-rp-result" data-id="'+p.id+'" data-title="'+p.title.replace(/"/g,'&quot;')+'" style="'+(disabled?'opacity:0.5':'')+'">'+p.title+(disabled?' ✓':'')+'</div>';
					   });
					   resultsDiv.innerHTML = html;
				   })
				   .catch(function(){ resultsDiv.innerHTML='<div class="krv-rp-loading">Ошибка</div>'; });
			 }, 300);
			});

			resultsDiv.addEventListener('click', function(e){
				var item = e.target.closest('.krv-rp-result');
				if (!item) return;
				addPost(parseInt(item.getAttribute('data-id'),10), item.getAttribute('data-title'));
				item.style.opacity = '0.5';
				item.textContent = item.getAttribute('data-title') + ' ✓';
			});
		}

		var script = document.currentScript;
		var postId = <?php echo json_encode( $post->ID ); ?>;
		function tryInitKRVRP() {
			var w = document.getElementById('krv-rp-wrap-' + postId);
			if (w && !w._krvInit) { w._krvInit = true; initKRVRP(w); return true; }
			return false;
		}
		if (!tryInitKRVRP()) {
			var obs = new MutationObserver(function() { tryInitKRVRP(); });
			obs.observe(document.body, {childList: true, subtree: true});
			setTimeout(function() { obs.disconnect(); }, 10000);
		}
	})();
	</script>
	<?php
}

/**
 * Save meta box data.
 */
add_action( 'save_post_project', function ( $post_id, $post = null, $update = false ) {
	if ( ! $update || ! $post ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! isset( $_POST['krv_related_posts_nonce'] ) ||
	     ! wp_verify_nonce( $_POST['krv_related_posts_nonce'], 'krv_related_posts_' . $post_id ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$raw = isset( $_POST['krv_related_posts_ids'] ) ? sanitize_text_field( $_POST['krv_related_posts_ids'] ) : '';
	$ids = array_filter( array_map( 'intval', explode( ',', $raw ) ) );

	update_post_meta( $post_id, 'related_posts', $ids );
}, 20, 3 );

/**
 * AJAX handler: search published posts by title.
 */
add_action( 'wp_ajax_krv_search_posts', function () {
	check_ajax_referer( 'krv_search_posts' );

	$q = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
	if ( mb_strlen( $q ) < 2 ) {
		wp_send_json_success( [] );
	}

	$results = get_posts( [
		's'              => $q,
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 15,
		'orderby'        => 'relevance',
		'suppress_filters' => true,
		'no_found_rows'  => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	] );

	$data = array_map( function ( $p ) {
		return [
			'id'    => $p->ID,
			'title' => $p->post_title,
		];
	}, $results );

	wp_send_json_success( $data );
} );
