

define(
[
    'jquery',
    'selectize'
],function($, selectize){

	//Modified from:
	//https://webdesign.tutsplus.com/tutorials/how-to-add-deep-linking-to-the-bootstrap-4-tabs-component--cms-31180
	var url = location.href.replace( /\/$/, "" );
	if ( location.hash && $( ".tab-content").length > 0 ) {
		var hash = url.split( "#" );
		$( 'a[href="#' + hash[1]+'"]' ).tab( "show" );
		url = location.href.replace( /\/#/, "#" );
		history.replaceState( null, null, url );
		setTimeout( function() {
			$(window).scrollTop(0);
		}, 400);
	} else if ( location.hash ) {
		setTimeout( function() {
			$( location.hash )[0].scrollIntoView();
			window.scrollBy(0, -100); 
		}, 100);
	}

	$( 'a[data-toggle="tab"]' ).on("click", function() {
		var newUrl;
		var hash = $(this).attr( "href" );
		if( hash == "#home" ) {
			newUrl = url.split("#")[0];
		} else {
			newUrl = url.split("#")[0] + hash;
		}
		history.replaceState( null, null, newUrl );
	});

	function handleFormResponse( $form, message, type = "danger" ) {
		$form.append( '<div class="alert alert-' + type + ' mt-2 form-response">' + message + '</div>' );
		setTimeout(function() {
			$( $form.find( ".form-response" ) ).slideUp();
		}, 6000);
	}

	function ajax( url, data, $btn, $response_region, success_callback ) {
		btn_text = $btn.text();
		$btn.text( "loading..." ).prop( "disabled", true );
		
		$.ajax({
			url: url, 
			type: "POST",             
			data: data,
			contentType: false,
			cache: false,
			processData: false,
			success: function( response ) {
				$btn.text( btn_text ).prop( "disabled", false );
				if( response.error ) {
					handleFormResponse( $response_region, response.error );
					return;
				} else if( response.success ) {
					handleFormResponse( $response_region, response.success, "success" );
				}
				if( typeof callback === "function" ) {
					callback( response );
				}
				if( response.redirect ) {
					$btn.text( btn_text ).prop( "disabled", true );
					if( location.href.split(location.host)[1] == response.redirect ) {
						location.reload();
					} else {
						var delay = ( response.redirect_delay ? response.redirect_delay : 0 );
						setTimeout(function() {
							window.location.href = response.redirect;
							setTimeout(function() {
								location.reload(); //Be sure page reloads with # in response.redirect
							}, 500);
						}, delay );
						
					}
				}
			},
			error: function(jqXHR) {
				var errorMessage = '';
				if (jqXHR.status == 500) {
				  errorMessage='Server side 500 error';
				} else if (jqXHR.status == 404) {
				  errorMessage='Not found 404 error';
				} else if (jqXHR.status == 404) {
				  errorMessage='Not allowed 403 error. Are you still logged ?';
				} else {
				  errorMessage='An error occured';
				}
				handleFormResponse( $response_region, errorMessage );
				$btn.text( btn_text ).prop( "disabled", false );
			}
		});
	}

	$( "form.auto-submit" ).submit( function(e) {
		$form = $(this);
		$btn = $form.find( "button[type='submit']" );
		e.preventDefault();
		$response_target = $form;
		if( $form.find( ".modal-body" ).length > 0 ) {
			$response_target = $form.find( ".modal-body" );
		}
		action = $(this).attr( "action" );
		
		ajax( action, new FormData( $form[0] ), $btn, $response_target );
	});

	$( ".custom-file input" ).change( function (e) {
		var files = [];
		for ( var i = 0; i < $(this)[0].files.length; i++ ) {
			files.push( $(this)[0].files[i].name );
		}
		$(this).next( ".custom-file-label" ).html(files.join( ", " ) );
	});

	if( $( ".language-select" ).length > 0 ) {
		$.getJSON( "/projects/get_languages", function( languages ) {
			$( ".language-select" ).each( function( index ) {
				ids = $(this).attr( "data-selected-ids" );
				selected_ids = ( ids ? ids.split( "|" ) : [] );
				selected_ids.push( $(this).attr( "data-selected-id" ) );
				$item = $(this);
				$.each( languages, function( key, item ) {
					var is_selected = ( selected_ids.indexOf( item.id ) !== -1 ? "selected" : "" );
					$item.append( "<option value='" + item.id + "' " + is_selected + ">" + item.name + "</option>" );
				});
				$item.selectize();
			});
		});
	}

	$( ".selectize" ).selectize( {
		"allowEmptyOption": false
	});

	$( ".series-select" ).selectize( {
		"create": true,
	});

	$( ".commit-paragraph" ).click( function(e) {
		$parent = $(this).parents( ".editor-item" );
		$btn = $(this);
		btn_text = $btn.text();
		$btn.text( "..." ).prop( "disabled", true );
		e.preventDefault();
		var data = {
			"content": $parent.find( "textarea" ).val(),
			"project_id": $parent.attr( "data-project-id" ),
			"content_id": $parent.attr( "data-content-id" ),
		}
		$.post( "/editor/commit", data, function( response ) {
			$btn.text( btn_text ).prop( "disabled", false );
			if( response.error ) {
				handleFormResponse( $parent.find( ".response" ), response.error );
				return;
			}
			handleFormResponse( $parent.find( ".response" ), response.success, "success" );
		})
		.fail(function() {
			handleFormResponse( $parent.find( ".response" ), "An error has occured" );
			$btn.text( btn_text ).prop( "disabled", false );
		});
	});

	$( ".revision-header" ).click( function() {
		$(this).next().slideToggle();
	});

	$( ".approve-paragraph" ).click( function(e) {
		$parent = $(this).parents( ".editor-item" );
		$btn = $(this);
		btn_text = $btn.text();
		$btn.text( "..." ).prop( "disabled", true );
		e.preventDefault();
		var data = {
			"project_id": $parent.attr( "data-project-id" ),
			"content_id": $parent.attr( "data-content-id" ),
		}
		$.post( "/editor/approve", data, function( response ) {
			if( response.error ) {
				handleFormResponse( $parent.find( ".response" ), response.error );
				return;
			}
			$btn.text( btn_text ).addClass( "hidden" );
			$parent.find( "textarea" ).prop( "disabled", response.lock_editing );
			$parent.find( ".approval_count" ).text( response.total_approvals );
			$parent.find( ".locked-status" ).removeClass( "hidden" );
			$parent.find( ".revision-request" ).remove();
			$parent.find( ".review-toggle" ).toggleClass( "btn-outline-secondary btn-outline-success" ).text( "Approved" );
		})
		.fail(function() {
			handleFormResponse( $parent.find( ".response" ), "An error has occured" );
			$btn.text( btn_text ).prop( "disabled", false );
		});
	});

	$( ".resolve-error" ).click( function(e) {
		$parent = $(this).parents( ".editor-item" );
		$alert = $(this).parents( ".alert" );
		$btn = $(this);
		btn_text = $btn.text();
		$btn.text( "..." ).prop( "disabled", true );
		e.preventDefault();
		var data = {
			"log_id": $alert.attr( "data-log-id" ),
		}
		$.post( "/editor/resolve", data, function( response ) {
			if( response.error ) {
				$btn.text( btn_text ).prop( "disabled", false );
				return;
			}
			$alert.slideUp();
		})
		.fail(function() {
			$btn.text( btn_text ).prop( "disabled", false );
		});
	});

	$( ".request-revision" ).click( function() {
		$parent = $(this).parents( ".editor-item" );
		$( "#suggest-revision-form" ).find( "input[name='project_id']" ).val( $parent.attr( "data-project-id" ) );
		$( "#suggest-revision-form" ).find( "input[name='content_id']" ).val( $parent.attr( "data-content-id" ) );
		$( "#suggest-revision-form" ).find( "textarea[name='comment']" ).val( "" );
		$( "#suggest-revision-form" ).modal( "show" );
	});

	$( ".user-search .search" ).keyup( function() {
		$widget = $(this).parents( ".user-search" );
		var query = encodeURIComponent( $(this).val().trim() );
		var is_admin = $(this)[0].hasAttribute( "data-is-admin" );
		var project_id = $(this).attr( "data-project-id" );
		if( query.length == 0 ) {
			return false;
		}
		$.getJSON( "/user/search/" + project_id + "/" + query, function( users ) {
			$widget.find( ".user-list" ).empty();
			$.each( users, function( key, user ) {
				btn_text = user.invite_only ? "Invite" : "Add";
				extra_attribute = user.invite_only ? 'data-invite-email="' + user.email + '"' : "";
				html = '<div class="custom-control custom-checkbox"><span class="d-flex align-items-center"><img src="' + user.avatar + '" class="avatar mr-2" /><span class="h6 mb-0" data-filter-by="text">' + user.heading + '</span><div class="dropdown ml-auto"><button class="btn btn-primary btn-sm text-light dropdown-toggle" type="button" data-toggle="dropdown">' + btn_text + '</button><div class="dropdown-menu"><a class="dropdown-item select-member" ' + extra_attribute + ' data-id="' + user.id + '" data-type="translator">Translator</a><a class="dropdown-item select-member" data-id="' + user.id + '" data-type="reviewer" ' + extra_attribute + '>Reviewer</a><a class="dropdown-item select-member ' + ( is_admin ? "" : "hidden" ) + '" data-id="' + user.id + '" data-type="manager" ' + extra_attribute + '>Manager</a></div></div></span></div>';
				$widget.find( ".user-list" ).append( html );
			});
		});
	});

	$( document ).on( "click", ".select-member", function() {
		$btn = $(this).parents( ".dropdown" ).find( ".btn" );
		var btn_text = $btn.text();
		$btn.text( "loading..." ).prop( "disabled", true );
		$modal = $(this).parents( ".modal-body" );
		var data = {
			"type": $(this).data( "type" ),
			"user_id": $(this).data( "id" ),
			"invite_email": $(this).data( "invite-email" ),
			"project_id": $(this).parents( ".modal" ).data( "project-id" ),
		}
		$.post( "/projects/add_member", data, function( response ) {
			$btn.text( btn_text ).prop( "disabled", false );
			if( response.error ) {
				handleFormResponse( $modal, response.error );
				return;
			}
			location.reload();
		})
		.fail(function() {
			handleFormResponse( $modal, "An error has occured" );
			$btn.text( btn_text ).prop( "disabled", false );
		});
	});

	$( ".confirm-dialog" ).click( function() {
		$message = $(this).data( "confirm-message" );
		return confirm( $message );
	});

	$( "form[data-loading-text]" ).on( "submit", function() {
		$btn = $(this).find( "button[type='submit']" );
		$btn.text( $(this).attr( "data-loading-text" ) );
		$btn.prop( "disabled", true );
	});

	$( ".auto-translate" ).click( function(e) {
		$parent = $(this).parents( ".editor-item" );
		$btn = $(this);
		btn_text = $btn.text();
		$btn.text( "loading..." ).prop( "disabled", true );
		e.preventDefault();
		var data = {
			"project_id": $parent.attr( "data-project-id" ),
			"content_id": $parent.attr( "data-content-id" ),
		}
		$.post( "/editor/translate", data, function( response ) {
			if( response.error ) {
				handleFormResponse( $parent.find( ".response" ), response.error );
				return;
			}
			$btn.text( btn_text ).prop( "disabled", false );
			if( response.translated_text.length > 0 ) {
				$parent.find( "textarea" ).val( response.translated_text );
				$parent.find( ".auto-translate" ).addClass( "hidden" );
			}
		})
		.fail(function() {
			handleFormResponse( $parent.find( ".response" ), "An error has occured" );
			$btn.text( btn_text ).prop( "disabled", false );
		});
	});

	$( ".editor-item textarea" ).on( "keyup input", function() {
		$parent = $(this).parents( ".editor-item" );
		var is_empty = $(this).val().length == 0;
		$parent.find( ".auto-translate" ).toggleClass( "hidden", ! is_empty );
	});

	$( ".restore-revision" ).click( function(e) {
		e.preventDefault();
		$response_region = $(this).parents( ".revision" );
		$btn = $(this);
		revision_id = $btn.attr( "data-id" );
		
		ajax( "/editor/restore/" + revision_id, null, $btn, $response_region );
	});

	$( ".skills-select" ).selectize( {
	    "create": true,
	});

});
