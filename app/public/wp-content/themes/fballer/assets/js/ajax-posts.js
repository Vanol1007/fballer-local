jQuery(function($) {
	function getCheckedValues(name, context) {
		return $(context).find('input[name="' + name + '[]"]:checked').map(function() {
			var value = $(this).val();
			var numericValue = parseInt(value, 10);

			return Number.isNaN(numericValue) ? value : numericValue;
		}).get();
	}

	function debounce(fn, wait) {
		let timeoutId = null;

		return function() {
			const context = this;
			const args = arguments;

			window.clearTimeout(timeoutId);
			timeoutId = window.setTimeout(function() {
				fn.apply(context, args);
			}, wait);
		};
	}

	function isMobileFilterMode() {
		return window.matchMedia('(max-width: 670px)').matches;
	}

	const ajaxContainer = $('.ajax-posts');
	if ( ajaxContainer.length ) {
		ajaxContainer.each(function() {
			var ajaxData = ajaxPostsData;
			let canLoadMore = true;
			
			const ajaxDataVariable = $(this).attr('ajax-data') || null;
			if ( ajaxDataVariable ) {
				ajaxData = ( typeof window[ajaxDataVariable] !== "undefined" ? window[ajaxDataVariable] : null );
			}

			if ( ajaxData ) {
				let currentPage = parseInt(ajaxData.current_page);
				let maxPages = parseInt(ajaxData.max_pages);
				const ajaxUrl = ajaxData.ajaxurl;
				const ajaxButton = $(this).find('.ajax-posts__btn');
				const ajaxItems = $(this).find('.ajax-posts__items');
				const container = $(this);

				function applyFilters() {
					var f1 = container.find('#date').val();
					var f2 = container.find('#district').val();
					var f3 = container.find('#field_size').val();
					var f4 = container.find('#coating').val();
					var f5 = container.find('#game_format').val();
					var f6 = container.find('#price').val();
					var f7 = container.find('#team_level').val();
					var f8 = container.find('#team_position').val();
					var f9 = container.find('#player_goal').val();
					var f10 = getCheckedValues('game_format', container);
					var f11 = getCheckedValues('city_direction', container);
					var f12 = getCheckedValues('admin_area', container);
					var f13 = getCheckedValues('district', container);
					var f14 = getCheckedValues('metro', container);
					var f15 = getCheckedValues('game_price_bucket', container);
					var f16 = getCheckedValues('coating', container);
					var f17 = getCheckedValues('team_level_multi', container);
					var f18 = getCheckedValues('team_position_multi', container);
					var f19 = getCheckedValues('player_goal_multi', container);

					if ( ! canLoadMore ) {
						return;
					}

					canLoadMore = false;
					currentPage = 1;

					ajaxData.current_page = currentPage;
					ajaxData.action = 'load_more_posts_6728';
					ajaxData.f1 = f1;
					ajaxData.f2 = f2;
					ajaxData.f3 = f3;
					ajaxData.f4 = f4;
					ajaxData.f5 = f5;
					ajaxData.f6 = f6;
					ajaxData.f7 = f7;
					ajaxData.f8 = f8;
					ajaxData.f9 = f9;
					ajaxData.f10 = f10;
					ajaxData.f11 = f11;
					ajaxData.f12 = f12;
					ajaxData.f13 = f13;
					ajaxData.f14 = f14;
					ajaxData.f15 = f15;
					ajaxData.f16 = f16;
					ajaxData.f17 = f17;
					ajaxData.f18 = f18;
					ajaxData.f19 = f19;

					$.post(ajaxUrl, ajaxData).done(function(response) {
						if ( response.success ) {
							if ( ajaxItems.children().first().find('.add-wrapper').length ) {
								ajaxItems.children().slice(1).remove();
							} else {
								ajaxItems.empty();
							}

							ajaxItems.append(response.data.html);
							maxPages = parseInt(response.data.max_pages || 0);
							ajaxData.max_pages = maxPages;

							if ( maxPages > 1 ) {
								ajaxButton.show();
							} else {
								ajaxButton.hide();
							}

							if ( typeof(gamesItemsUrlTrigger) === "function" ) {
								gamesItemsUrlTrigger();
							}
						}
					}).always(function() {
						canLoadMore = true;
					});
				}

				const debouncedApplyFilters = debounce(applyFilters, 120);

				if ( ajaxButton.length && ajaxItems.length ) {
					ajaxButton.click(function(e) {
						e.preventDefault();

						if ( canLoadMore ) {
							canLoadMore = false;
							currentPage++;
							
							ajaxData.current_page = currentPage;
							ajaxData.action = 'load_more_posts_4832';

							if ( currentPage <= maxPages ) {
								$.post(ajaxUrl, ajaxData).done(function(data) {
									if ( data ) {
										gamesItemsUrlTrigger();
										
										ajaxItems.append(data);
										canLoadMore = true;
										
										if ( currentPage >= maxPages ) {
											ajaxButton.hide();
										}
										
										if ( typeof(gamesItemsUrlTrigger) === "function" ) gamesItemsUrlTrigger();
									} else {
										console.log('No more posts to load.');
									}
								});
							}
						}
					});
				}
				
				const filterButton = $(this).find('.filter-btn') || null;
				if ( filterButton.length ) {
					filterButton.click(function(e) {
						e.preventDefault();
						applyFilters();
					});
				}

				container.on('change', '#date, #district, #field_size, #coating, #game_format, #price, #team_level, #team_position, #player_goal', function() {
					if ( isMobileFilterMode() ) {
						return;
					}
					applyFilters();
				});

				container.on('change', 'input[name="game_format[]"], input[name="city_direction[]"], input[name="admin_area[]"], input[name="district[]"], input[name="metro[]"], input[name="game_price_bucket[]"], input[name="coating[]"], input[name="team_level_multi[]"], input[name="team_position_multi[]"], input[name="player_goal_multi[]"]', function() {
					if ( isMobileFilterMode() ) {
						return;
					}
					debouncedApplyFilters();
				});
			}
		});
	}
});
