const path = require('path');
var fs = require('fs');

// Extract multiple stylesheet using 'mini-css-extract-plugin'
const recursiveIssuer = (m) => {
	if (m.issuer) {
	  return recursiveIssuer(m.issuer);
	} else if (m.name) {
	  return m.name;
	} else {
	  return false;
	}
}

const copyFileSync = ( source, target ) =>{
	var targetFile = target;
	var sourceArray = (Array.isArray(source))? source: [source];

	if ( fs.existsSync( target ) ) {
		if ( fs.lstatSync( target ).isDirectory() ) {
			sourceArray.forEach(function (file) {
				targetFile = path.join( target, path.basename( file ) );
				fs.writeFileSync(targetFile, fs.readFileSync(file));
			})
		}
	}
	
}


const copyFolderRecursiveSync = ( source, target ) => {
    var files = [];

    //check if folder needs to be created or integrated
    var targetFolder = path.join( target, path.basename( source ) );
    if ( !fs.existsSync( targetFolder ) ) {
        fs.mkdirSync( targetFolder );
    }

    //copy
    if ( fs.lstatSync( source ).isDirectory() ) {
        files = fs.readdirSync( source );
        files.forEach( function ( file ) {
            var curSource = path.join( source, file );
            if ( fs.lstatSync( curSource ).isDirectory() ) {
                copyFolderRecursiveSync( curSource, targetFolder );
            } else {
                copyFileSync( curSource, targetFolder );
            }
        } );
    }
}

module.exports = {recursiveIssuer, copyFileSync, copyFolderRecursiveSync}