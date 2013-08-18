(function ( $ ) {
	var read = mw.LiveEdit.ReadPage = {

		$editLink: null,

		topIcon: null,

		sectionIcons: [],

		activeSectionIcons: [],

		init: function() {
			read.$editLink =
				$( '#ca-edit > span > a' )
					.tipsy( { gravity: 'e', trigger: 'manual', title: 'tipsy' } )
					.hover( read.editLinkHoverIn, read.editLinkHoverOut );

			read.topIcon = $( '<div></div>', { 'class': 'liveEdit-icons' } )
				.hide()
				.prependTo( read.$editLink );

			$( '.liveEdit-icons.editsection' ).each(function() {
				var $e = $(this)
						.tipsy( { gravity: 'se', trigger: 'manual' } );
				$e.parents( '.liveEdit-wrap' )
					.hover( read.sectionLinkHoverIn, read.sectionLinkHoverOut );
				read.sectionIcons[$e.data( 'sectionid' )] = $e;
			});

			read.getEditors();
		},

		editLinkHoverIn: function( e ) {
			if ( $( '#ca-edit' ).hasClass( 'liveEdit-editing' ) ) {
				read.$editLink.tipsy( 'show' );
			}
		},

		editLinkHoverOut: function( e ) {
			read.$editLink.tipsy( 'hide' );
		},

		/**
		 * @todo DelayOut doesn't work with manual triggers
		 */
		sectionLinkHoverIn: function( e ) {
			var $icon = $(this).children('.liveEdit-icons');

			// Check to see if it's visible
			if ( $icon.css( 'display' ) === 'block' ) {
				$icon.tipsy( 'show' );
			}
		},

		sectionLinkHoverOut: function( e ) {
			$(this).children('.liveEdit-icons').tipsy( 'hide' );
		},

		getEditors: function() {
			mw.LiveEdit.getEditingUsers( read.showEditingUsers );
		},

		showEditingUsers: function( editors ) {
			if ( editors === undefined || !editors.length ) {
				$( '#ca-edit' ).removeClass( 'liveEdit-editing' );
				read.topIcon.hide();
				editors = [];
			} else {
				$( '#ca-edit' ).addClass( 'liveEdit-editing' );
			}

			var currentEditors = [],
				editorsInSections = {};

			for ( var i = 0, len = editors.length; i < len; i++ ) {
				var editor = editors[i];
				currentEditors.push( editor.user );

				if ( editor.section > 0 ) {
					if ( editorsInSections[editor.section] === undefined ) {
						editorsInSections[editor.section] = new Array;
					}

					editorsInSections[editor.section].push( editor.user );
				}
			}

			for ( var i = 0, len = read.activeSectionIcons.length; i < len; i++ ) {
				var section = read.activeSectionIcons[i];
				if ( editorsInSections[section] === undefined ) {
					read.sectionIcons[section].fadeOut();
					read.activeSectionIcons.splice( i, 1 );
				}
			}

			for ( var sectionId in editorsInSections ) {
				if ( !editorsInSections.hasOwnProperty( sectionId ) ) {
					continue;
				}

				var sectionEditors = editorsInSections[sectionId];
				var usernames = sectionEditors.join( ', ' );

				read.sectionIcons[sectionId]
					.attr( 'title', mw.msg( 'liveedit-editing-section', usernames ) )
					.fadeIn();

				if ( read.activeSectionIcons.indexOf( sectionId ) === -1 ) {
					read.activeSectionIcons.push( sectionId );
				}
			}

			if ( editors.length ) {
				var title = mw.msg( 'liveedit-editing', currentEditors.join( ', ' ) );
				read.topIcon.show();
				read.$editLink.attr('tipsy', title);
			}

			setTimeout( read.getEditors, mw.config.get( 'wgLiveEditQueryInterval' ) );
		}
	};

	$( read.init );
})( jQuery );