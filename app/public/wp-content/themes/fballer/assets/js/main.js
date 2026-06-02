const sliderAbout = () => {
  let slider = document.querySelector('.place-slider');

  if (slider) {
    var main = new Splide('.place-slider', {
      type: 'slide',
      // rewind    : true,
      pagination: false,
      arrows: true,
      perPage: 1,
      breakpoints: {
        992: {
          // autoWidth: false,
          perPage: 1,
          gap: 17,
        },

        670: {
          autoWidth: true,
          perPage: 1,
          gap: 17,
        },
      },
    });

    var thumbnails = new Splide('.thumb-place-slider', {
      gap: 21,
      pagination: false,
      isNavigation: true,
      arrows: false,
      perPage: 3,

    });

    main.sync(thumbnails);
    main.mount();
    thumbnails.mount();
  }


};

document.addEventListener('DOMContentLoaded', sliderAbout);

function applyPhoneMask() {
  document.querySelectorAll('#place-phone').forEach(phoneInput => {
      phoneInput.addEventListener('input', function () {
          let input = phoneInput.value.replace(/\D/g, ''); // Удаляем всё, кроме цифр

          // Добавляем префикс "+7" и ограничиваем ввод 11 цифрами
          if (input.length > 0) {
              input = '7' + input.substring(1, 11);
          }

          // Формируем маску поэтапно
          let formattedInput = '+7 (';
          if (input.length > 1) {
              formattedInput += input.substring(1, 4);
          }
          if (input.length >= 5) {
              formattedInput += ') ' + input.substring(4, 7);
          }
          if (input.length >= 8) {
              formattedInput += '-' + input.substring(7, 9);
          }
          if (input.length >= 10) {
              formattedInput += '-' + input.substring(9, 11);
          }

          phoneInput.value = formattedInput;
      });
  });
}

document.addEventListener('DOMContentLoaded', applyPhoneMask);



Fancybox.bind("[data-fancybox]", {
  // Your custom options
});

