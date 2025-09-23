jQuery(document).ready(function() {
    // insert toggle button
    jQuery(".advanced-sidebar-nav").each(function() {
        jQuery(".menu-item-has-children", jQuery(this)).each(function() {
            // insert toggle button
            jQuery("> a", jQuery(this)).append(
                '<span class="advanced-sidebar-nav-toggle"></span>'
            );

            // add indent
            var depth = jQuery(this).parents(".menu-item-has-children").length;
            jQuery("ul li a", jQuery(this)).attr(
                "style",
                "padding-left:" + (depth + 2) * 20 + "px !important"
            );

            // open nav menu toggle
            if (jQuery("ul", jQuery(this)).css("display") == "block") {
                jQuery(".advanced-sidebar-nav-toggle", jQuery(this)).addClass(
                    "advanced-sidebar-nav-toggle-open"
                );
            }
        });
    });

    // menu toggle
    jQuery(".advanced-sidebar-nav").on(
        "click",
        ".advanced-sidebar-nav-toggle",
        function(e) {
            e.preventDefault();
            jQuery(this).toggleClass("advanced-sidebar-nav-toggle-open");
            jQuery(this).hasClass("advanced-sidebar-nav-toggle-open")
                ? jQuery(this)
                      .parent("a")
                      .addClass("advanced-sidebar-nav-menu-open")
                      .siblings("ul")
                      .slideDown(300)
                : jQuery(this)
                      .parent("a")
                      .removeClass("advanced-sidebar-nav-menu-open")
                      .siblings("ul")
                      .slideUp(300);
        }
    );
});
