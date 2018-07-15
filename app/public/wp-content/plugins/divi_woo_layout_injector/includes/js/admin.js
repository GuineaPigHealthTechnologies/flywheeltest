jQuery(document).ready(function ($) {
    db_wli_init_colourpicker();
});

function db_wli_init_colourpicker() {
    jQuery(".wli_colorpicker").spectrum({
        //color: "#ECC",
        showInput: true,
        //className: "full-spectrum",
        showInitial: true,
        showPalette: true,
        showSelectionPalette: true,
        maxSelectionSize: 10,
        preferredFormat: "hex",
        localStorageKey: "spectrum.demo"
    });
}