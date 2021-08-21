const mix      = require('wp-mix');
const fsExtra  = require("fs-extra");
const path     = require("path");
const cliColor = require("cli-color");
const emojic   = require("emojic");
const min      = Mix.inProduction() ? '.min' : '';

const PackageFile = JSON.parse(File.find(Mix.paths.root('package.json')).read());

if (process.env.NODE_ENV === 'package') {

    mix.then(function () {

        let bundledir = path.basename(path.resolve(__dirname));
        let copyfrom  = path.resolve(__dirname);
        let copyto    = path.resolve(`${bundledir}`);
        // Select All file then paste on list
        let list      = `assets
fonts
images
includes
languages
templates
package.json
README.txt
webpack.mix.js
wpml-config.xml
woo-variation-gallery.php`;

        let includes = list.split("\n");
        fsExtra.ensureDir(copyto, function (err) {
            if (err) return console.error(err)

            includes.map(include => {

                fsExtra.copy(`${copyfrom}/${include}`, `${copyto}/${include}`, function (err) {
                    if (err) return console.error(err)

                    console.log(cliColor.white(`=> ${emojic.smiley}  ${include} copied...`));

                    /*if (include == 'assets') {
                     // Just Removed SCSS Dir
                     fsExtra.removeSync(`${copyto}/${include}/scss`);
                     }*/
                })
            });

            console.log(cliColor.white(`=> ${emojic.whiteCheckMark}  Build directory created`));
        })
    });

    return;
}

if (Mix.inProduction()) {
    mix.generatePot({
        package   : 'Additional Variation Images Gallery for WooCommerce',
        bugReport : 'https://github.com/EmranAhmed/woo-variation-gallery/issues',
        src       : '**/*.php',
        domain    : 'woo-variation-gallery',
        destFile  : `languages/woo-variation-gallery.pot`
    });
}

mix.banner({
    banner : "Additional Variation Images Gallery for WooCommerce v1.2.0 \n\nAuthor: Emran Ahmed ( emran.bd.08@gmail.com ) \nDate: " + new Date().toLocaleString() + "\nReleased under the GPLv3 license."
});

mix.notification({
    title : 'Gallery',
    // contentImage : Mix.paths.root('images/logo.png')
});

/*if (!Mix.inProduction()) {
    mix.sourceMaps();
}*/

mix.babel(`node_modules/slick-carousel/slick/slick${min}.js`, `assets/js/slick${min}.js`);

// mix.babel(`src/js/single-product.js`, `assets/js/single-product${min}.js`);
mix.js(`src/js/frontend.js`, `assets/js/frontend${min}.js`);
mix.copy(`src/js/bluebird.js`, `assets/js/bluebird${min}.js`);


mix.sass(`src/scss/slick.scss`, `assets/css/slick${min}.css`);
mix.sass(`src/scss/frontend.scss`, `assets/css/frontend${min}.css`);
mix.sass(`src/scss/theme-support.scss`, `assets/css/theme-support${min}.css`);

mix.js(`src/js/backend.js`, `assets/js/admin${min}.js`);
mix.babel(`src/js/gwp-backbone-modal.js`, `assets/js/gwp-backbone-modal${min}.js`);
// mix.babel(`src/js/meta-boxes-product-variation.js`, `assets/js/meta-boxes-product-variation${min}.js`);
mix.js(`src/js/gwp-admin.js`, `assets/js/gwp-admin${min}.js`);
mix.sass(`src/scss/backend.scss`, `assets/css/admin${min}.css`);
mix.sass(`src/scss/gwp-admin.scss`, `assets/css/gwp-admin${min}.css`);
mix.sass(`src/scss/gwp-admin-notice.scss`, `assets/css/gwp-admin-notice${min}.css`);
