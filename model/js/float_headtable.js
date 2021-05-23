var topFloat = 100;

(function(a) {
    a.fn.goToTop = function(d) {
        var e = a(window);
        var c = a(this);
        var b = (e.scrollTop() > d) ? true: false;
        if (b) {
            c.stop().show()
        } else {
            c.stop().hide()
        }
        return this
    };
    a.fn.headerFloat = function() {
        var b = function(c) {
            a(window).on("scroll resize", function() {
                var e = a(this).scrollTop();
                _top_ = c.position().top;
                if (e >topFloat) {
                    a("#floatTable").css({position: "absolute",top: e-160});
                    a("#floatTable").css("z-index", "1900");
                    a("#floatTable").css("background-color", "#F0F5F8");
                    a("#floatTable").show();
                } else {
                    a("#floatTable").css({
                        top: _top_
                    });
                    a("#floatTable").hide();
                }
            })
        }; 
        return a(this).each(function() {
            b(a(this))
        })
    }
})(jQuery);

jQuery(document).ready(function() {
    jQuery("#tr_float").headerFloat();
});