<?php
	/**
	 * LO WP Template Loader
	 *
	 * @since   0.3.0
	 * @package Liquid Outreach
	 */
	
	/**
	 * Template Loader.
	 *
	 * @since 0.3.0
	 */
	class LO_WP_Template_Loader {
		
		/**
		 * Template names array
		 *
		 * @var array
		 * @since 0.3.0
		 */
		public $templates = array();
		
		/**
		 * Template name
		 *
		 * @var string
		 * @since 0.3.0
		 */
		public $template = '';
		
		/**
		 * Template file extension
		 *
		 * @var string
		 * @since 0.3.0
		 */
		protected $extension = '.php';
		
		public function __construct() {
			//            add_filter('archive_template', array($this, 'load_template'));
			add_filter( 'template_include', array( $this, 'template_loader' ) );
		}
		
		/**
		 * Load a template.
		 *
		 * Handles template usage so that we can use our own templates instead of the themes.
		 *
		 * Templates are in the 'templates' folder. plugin looks for theme
		 * overrides in /theme/scrptz-tdl/ by default
		 *
		 * @param mixed $template
		 *
		 * @return string
		 * @since 0.3.0
		 */
		public function template_loader( $template ) {
			$file          = '';
			$base_template = "index{$this->extension}";
			
			$find[] = "templates/wp-template/$base_template";
			
			global $wp_query;
			$event_cat_tax           = liquid_outreach()->lo_ccb_event_categories->taxonomy();
			$event_post_type         = liquid_outreach()->lo_ccb_events->post_type();
			$event_partner_post_type = liquid_outreach()->lo_ccb_event_partners->post_type();
			
			if ( is_singular( $event_post_type ) ) {
				
				$page_template = $this->template = "single-event{$this->extension}";
				$file          = $this->template = "{$page_template}";
				
				$find[] = "templates/wp-template/$file";
				$find[] = 'liquid-outreach/' . $base_template;
				$find[] = 'liquid-outreach/' . $file;
				
			} elseif ( is_singular( $event_partner_post_type ) ) {
				
				$page_template = $this->template = "single-event-partner{$this->extension}";
				$file          = $this->template = "{$page_template}";
				
				$find[] = "templates/wp-template/$file";
				$find[] = 'liquid-outreach/' . $base_template;
				$find[] = 'liquid-outreach/' . $file;
				
			} elseif ( is_category( $event_cat_tax ) || is_tax( $event_cat_tax ) ) {
				
				$page_template = $this->template = "single-event-category{$this->extension}";
				$file          = $this->template = "{$page_template}";
				
				$find[] = "templates/wp-template/$file";
				$find[] = 'liquid-outreach/' . $base_template;
				$find[] = 'liquid-outreach/' . $file;
				
			}
			
			if ( $file ) {
				
				$template = locate_template( array_unique( $find ) );
				if ( ! $template ) {
					$template = Liquid_Outreach::$path . 'templates/wp-template/' . $file;
					if ( ! file_exists( $template ) ) {
						$template = Liquid_Outreach::$path . 'templates/wp-template/' .
						            $base_template;
					}
				}
			}
			
			return $template;
		}
		
	}
