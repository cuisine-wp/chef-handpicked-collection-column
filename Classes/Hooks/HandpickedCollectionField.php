<?php

	namespace HandpickedCollectionColumn\Hooks;

	use Cuisine\Fields\DefaultField;
	use Cuisine\Utilities\Session;
	use \Cuisine\Utilities\Sort;
	use ChefRelated\Database\DB;
	use \ChefRelated\Front\Settings;


	class HandpickedCollectionField extends DefaultField{



		/**
		 * The current Post ID
		 * 
		 * @var integer
		 */
		var $post_id = 0;


		/**
		 * Method to override to define the input type
		 * that handles the value.
		 *
		 * @return void
		 */
		protected function fieldType(){
		    $this->type = 'handpickedcollection';

		}


		/**
		* Define a core Field.
		*
		* @param array $properties The text field properties.
		*/
		public function __construct( $name, $label = '', $props = array() ){

			 $this->post_id = Session::postId();

			 parent::__construct($name, $label, $props );
			 
		}

		/**
		 * Build the html
		 *
		 * @return String;
		 */
		public function build(){

		    $posts = $this->getValue();
		    $posts = Sort::byField( $posts, 'position', 'ASC' );
		    
		    $html = '<div class="handpickedcollection-search-field" data-highest-id="'.$this->getHighestItemId().'" data-post_id="'.$this->post_id.'">';

		    $html .= '<div class="not-selected-wrapper">';
		    	$html .= '<div class="search-bar">';
		    		$html .= '<input type="text" placeholder="Zoeken..." id="search-posts">';
		    	$html .= '</div>';

		    	$html .= '<div class="not-selected">';
		    	$html .= '<h3>'.__( 'Niet geselecteerd', 'chefrelated').'</h3>';

		    	$html .= '<span class="spinner"></span>';

		    	$html .= '<ul class="not-selected-items records">';


		    	$html .= '</ul></div>';

		    $html .= '</div>';
		    $html .= '<div class="is-selected">';
		    	$html .= '<h3>'.__( 'Geselecteerd', 'chefrelated').'</h3>';
		    	$html .= '<ul class="selected-items records">';
		    	$i = 0;

		    	if( !empty( $posts ) ){

		    		$shown = array();

		    		foreach( $posts as $p ){
		    			
		    			if( get_post_status( $p['id'] ) == 'publish' && !in_array( $p['id'], $shown ) ){
		    				$html .= $this->makeItem( $p );
		    				$shown[] = $p['id'];
		    			}
	
		    		}
		    	}

		    	$html .= '</ul>';

		    $html .= '</div><div class="clear"></div>';
		    $html .= '</div>';

		    return $html;

		}

		/**
		 * Get a single post-block
		 * 
		 * @return String
		 */
		public function makeItem( $item ){

			$prefix = '<input type="hidden" class="multi" name="';
			$prefix .= $this->name.'['.$item['id'].']';

			$html = '';
			$html .= '<li data-id="'.$item['id'].'">';
				$html .= '<b>'.str_replace( "\\",'', $item['title'] ).'</b>';
				$html .= '<span class="type">'.$item['type'].'</span>';

				$html .= $prefix.'[id]" value="'.$item['id'].'" disabled>';
				$html .= $prefix.'[title]" value="'.str_replace( "\\",'', $item['title'] ).'" disabled>';
				$html .= $prefix.'[type]" value="'.$item['type'].'" disabled>';
				$html .= $prefix.'[position]" value="'.$item['position'].'" id="position" disabled>';

			$html .= '</li>';
			
			return $html;
		}



		/**
		 * Return the template, for Javascript
		 * 
		 * @return String
		 */
		public function renderTemplate(){

		    //make a clonable item, for javascript:
		    $html = '<script type="text/template" id="handpickedcollection_search_template">';
		        $html .= $this->makeItem( array( 
		            'id' => '<%= item_id %>',
		            'title' => '<%= title %>', 
		            'type' => '<%= type %>',
		            'position' => '<%= position %>',
		        ) );
		    $html .= '</script>';

		    return $html;
		}



		/**
		 * Get the highest item ID available
		 * 
		 * @return int
		 */
		private function getHighestItemId(){

		    $posts = $this->getValue();
		    return count( $posts );

		}




		/**
	     * Get the value of this field:
	     * 
	     * @return String
	     */
	    public function getValue(){

	    	$val = [];

	        if( $this->properties['defaultValue'] )
	            $val = $this->getDefault();

	        return $val;
	    }

	}
