/* CV page behaviours.
 * .ov-print        : "Print or save as PDF" opens the browser print dialog.
 * .ov-contact-open : opens the contact pop-up (<dialog id="ov-contact">). If the dialog is not on the page,
 *                    the link is left alone and goes to its href (LinkedIn).
 */
( function () {
	var dialog = document.getElementById( 'ov-contact' );

	document.addEventListener( 'click', function ( event ) {
		if ( event.target.closest( '.ov-print a' ) ) {
			event.preventDefault();
			window.print();
			return;
		}

		if ( dialog && typeof dialog.showModal === 'function' && event.target.closest( '.ov-contact-open a' ) ) {
			event.preventDefault();
			dialog.showModal();
			return;
		}

		if ( dialog && ( event.target.closest( '.ov-dialog-close' ) || event.target === dialog ) ) {
			dialog.close(); // Close button, or a click on the backdrop.
		}
	} );
} )();
