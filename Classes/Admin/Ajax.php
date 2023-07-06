<?php

	namespace HandpickedCollectionColumn\Admin;

	use \Cuisine\Wrappers\PostType;
	use \HandpickedCollectionColumn\Wrappers\AjaxInstance;
	use \WP_Query;

	class Ajax extends AjaxInstance{

		/**
		 * Init admin ajax events:
		 */
		function __construct(){

			$this->listen();

		}

		/**
		 * All backend-ajax events for this plugin
		 * 
		 * @return string, echoed
		 */
		private function listen(){


			add_action( 'wp_ajax_fetchHandpickedCollection', function(){

				$this->setPostGlobal();

				//query caching
				global $handpickedCollection;
				global $post;

				if( !isset( $handpickedCollection ) ){
					
					$post_types = apply_filters( 'chef_handpicked_collection_posttypes', 'post' );

					if( isset( $_POST ) && isset( $_POST['post_type'] ) ) {
						$post_types = $_POST['post_type'];
					}

					$query = new WP_Query( 
						array( 
							'post_type' => $post_types, 
							'posts_per_page' => -1, 
							'post__not_in'=> array( $post->ID ),
							'post_status' => 'publish'
						)
					);
	
					$GLOBALS['handpickedCollection'] = $query->posts;
					$return = $query->posts;				
				
				}else{
					$return = $handpickedCollection;
				
				}


				//return the post-list
				echo json_encode( $return );

				die();

			});

		}
	}


	if( is_admin() )
		\HandpickedCollectionColumn\Admin\Ajax::getInstance();
