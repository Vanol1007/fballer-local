	<footer class="footer">
		<div class="container">
			<div class="footer-row">
				<div class="footer-row__col footer-row__col--3">
					<a href="<?php echo get_site_url(); ?>" class="footer-logo">
						<img src="<?php echo get_template_directory_uri() . '/assets/img/fbftlogo.svg'; ?>" alt="logo" />
					</a>
					
					<div class="footer-social">
						<a href="https://t.me/fba11er" class="social-link" target="_blank" rel="nofollow noopener">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 25 25"><path fill="#E52F24" d="M12.5 0C5.6 0 0 5.6 0 12.5S5.6 25 12.5 25 25 19.4 25 12.5 19.4 0 12.5 0Zm5.8 8.5c-.188 1.975-1 6.775-1.413 8.988-.175.937-.524 1.25-.85 1.287-.724.063-1.274-.475-1.974-.938-1.1-.724-1.726-1.174-2.788-1.874-1.238-.813-.438-1.263.275-1.988.188-.188 3.387-3.1 3.45-3.362a.25.25 0 0 0-.063-.226c-.074-.062-.175-.037-.262-.024-.113.024-1.863 1.187-5.275 3.487-.5.338-.95.513-1.35.5-.45-.012-1.3-.25-1.938-.463-.787-.25-1.4-.387-1.35-.825.026-.224.338-.45.926-.687 3.65-1.588 6.074-2.637 7.287-3.137 3.475-1.45 4.188-1.7 4.662-1.7.1 0 .338.024.488.15.125.1.163.237.175.337-.012.075.012.3 0 .475Z"/></svg>
						</a>
					</div>
				</div>
				<div class="footer-row__col">
					<div class="footer-menus">
					
						<?php // Меню дефолтное
						$args = array(
							'theme_location' => 'menu-footer',
							'container' => '',
							'menu_class' => 'footer-menu',
							'depth' => 0,
							'fallback_cb' => '',
						);
						wp_nav_menu($args);
						?>
						
						<?php // Меню дефолтное
						$args = array(
							'theme_location' => 'menu-footer2',
							'container' => '',
							'menu_class' => 'footer-menu menu-footer--red',
							'depth' => 0,
							'fallback_cb' => '',
						);
						wp_nav_menu($args);
						?>
						
						<?php // Меню дефолтное
						$args = array(
							'theme_location' => 'menu-footer3',
							'container' => '',
							'menu_class' => 'footer-menu footer-menu--hide-mobile',
							'depth' => 0,
							'fallback_cb' => '',
						);
						wp_nav_menu($args);
						?>
				
					</div>
				</div>
			</div>
			
			<div class="footer-policy">
				<div class="footer-policy__container">
				
				<?php // Меню дефолтное
					$args = array(
						'theme_location' => 'menu-footer-center',
						'container' => '',
						'menu_class' => 'footer-menu',
						'depth' => 0,
						'fallback_cb' => '',
					);
					wp_nav_menu($args);
					?>
				
				</div>
			</div>
			
			<div class="created-by">
				<img src="<?php echo get_template_directory_uri(); ?>/assets/img/created-by-new.svg">
			</div>
		</div>
	</footer>

	<?php wp_footer(); ?>

<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function(m,e,t,r,i,k,a){
        m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();
        for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
        k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)
    })(window, document,'script','https://mc.yandex.ru/metrika/tag.js', 'ym');

    ym(101042229, 'init', {webvisor:true, clickmap:true, accurateTrackBounce:true, trackLinks:true});
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/101042229" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->



</body>
</html>
