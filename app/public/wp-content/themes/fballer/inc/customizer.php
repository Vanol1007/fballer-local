<?php

// Защита от прямого доступа к файлу
if ( ! defined('ABSPATH') ) {
	exit;
}

// Подключаем Customizer
function theme_customize_register($wp_customize) {
	// Подключаем JS и CSS
	function theme_customizer_css_and_scripts() {
		wp_enqueue_style( 'theme-customizer', get_template_directory_uri() . '/assets/css/customizer.css', '', filemtime(dirname( __FILE__ ) . '/../assets/css/customizer.css') );
	}
	add_action( 'customize_controls_enqueue_scripts', 'theme_customizer_css_and_scripts' );
	
	// Секции
	$wp_customize->add_section(
		'theme_section_optimization',
		array(
			'title' => 'Оптимизация',
			'priority'  => 210,
			'description' => 'Настройки оптимизации сайта',
		)
	);
	$wp_customize->add_section(
		'theme_additional_scripts',
		array(
			'title' => 'Дополнительные скрипты',
			'priority'  => 200,
			'description' => '',
		)
	);
	$wp_customize->add_section(
		'theme_section_content',
		array(
			'title' => 'Контент',
			'priority'  => 220,
			// 'description' => '',
		)
	);

	// Настройки секции "Свойства сайта"
	$wp_customize->add_setting(
		'mobile-color',
		array(
			'default' => '#000000',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'mobile-color',
			array(
				'section' => 'title_tagline',
				'label' => 'Цвет вкладок для мобильных устройств',
				'description' => '',
			)
		)
	);
	
	$wp_customize->add_setting(
		'header-logo',
		array(
			'default' => '',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'header-logo',
			array(
				'section' => 'title_tagline',
				'label' => 'Логотип сайта в шапке',
			)
		)
	);
	
	$wp_customize->add_setting(
		'footer-logo',
		array(
			'default' => '',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'footer-logo',
			array(
				'section' => 'title_tagline',
				'label' => 'Логотип сайта в подвале',
			)
		)
	);

	// Настройки секции "Дополнительные скрипты"
	$wp_customize->add_setting(
		'custom_html_head',
		array(
			'default' => '',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Code_Editor_Control(
			$wp_customize,
			'custom_html_head',
			array(
				'section'   => 'theme_additional_scripts',
				'label' => 'Код внутри тега <head>',
				'code_type' => 'html',
				'description' => '',
			)
		)
	);
	
	$wp_customize->add_setting(
		'custom_html_footer',
		array(
			'default' => '',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Code_Editor_Control(
			$wp_customize,
			'custom_html_footer',
			array(
				'section'   => 'theme_additional_scripts',
				'label' => 'Код перед закрывающим тегом </body>',
				'code_type' => 'html',
				'description' => '',
			)
		)
	);
	
	// Настройки секции "Оптимизация"
	$wp_customize->add_setting(
		'optimization-meta-generator',
		array(
			'default' => false,
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Switch_Control(
			$wp_customize,
			'optimization-meta-generator',
			array(
				'section' => 'theme_section_optimization',
				'label' => 'Удалить meta generator',
				'type' => 'checkbox',
				'description' => '',
			)
		)
	);
	
	$wp_customize->add_setting(
		'optimization-rsd',
		array(
			'default' => false,
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Switch_Control(
			$wp_customize,
			'optimization-rsd',
			array(
				'section' => 'theme_section_optimization',
				'label' => 'Удалить RSD ссылку',
				'type' => 'checkbox',
				'description' => '',
			)
		)
	);
	
	$wp_customize->add_setting(
		'optimization-wlw-manifest',
		array(
			'default' => false,
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Switch_Control(
			$wp_customize,
			'optimization-wlw-manifest',
			array(
				'section' => 'theme_section_optimization',
				'label' => 'Удалить WLW manifest ссылку',
				'type' => 'checkbox',
				'description' => '',
			)
		)
	);
	
	$wp_customize->add_setting(
		'optimization-next-prev-url',
		array(
			'default' => false,
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Switch_Control(
			$wp_customize,
			'optimization-next-prev-url',
			array(
				'section' => 'theme_section_optimization',
				'label' => 'Убрать ссылки на предыдущую/следующую запись',
				'type' => 'checkbox',
				'description' => '',
			)
		)
	);
	
	$wp_customize->add_setting(
		'optimization-shortlink',
		array(
			'default' => false,
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Switch_Control(
			$wp_customize,
			'optimization-shortlink',
			array(
				'section' => 'theme_section_optimization',
				'label' => 'Удалить shortlink ссылку',
				'type' => 'checkbox',
				'description' => '',
			)
		)
	);
	
	$wp_customize->add_setting(
		'optimization-emoji',
		array(
			'default' => false,
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Switch_Control(
			$wp_customize,
			'optimization-emoji',
			array(
				'section' => 'theme_section_optimization',
				'label' => 'Отключить Emoji',
				'type' => 'checkbox',
				'description' => '',
			)
		)
	);
	
	$wp_customize->add_setting(
		'optimization-recentcomments',
		array(
			'default' => false,
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Switch_Control(
			$wp_customize,
			'optimization-recentcomments',
			array(
				'section' => 'theme_section_optimization',
				'label' => 'Удалить стили .recentcomments',
				'type' => 'checkbox',
				'description' => '',
			)
		)
	);
	
	$wp_customize->add_setting(
		'optimization-dns-prefetch',
		array(
			'default' => false,
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Switch_Control(
			$wp_customize,
			'optimization-dns-prefetch',
			array(
				'section' => 'theme_section_optimization',
				'label' => 'Удалить dns-prefetch',
				'type' => 'checkbox',
				'description' => '',
			)
		)
	);
	
	$wp_customize->add_setting(
		'optimization-xml-rpc',
		array(
			'default' => false,
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Switch_Control(
			$wp_customize,
			'optimization-xml-rpc',
			array(
				'section' => 'theme_section_optimization',
				'label' => 'Отключить XML-RPC',
				'type' => 'checkbox',
				'description' => '',
			)
		)
	);
	
	$wp_customize->add_setting(
		'optimization-archive-date',
		array(
			'default' => false,
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Switch_Control(
			$wp_customize,
			'optimization-archive-date',
			array(
				'section' => 'theme_section_optimization',
				'label' => 'Удалить архивы дат',
				'type' => 'checkbox',
				'description' => 'Удаляет полностью архивы дат и ставит редирект. Отключает виджет.',
			)
		)
	);
	
	$wp_customize->add_setting(
		'optimization-archive-user',
		array(
			'default' => false,
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Switch_Control(
			$wp_customize,
			'optimization-archive-user',
			array(
				'section' => 'theme_section_optimization',
				'label' => 'Удалить архивы пользователей',
				'type' => 'checkbox',
				'description' => 'Удаляет полностью архивы пользователей и ставит редирект.',
			)
		)
	);
	
	$wp_customize->add_setting(
		'optimization-adminbar',
		array(
			'default' => false,
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Switch_Control(
			$wp_customize,
			'optimization-adminbar',
			array(
				'section' => 'theme_section_optimization',
				'label' => 'Убрать ссылки из админ бара',
				'type' => 'checkbox',
				'description' => 'Убирает логотип Wordpress и все ссылки на wordpress.org из панели инструментов.',
			)
		)
	);
	
	$wp_customize->add_setting(
		'optimization-rss',
		array(
			'default' => false,
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Switch_Control(
			$wp_customize,
			'optimization-rss',
			array(
				'section' => 'theme_section_optimization',
				'label' => 'Отключить RSS ленты',
				'type' => 'checkbox',
				'description' => '',
			)
		)
	);
	
	$wp_customize->add_setting(
		'optimization-minify-html',
		array(
			'default' => false,
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Switch_Control(
			$wp_customize,
			'optimization-minify-html',
			array(
				'section' => 'theme_section_optimization',
				'label' => 'Минификация HTML',
				'type' => 'checkbox',
				'description' => '',
			)
		)
	);
	
	$wp_customize->add_setting(
		'optimization-disable-visual-search',
		array(
			'default' => false,
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Switch_Control(
			$wp_customize,
			'optimization-disable-visual-search',
			array(
				'section' => 'theme_section_optimization',
				'label' => 'Отключить визуальный поиск для изображений',
				'type' => 'checkbox',
				'description' => 'Визуальный поиск используется в некоторых браузерах, например Edge.',
			)
		)
	);
	
	// Настройки секции "Контент"
	$wp_customize->add_setting(
		'custom_content_archive_places',
		array(
			'default' => '',
		)
	);
	$wp_customize->add_control(
		new Text_Editor_Custom_Control(
			$wp_customize,
			'custom_content_archive_places',
			array(
				'section'   => 'theme_section_content',
				'label' => 'Контент "Поля"',
				'description' => 'Контент архивной страницы для типа записей "Поля".',
			)
		)
	);
	$wp_customize->add_setting(
		'custom_content_archive_champs',
		array(
			'default' => '',
		)
	);
	$wp_customize->add_control(
		new Text_Editor_Custom_Control(
			$wp_customize,
			'custom_content_archive_champs',
			array(
				'section'   => 'theme_section_content',
				'label' => 'Контент "Чемпионаты"',
				'description' => 'Контент архивной страницы для типа записей "Чемпионаты".',
			)
		)
	);
	$wp_customize->add_setting(
		'custom_content_archive_teams',
		array(
			'default' => '',
		)
	);
	$wp_customize->add_control(
		new Text_Editor_Custom_Control(
			$wp_customize,
			'custom_content_archive_teams',
			array(
				'section'   => 'theme_section_content',
				'label' => 'Контент "Комнанды"',
				'description' => 'Контент архивной страницы для типа записей "Команды".',
			)
		)
	);
	
	
}
add_action( 'customize_register', 'theme_customize_register' );

// Новые типы полей для Customizer
if ( class_exists('WP_Customize_Control') ) {
	// Переключатель
	class WP_Customize_Switch_Control extends WP_Customize_Control {
		public $type = 'switch';
		public $relation = '';

		public function render_content(){
			?>
			<div class="checkbox_switch <?php echo $this->relation; ?>">
				<div class="onoffswitch">
					<input type="checkbox" id="<?php echo esc_attr($this->id); ?>" name="<?php echo esc_attr($this->id); ?>" class="onoffswitch-checkbox" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); checked( $this->value() ); ?>>
					<label class="onoffswitch-label" for="<?php echo esc_attr($this->id); ?>"></label>
				</div>
				<span class="customize-control-title onoffswitch_label"><?php echo esc_html( $this->label ); ?></span>
				<p><?php echo wp_kses_post($this->description); ?></p>
			</div>
			<?php
		}
	}
	
	// Визуальный редактор
	class Text_Editor_Custom_Control extends WP_Customize_Control {
		public $type = 'tinymce_editor';
		
		public function enqueue(){
			wp_register_script( 'text-editor-custom-control', '', array('jquery'), '', true );
			wp_enqueue_script('text-editor-custom-control');
			wp_add_inline_script( 'text-editor-custom-control', 'jQuery(document).ready(function($){
				$(".customize-control-tinymce-editor").each(function(){
					wp.editor.initialize($(this).attr("id"), {
						tinymce: {
							wpautop: true,
							selector: "textarea",
							height: 300,
							toolbar1: "bold,italic,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,fullscreen,wp_adv",
							toolbar2: "formatselect,underline,alignjustify,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help"
						},
						quicktags: true,
						mediaButtons: true,
					});
				});
				
				$(document).on( "tinymce-editor-init", function( event, editor ) {
					editor.on("change", function(e) {
						tinyMCE.triggerSave();
						$("#"+editor.id).trigger("change");
					});
				});
			});' );
			wp_enqueue_editor();
		}

		public function render_content(){
			?>
			<div class="tinymce-control">
				<span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
				<?php if ( !empty($this->description) ) { ?>
					<span class="customize-control-description"><?php echo esc_html($this->description); ?></span>
				<?php } ?>
				<textarea id="<?php echo esc_attr($this->id); ?>" class="customize-control-tinymce-editor" <?php $this->link(); ?>><?php echo esc_attr($this->value()); ?></textarea>
			</div>
			<?php
		}
	}
}
