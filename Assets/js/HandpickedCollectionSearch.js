
var HandpickedCollectionSearch = Backbone.View.extend({

	id: '',
	highestId: '',
	posts: {},
	filtered: {},
	items: {},
	selectedItems: {},
	postTypeSelector: {},

	included: '',
	excluded: '',

	events: {

		'keyup #search-posts' : 'searchItems'

	},


	initialize: function(){

	
		var self = this;
		self.id = self.$el.data('id');
		self.highestId = parseInt( self.$el.data( 'highest-id' ) );
		
		//set the post-type selector:
		self.postTypeSelector = self.$el.parent().parent().find('.field-post_type');

		self.included = self.$el.find( '.selected-items' );
		self.excluded = self.$el.find( '.not-selected' );

		self.setSelected();
		self.setEvents();
		self.setItemPositions();
	

	},



	setEvents: function(){

		var self = this;

        self.$el.find('.records').sortable({
			connectWith: '.records',
			stop: function(e, ui){
				
				self.setItemPositions();

				var _item = jQuery( ui.item[0] );

				if( _item.parent().hasClass('selected-items') === true ){

					_item.find('input').prop( 'disabled', false );

				}else{
					_item.find('input').prop( 'disabled', true );
				}


			}
		}).disableSelection();


		self.postTypeSelector.change(function() {
  			
  			self.fetchPosts();

		});


	},


	/**
	 * Set item positions:
	 *
	 * @return void
	 */
	setItemPositions: function(){

		var self = this;
		var i = 0;

		self.$el.find( '.selected-items li').each( function(){

			jQuery( this ).find( '#position' ).val( i );
			i++;

		});
		
	},


	renderList: function(){

		var self = this;
		var html = jQuery('#handpickedcollection_search_template').html();
		var template = '';

		jQuery('.not-selected-items').html('');


		for( var i = 0; i < self.filtered.length; i++ ){

			var item = self.filtered[ i ];
			var datas = {

				item_id: item.ID,
				title: item.post_title,
				type: item.post_type,
				position: i

			}


			if( self.selectedItems.indexOf( item.ID ) === -1 ){
				var _temp = _.template( html );
				template += _temp( datas );
			}
		}

		jQuery( '.not-selected .spinner' ).remove();
		jQuery('.not-selected-items').append( template );	

		return false;

	},



	setSelected: function(){

		var self = this;
		self.selectedItems = new Array();

		jQuery( '.selected-items li').each( function(){

			jQuery( this ).find('input').prop( 'disabled', false );

			var _id = jQuery( this ).data( 'id' );
			self.selectedItems.push( _id );

		});

		self.fetchPosts();
	},


	addItem: function(){


	},

	removeItem: function(){


	},


	cleanField: function(){

		jQuery('.not-selected-items').html( '' );

	},

	searchItems: function( e ){

		var self = this;
		e.preventDefault;

		//if( e.keyCode == '13' ){

			var val = jQuery( e.target ).val().toLowerCase();

			//look through the results and return matches:
			var results = _.filter( self.posts, function( item ){

				return ( item.post_title.toLowerCase().indexOf( val ) > -1 );

			});
		
			self.filtered = results;
			self.renderList();
		//}

		return false;
	},


	/**
	 * Fetch the post for this search-form:
	 * @return {[type]} [description]
	 */
	fetchPosts: function(){

		var self = this;
		var posttype = self.postTypeSelector.val();

		var data = {
			'action' 		: 'fetchHandpickedCollection',
			'post_id'		: self.$el.data('post_id' ),
			'post_type'		: posttype
		};

		//console.log(data);

		jQuery.post( ajaxurl, data, function( response ){
			self.posts = JSON.parse( response );
			self.filtered = self.posts;
			self.renderList();
			return response;
		});

	},

	destroy: function(){
		this.undelegateEvents();
	}

});


var handpickedCollectionSearch = [];


jQuery( document ).ready( function( $ ){

	setHandpickedCollectionSearch();

});

jQuery( document ).on( 'refreshFields', function(){

	for( var i = 0; i < handpickedCollectionSearch.length; i++ ){

		handpickedCollectionSearch[ i ].destroy();

	}

	handpickedCollectionSearch = [];
	setHandpickedCollectionSearch();

});


function setHandpickedCollectionSearch(){

	var query = false;

	jQuery('.handpickedcollection-search-field').each( function( index, obj ){
		
		var ps = new HandpickedCollectionSearch( { el: obj } );
		handpickedCollectionSearch.push( ps );

	});
}

