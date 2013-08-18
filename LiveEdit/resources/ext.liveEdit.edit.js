(function ( $ ) {
	var edit = mw.LiveEdit.EditPage = {
		$box: null,

		$list: null,

		initialCheck: true,

		editors: {},

		init: function() {
			// Mark the form field as active
			$( '#liveEditActive' ).val( 'true' );

			// Add the div for listing users
			edit.$box = $( '<div></div>' )
				.attr( 'id', 'liveEditors' )
				.css( 'display', 'none' )
				.html( $( '<strong></strong>' )
					.text( mw.msg( 'liveedit-editpage-text' ) ) );

			edit.$list = $( '<ul></ul>' );
			edit.$box.append( edit.$list );

			$( '#wpTextbox1' ).after( edit.$box );

			$(window).bind( 'beforeunload', edit.pauseSession );

			edit.updateSession();
			edit.getEditors();
		},

		updateSession: function() {
			mw.LiveEdit.updateSessionCall( 'active', edit.updateSessionCallback, true );
		},

		updateSessionCallback: function( data ) {
			if ( ( 'error' in data ) || !data.liveeditsessionupdate ) {
				// TODO: Recover from an expired session
				setTimeout( edit.updateSession, 10000 );
				return false;
			}

			// 5 seconds
			setTimeout( edit.updateSession, mw.config.get( 'wgLiveEditSessionUpdateInterval' ) );
		},

		getEditors: function() {
			mw.LiveEdit.getEditingUsers( edit.listEditingUsers );
		},

		listEditingUsers: function( editors ) {
			var currentEditors = new Array,
				newEditors = new Array;

			editors = editors || [];

			for ( var i = 0, len = editors.length; i < len; i++ ) {
				var editor = editors[i];

				// Don't show ourselves!
				if ( editor.user === mw.config.get( 'liveEditUser' ) ) {
					continue;
				}

				currentEditors.push( editor.user );

				// Check to see if this is user is already known
				if ( edit.editors[ editor.user ] !== undefined ) {
					continue;
				}

				newEditors.push( editor.user );

				var $line = $( '<li></li>' )
					.text( editor.user );

				edit.editors[ editor.user ] = {
					user: editor.user,
					start: editor.start,
					line: $line
				};
			}

			for ( var i = 0; i < newEditors.length; i++ ) {
				var editor = newEditors[i];
				if ( !edit.initialCheck ) {
					edit.editors[editor].line.hide();
				}
				edit.$list.append( edit.editors[editor].line );
				if ( !edit.initialCheck ) {
					edit.editors[editor].line.fadeIn();
				}
			}

			if ( edit.initialCheck && currentEditors.length ) {
				edit.$box.show();
			} else if ( currentEditors.length ) {
				edit.$box.slideDown();
			} else if ( !edit.initialCheck && currentEditors.length === 0 ) {
				edit.$box.slideUp();
			}

			// Remove editors who are no longer editing
			for ( var editor in edit.editors ) {
				if ( edit.editors.hasOwnProperty( editor ) && currentEditors.indexOf( editor ) === -1 ) {
					// The user is no longer editing, remove them
					edit.editors[editor].line.fadeOut( 'slow', function() {
						$(this).remove();
					} );
					delete edit.editors[editor];
				}
			}

			edit.initialCheck = false;
			setTimeout( edit.getEditors, mw.config.get( 'wgLiveEditQueryInterval' ) );
		},

		pauseSession: function() {
			$.cookie(
				'killLiveEditSession',
				mw.config.get( 'liveEditSessionId' ),
				mw.LiveEdit.cookieSettings
			);
			mw.LiveEdit.updateSessionCall( 'paused', function() {}, false );
		}
	};

	$( edit.init );
})( jQuery );