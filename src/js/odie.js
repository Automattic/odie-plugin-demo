document.addEventListener( 'DOMContentLoaded', function () {
	const button = document.createElement( 'button' );
	button.textContent = 'Show Odie';

	button.addEventListener( 'click', function () {
		// console.log( document.getElementById( 'jetpack-odie-root' ) );
		if ( window.Odie && window.Odie.render ) {
			console.log( document.getElementById( 'jetpack-odie-root' ) );
			window.Odie.render( {
				...window.JetpackXhrParams,
				domNode: document.getElementById( 'jetpack-odie-root' ),
				// eslint-disable-next-line no-console
				onLoaded: () => console.log( 'Chat is loaded.' ),
			} );
		}
	} );

	const widgetContainer = document.getElementById( 'jetpack-odie-root' );
	widgetContainer.appendChild( button );
} );
