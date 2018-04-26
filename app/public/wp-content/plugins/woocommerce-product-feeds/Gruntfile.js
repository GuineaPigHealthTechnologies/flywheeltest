module.exports = function(grunt) {

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		makepot: {
			target: {
				options: {
					cwd: '',                          // Directory of files to internationalize.
					domainPath: 'languages/',         // Where to save the POT file.
					exclude: ['vendor'],                      // List of files or directories to ignore.
					include: [],                      // List of files or directories to include.
					mainFile: 'woocommerce-gpf.php',                     // Main project file.
					potComments: 'Copyright (C) 2017 Ademti Software Ltd.',                  // The copyright at the beginning of the POT file.
					potFilename: 'woocommerce_gpf.pot',                  // Name of the POT file.
					potHeaders: {
						poedit: false,                 // Includes common Poedit headers.
						'x-poedit-keywordslist': true, // Include a list of all possible gettext functions.
						'Language-Team': '"Ademti Software WooCommerce Support" <wcsupport@ademti-software.co.uk>',
						'Last-Translator': '"Ademti Software WooCommerce Support" <wcsupport@ademti-software.co.uk>',
						'Report-Msgid-Bugs-To': 'https://plugins.leewillis.co.uk/support\n'
					},                                // Headers to add to the generated POT file.
					processPot: null,                 // A callback function for manipulating the POT file.
					type: 'wp-plugin',                // Type of project (wp-plugin or wp-theme).
					updateTimestamp: true,            // Whether the POT-Creation-Date should be updated without other changes.
					updatePoFiles: false              // Whether to update PO files in the same directory as the POT file.
				}
			}
		}
	});

	grunt.loadNpmTasks('grunt-wp-i18n');

	// Default task(s).
	grunt.registerTask('default', ['makepot']);

};