// Выбор города
const citySelectors = document.querySelectorAll('.town-select');
if ( citySelectors.length ) {
	const localizedDefaultCityId = typeof fballerMainData !== 'undefined' ? String(fballerMainData.defaultCityId || '') : '';
	const fallbackDefaultCityId = citySelectors[0]?.querySelector('[data-id]')?.getAttribute('data-id') || '';
	const defaultCityId = localizedDefaultCityId || fallbackDefaultCityId;

	function getInitialVisibleCitySelector() {
		if ( window.matchMedia('(max-width: 767px)').matches ) {
			return document.querySelector('.town-select.mobile-select--inline');
		}

		return document.querySelector('.town-select.desktop-select');
	}

	function closeAllCityPopups() {
		citySelectors.forEach(function(selector) {
			const info = selector.querySelector('.town-select__info');
			const list = selector.querySelector('.town-select__list');

			if ( info ) {
				info.classList.remove('active');
			}

			if ( list ) {
				list.classList.remove('active');
			}
		});
	}

	function syncSelectedCity(cityId) {
		citySelectors.forEach(function(selector) {
			const currentCityEl = selector.querySelector('span');
			const cityOptions = selector.querySelectorAll('[data-id]');
			const cityConfirm = selector.querySelector('.town-select__info');
			const confItemCurrent = cityConfirm ? cityConfirm.querySelector('span') : null;
			const matchedOption = Array.from(cityOptions).find(function(element) {
				return element.getAttribute('data-id') == cityId;
			});

			cityOptions.forEach(function(cityItem) {
				cityItem.classList.remove('active');
			});

			if ( matchedOption ) {
				matchedOption.classList.add('active');
				if ( currentCityEl ) {
					currentCityEl.textContent = matchedOption.textContent.trim();
				}
				if ( confItemCurrent ) {
					confItemCurrent.textContent = matchedOption.textContent.trim();
				}
			} else {
				const firstOption = cityOptions[0] || null;
				if ( firstOption ) {
					firstOption.classList.add('active');
					if ( currentCityEl ) {
						currentCityEl.textContent = firstOption.textContent.trim();
					}
					if ( confItemCurrent ) {
						confItemCurrent.textContent = firstOption.textContent.trim();
					}
				}
			}
		});
	}

	let savedCity = getCookie( 'selected_city' );
	const initialCity = savedCity || defaultCityId;
	syncSelectedCity(initialCity);

	if ( ! savedCity ) {
		const initialSelector = getInitialVisibleCitySelector();
		const firstConfirm = initialSelector ? initialSelector.querySelector('.town-select__info') : null;
		if ( firstConfirm ) {
			firstConfirm.classList.add('active');
		}
	}

	citySelectors.forEach(function(citySelector) {
		const currentCityEl = citySelector.querySelector('span');
		const cityOptions = citySelector.querySelectorAll('[data-id]');
		const cityConfirm = citySelector.querySelector('.town-select__info');
		const cityConfirmButtons = citySelector.querySelectorAll('.town-select__info-button');
		const cityList = citySelector.querySelector('.town-select__list');

		if ( currentCityEl && cityOptions.length ) {
			// Показываем выбор города
			citySelector.addEventListener('click', function(e) {
				e.stopPropagation();
				let doSkip = false;
				if ( cityConfirm ) {
					if ( cityConfirm.classList.contains("active") ) {
						doSkip = true;
					}
				}
				
				if ( ! doSkip ) {
					const shouldOpen = ! cityList.classList.contains('active');
					closeAllCityPopups();
					if ( shouldOpen ) {
						cityList.classList.add('active');
					}
				}
			});
			
			// Обработчик выбора города
			cityOptions.forEach(function(item) {
				item.addEventListener('click', function(e) {
					e.stopPropagation();
					const selectedCityId = item.getAttribute('data-id');
					const selectedCityName = item.textContent;
					savedCity = selectedCityId;
					syncSelectedCity(selectedCityId);
					setCookie( 'selected_city', selectedCityId, 30 );

					cityOptions.forEach(function(cityItem) {
						cityItem.classList.remove('active');
					});
					item.classList.add('active');
					closeAllCityPopups();
					
					location.reload(true);
				});
			});
			
			// Обработчик кнопок выбора
			cityConfirmButtons.forEach(function(item) {
				item.addEventListener('click', function(e) {
					e.preventDefault();
					e.stopPropagation();
					if ( item.classList.contains("town-select__info-button--black") ) {
						const confirmedCityId = getCookie( 'selected_city' ) || initialCity;
						savedCity = confirmedCityId;
						syncSelectedCity(confirmedCityId);
						setCookie( 'selected_city', savedCity, 30 );
					} else {
						closeAllCityPopups();
						setTimeout(function() {
							cityList.classList.add('active');
						}, 100);
					}
					
					closeAllCityPopups();
				});
			});
		}
	});

	document.addEventListener('click', function(e) {
		if ( ! e.target.closest('.town-select') ) {
			closeAllCityPopups();
		}
	});

	// Вспомогательные функции для работы с cookie
	function setCookie( name, value, days ) {
		const date = new Date();
		date.setTime( date.getTime() + ( days * 24 * 60 * 60 * 1000 ) );
		const expires = "expires=" + date.toUTCString();
		document.cookie = name + "=" + encodeURIComponent( value ) + ";" + expires + ";path=/";
	}

	function getCookie( name ) {
		const nameEQ = name + "=";
		const cookies = document.cookie.split( ';' );
		for ( let i = 0; i < cookies.length; i++ ) {
			let c = cookies[i].trim();
			if ( c.indexOf( nameEQ ) === 0 ) {
				return decodeURIComponent( c.substring( nameEQ.length ) );
			}
		}
		
		return null;
		// return 145;
	}
}

function gamesItemsUrlTrigger() {
	var elements = document.querySelectorAll('.player-item');
	if ( elements.length ) {
		elements.forEach(function(element) {
			if ( element.dataset.hooked === "true" ) {
				return;
			}
			
			var url = element.getAttribute('data-url');
			if ( url ) {
				element.addEventListener('click', function(e) {
					if ( event.target.closest('a') ) {
						return;
					}
					
					e.preventDefault();
					window.location.href = url;
				});
				
				element.dataset.hooked = "true";
			}
		});
	}
}
window.gamesItemsUrlTrigger = gamesItemsUrlTrigger;

