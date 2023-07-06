<?php
/**
 * Plugin Name: Chef Handpicked Collection Column
 * Plugin URI: http://chefduweb.nl/plugins/chef-handpicked-collection-column
 * Description: pick the content of your collection by hand
 * Version: 1.1
 * Author: Luc Princen & Remy Bakker
 * Author URI: http://www.chefduweb.nl/
 * License: GPLv2
 * 
 * @package Cuisine
 * @category Core
 * @author Chef du Web
 */

//Chaning the namespaces is the most important part, 
//after that the bus pretty much drives itself.
namespace HandpickedCollectionColumn;

use Cuisine\Wrappers\Script;
use Cuisine\Wrappers\Sass;
use Cuisine\Utilities\Url;


class ColumnIgniter{ 

	/**
	 * Static bootstrapped HandpickedCollectionColumn\ColumnIgniter instance.
	 *
	 * @var \HandpickedCollectionColumn\ColumnIgniter
	 */
	public static $instance = null;


	/**
	 * Init admin events & vars
	 */
	function __construct(){

		//register column:
		$this->register();

		//load the right files
		$this->load();

		//assets:
		$this->enqueues();


	}


	/**
	 * Register this column-type with Chef Sections
	 * 
	 * @return void
	 */
	private function register(){


		add_filter( 'chef_sections_column_types', function( $types ){

			$base = Url::path( 'plugin', 'chef-handpicked-collection-column', true );

			//change the $types[ key ] and the name value:
			$types['handpickedcollection'] = array(
						'name'		=> 'Handpicked Collection',
						'class'		=> 'HandpickedCollectionColumn\Column',
						'template'	=> $base.'Assets/template.php'
			);

			return $types;

		});

		add_action( 'init', function(){

			add_filter( 'cuisine_field_types', function( $arr ){

				$arr['handpickedcollection'] = array(
					'name'		=> 'Posts Zoeker',
					'class'		=> 'HandpickedCollectionColumn\\Hooks\\HandpickedCollectionField'
				);

				return $arr;

			});


		
		});
	}

	/**
	 * Load all includes for this plugin
	 * 
	 * @return void
	 */
	private function load(){

		//auto-loads all .php files in these directories.
            $includes = array( 
                'Classes/Wrappers',      //facades
                'Classes/Hooks',
                'Classes/Front',
                'Classes/Admin',
                'Classes'
            );

            foreach( $includes as $inc ){
                
                $root = static::getPluginPath();
                $files = glob( $root.$inc.'/*.php' );

                foreach ( $files as $file ){

                    require_once( $file );

                }
            }

	}


	/**
	 * Enqueue scripts & Styles
	 * 
	 * @return void
	 */
	private function enqueues(){

	


		add_action( 'admin_menu', function(){


			$url = Url::plugin( 'chef-handpicked-collection-column', true ).'Assets';

			//enqueue a script
			//wp_enqueue_script( 'chef_related', $url.'/js/Admin.js' );
			wp_enqueue_script( 'chef_handpicked_collection_search', $url.'/js/HandpickedCollectionSearch.js' );

			//enqueue a stylesheet:
			wp_enqueue_style( 'handpicked-collection-style', $url.'/css/admin.css' );
			
		});
		
	}


	/*=============================================================*/
	/**             Getters & Setters                              */
	/*=============================================================*/


	/**
	 * Init the \HandpickedCollectionColumn\ColumnIgniter Class
	 *
	 * @return \HandpickedCollectionColumn\ColumnIgniter
	 */
	public static function getInstance(){
		
	    return static::$instance = new static();

	}

	        

    /**
     * Get the plugin path
     * 
     * @return string
     */
    public static function getPluginPath(){
    	return __DIR__.DS;
    }


}


add_action('chef_sections_loaded', function(){

	\HandpickedCollectionColumn\ColumnIgniter::getInstance();

});