document.addEventListener( 'DOMContentLoaded', function() {
	gamesItemsUrlTrigger();
});

// Menu Mobile
const menuMobile = document.querySelector('.menu-mobile');
const menuMobileHamburgers = document.querySelectorAll('.menu-hamburger');
if ( menuMobile && menuMobileHamburgers.length ) {
	const body = document.querySelector('body');
	
	// Show/Hide
	menuMobileHamburgers.forEach(function(menuMobileHamburger) {
		menuMobileHamburger.addEventListener('click', function(e) {
			menuMobile.classList.toggle('active');
			
			if ( menuMobile.classList.contains("active") ) {
				body.classList.add('overflow-hidden');
			} else {
				body.classList.remove('overflow-hidden');
			}
		});
	});
	
	// Close
	const menuMobileClose = menuMobile.querySelector('.menu-mobile__close');
	if ( menuMobileClose ) {
		menuMobileClose.addEventListener('click', function(e) {
			menuMobile.classList.remove('active');
			body.classList.remove('overflow-hidden');
		});
	}
}

// Add dropdown (header + mobile menu)
document.addEventListener('DOMContentLoaded', function() {
	const dropdowns = document.querySelectorAll('.add-dropdown');
	if (!dropdowns.length) return;

	const closeAll = () => {
		dropdowns.forEach((dropdown) => {
			dropdown.classList.remove('is-open');
			const toggle = dropdown.querySelector('.add-dropdown__toggle');
			if (toggle) toggle.setAttribute('aria-expanded', 'false');
		});
	};

	document.addEventListener('click', function(e) {
		const toggle = e.target.closest('.add-dropdown__toggle');
		const dropdown = e.target.closest('.add-dropdown');

		if (!dropdown) {
			closeAll();
			return;
		}

		if (toggle) {
			e.preventDefault();
			const isOpen = dropdown.classList.contains('is-open');
			closeAll();
			if (!isOpen) {
				dropdown.classList.add('is-open');
				toggle.setAttribute('aria-expanded', 'true');
			}
		}
	});

	document.addEventListener('keydown', function(e) {
		if (e.key === 'Escape') closeAll();
	});

	dropdowns.forEach((dropdown) => {
		dropdown.querySelectorAll('[data-modal]').forEach((link) => {
			link.addEventListener('click', function() {
				closeAll();
			});
		});
	});
});

// Sticky header
document.addEventListener('DOMContentLoaded', function() {
	const body = document.body;
	const header = document.querySelector('.site-header');
	if (!body || !header) return;

	const desktopLogo = header.querySelector('.logo-desktop');
	const mobileLogo = header.querySelector('.logo-mobile');

	const updateHeaderHeight = () => {
		document.documentElement.style.setProperty('--site-header-height', header.offsetHeight + 'px');
	};

	const syncLogos = (isSticky) => {
		[desktopLogo, mobileLogo].forEach((logo) => {
			if (!logo) return;
			const nextSrc = isSticky ? logo.dataset.stickySrc : logo.dataset.defaultSrc;
			if (nextSrc && logo.getAttribute('src') !== nextSrc) {
				logo.setAttribute('src', nextSrc);
			}
		});
	};

	const syncStickyState = () => {
		const isSticky = window.scrollY > 0;
		body.classList.toggle('has-scrolled-header', isSticky);
		syncLogos(isSticky);
		updateHeaderHeight();
	};

	let ticking = false;
	const requestSync = () => {
		if (ticking) return;
		ticking = true;
		window.requestAnimationFrame(() => {
			syncStickyState();
			ticking = false;
		});
	};

	syncStickyState();
	window.addEventListener('scroll', requestSync, { passive: true });
	window.addEventListener('resize', requestSync);
	window.addEventListener('load', requestSync);
});
