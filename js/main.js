/*
    Gallery Grid Main Script
    (c) 2013-2017 bdwm.be
    For any questions please email me at jules@bdwm.be
    Version: 2.2.1
*/

// rgg_params[0].effect = 'bubble';
// rgg_params[1].effect = 'bubble';
// rgg_params[1].effect = 'fade';
//
// rgg_params[0].scale_mode = 'simple' // fixed-amount, fixed-amount-horizontal, fixed-amount-vertical, scale,
// rgg_params[0].fixed_amount_horizontal = 20;


// stretch vertical with max 20px and contain proportions
// - scale_mode = advanced
// - vertical_stretch_px = 20
// - contain_proportions = yes

// stretch vertical with max 20px and stretch horizontally max 50px and contain proportions
// - scale_mode = advanced
// - vertical_stretch_px = 20
// - horizontal_stretch_px = 50
// - contain_proportions = yes

// stretch vertical with max 50% and do not contain proportions
// - scale_mode = advanced
// - vertical_stretch_percent = 50
// - contain_proportions = no

// simply scale the image-height with 10% and contain proportions
// - scale_mode = simple
// - scale = 1.1

var timeout;
var previous_row_resize_scale = 1;
var rgg_is_pro =  rgg_params.rgg_is_pro;

jQuery(document).ready(function($) {

    $grids = $('.rgg-imagegrid');

    pre_init(); // called only once per page

    jQuery(window).resize(function(){
        clearTimeout(timeout);
        timeout = setTimeout(function(){

            reinit_grids();

        }, 200);
    });

    init_grids(); // can be called multiple times, for example on resize


    function pre_init() {

        if (typeof $('.rgg-simplelightbox').simpleLightbox === 'function') {
            $('.rgg-simplelightbox').simpleLightbox({captionSelector: 'self'});
        }

        // add click events for image-above images (slick)
        $('.rgg-image-above').click(function() {

            $grid = $(this).closest('.rgg-imagegrid').eq(0);

            $image_above_container = $grid.siblings('.image-above-container').eq(0);

            //$('.image-above-container').eq(0).html('<img src="'+$(this).attr('href')+'">');
            $image_above_container.slick('slickGoTo',$(this).index());
            var container_top = $image_above_container.offset().top;
            var window_top = $(window).scrollTop();

            if (window_top > container_top) {
                $([document.documentElement, document.body]).animate({
                    scrollTop: container_top
                }, 0);
            }

            return false;
        });

        load_slick = false;
        for (var i in rgg_params) {
            // sanitize vars
            rgg_params[i].margin = parseInt(rgg_params[i].margin);
            // end sanitize vars
            if (rgg_params[i].lightbox == 'image-above') {
                load_slick = true;
            }
        }
        if (load_slick) {
            // image-above
            $('.image-above-container').slick({
                adaptiveHeight: true
            });
        }


    }

    function reinit_grids() {
        $('.rgg-img',$grids).stop().off('mouseenter mouseleave'); // stop animations and remove all event listeners on the images
        init_grids();
    }

    function init_grids() {

        $grids.each(function(i) {

            $grid = $(this);
            par = rgg_params[$grid.data('rgg-id')];
            par.scale_percent = par.scale*100;
            init_grid($grid,par);

        });
    }


    function init_grid($grid, par) {

        var containerwidth = $grid.width();

        var top = 0;
        var left = 0;
        var total_margins = -par.margin;

        var tiles = [];
        var lastrow = [];

        var $imgs = $('.rgg-img',$grid);

        $imgs.each(function(img_index) {

            $img = $(this);

            $img.css('background-image',"url('"+$img.data('src')+"')");

            var tile = new Tile($img,par);

            tile.top = top;
            tile.left = left;

            $img.on('mouseenter', {tile:tile},function(e) {
                e.data.tile.animate_in();
            });

            $img.on('mouseleave', {tile:tile},function(e) {
                e.data.tile.animate_out();
            });

            lastrow.push(tile);
            tiles.push(tile);

            // calculate left position for next tile
            left += tile.width;
            total_margins += par.margin + tile.extrawidth;


            var resize_scale = 1;
            var row_is_full = (left > (containerwidth-total_margins));
            var is_last_row = ($imgs.length-1 === img_index);
            var left_offset = 0;
            var margin = par.margin;

            if (row_is_full) {
                resize_scale = (containerwidth-total_margins)/left;
                previous_row_resize_scale = resize_scale;
            }

            if (is_last_row && !row_is_full) {

                if (par.lastrowbehavior === 'last_row_same_height') {
                    resize_scale = previous_row_resize_scale;
                } else if (rgg_is_pro) { // pro only
                    if (par.lastrowbehavior === 'force_justified') {
                        resize_scale = (containerwidth - total_margins) / left;
                    } else if (par.lastrowbehavior === 'center') {
                        resize_scale = previous_row_resize_scale;
                        left_offset = (containerwidth - total_margins - left * resize_scale) / 2;
                    } else if (par.lastrowbehavior === 'align_right') {
                        resize_scale = previous_row_resize_scale;
                        left_offset = (containerwidth - total_margins - left * resize_scale);
                    } else if (par.lastrowbehavior === 'hide') {
                        resize_scale = 0;
                        margin = 0;
                    }
                }
            }

            if (row_is_full || is_last_row) {
                var scaled_height = par.maxrowheight*resize_scale;

                for (var i in lastrow) {
                    lastrow[i].width = lastrow[i].width*resize_scale;
                    lastrow[i].left = left_offset + lastrow[i].left*resize_scale+(i*(par.margin+tile.extrawidth));
                    lastrow[i].height = scaled_height;
                }

                top += scaled_height + margin + tile.extraheight;
                left = 0;
                total_margins = -margin;
                lastrow = [];
            }
        });

        $grid.css('height',top);

        for (var i in tiles) {
            $imgs.eq(i).css({'left': tiles[i].left, 'top' : tiles[i].top, 'width': tiles[i].width, 'height' : tiles[i].height});
        }
    }

});

function Tile($img, par) {
    this.$img = $img;
    this.$caption = jQuery('.rgg-caption-container',$img).eq(0);
    this.par = par;

    this.ratio = $img.data('ratio');

    this.width = par.maxrowheight * this.ratio;
    this.extrawidth = parseInt($img.css('border-left-width'))+parseInt($img.css('border-right-width'));
    this.extraheight = parseInt($img.css('border-top-width'))+parseInt($img.css('border-bottom-width'));
    this.height = par.maxrowheight;
    this.top = 0;
    this.left = 0;

    if (rgg_is_pro) { // pro only
        this.init_captions_params();
    }

    this.init_animate_params();

}

Tile.prototype.animate_in = function() {
    if (this.is_animating_in) { // already animating in? - remove any future animations, and return false.
        this.$img.clearQueue();
        return false;
    } else if (this.is_animating_out) { // animating out? - remove any future animation and finish the out animation.
        this.$img.clearQueue();
        this.$img.finish();
    }
    if (rgg_is_pro && this.par.effect === 'fade') {
        this.fade();
    } if (rgg_is_pro && this.par.effect === 'zoom') {
        this.zoom();
    } else if (this.par.effect === 'bubble') {
        this.bubble();
    } else {
        this.dummy();
    }
};
Tile.prototype.animate_out = function() {
    if (this.is_animating_out) { // already animating out? - remove any future animations, and return false.
        this.$img.clearQueue();
        return false;
    }

    // is already animating in, just continue and queue the out animation to happen afterwards.

    if (rgg_is_pro && this.par.effect === 'fade') {
        this.unfade();
    } else if (rgg_is_pro && this.par.effect === 'zoom') {
        this.unzoom();
    } else if (this.par.effect === 'bubble') {
        this.unbubble();
    } else {
        this.undummy();
    }
};

Tile.prototype.animate_tile_in = function(css) {
    var tile = this;

    if (this.is_animating_in) return false;

    this.$img.animate(
        css,
        {
            duration: parseInt(this.par.intime),
            complete: function() {
                tile.animate_in_done();
            },
            start: function() {
                tile.$img.css({'box-shadow' : '0 1px 3px rgba(0,0,0,.5)'});
                tile.animate_in_start();
            },
            progress: function(animation, progress) {

                zindex = Math.round(10+progress*10);
                tile.$img.css({'z-index':zindex});
            }
        }
    );

    this.$caption.show();
};
Tile.prototype.animate_tile_out = function(css) {
    var tile = this;
    this.$img.animate(
        css,
        {
            duration: parseInt(this.par.outtime),
            complete: function() {
                tile.animate_out_done();
                tile.$img.css({
                    'box-shadow' : '0 0 0 rgba(0,0,0,.1)'
                });
            },
            start: function() {
                tile.animate_out_start();
            },
            progress: function(animation, progress) {
                zindex = Math.round(20-progress*10);
                tile.$img.css({'z-index':zindex});
            }
        }
    );
};

Tile.prototype.init_animate_params = function() {
    this.is_animating     = false;
    this.is_animating_in  = false;
    this.is_animating_out = false;
    this.is_animated_in   = false;
    this.is_animated_out  = false;  // is_animated_out
};
Tile.prototype.animate_in_done = function() {
    this.init_animate_params();
    this.is_animated_in   = true;
    this.$img.trigger('animate_in_done'); // trigger custom event
};
Tile.prototype.animate_out_done = function() {
    this.init_animate_params();
    this.is_animated_out  = true;
    this.$img.trigger('animate_out_done'); // trigger custom event
};
Tile.prototype.animate_in_start = function() {
    this.init_animate_params();
    this.is_animating     = true;
    this.is_animating_in  = true;
    this.$img.trigger('animate_in_start'); // trigger custom event
    this.$img.addClass('rgg-in');
    if (rgg_is_pro) { // pro only
        this.animate_caption_in();
    }
};
Tile.prototype.animate_out_start = function() {
    this.init_animate_params();
    this.is_animating     = true;
    this.is_animating_out = true;
    this.$img.trigger('animate_out_start'); // trigger custom event
    this.$img.removeClass('rgg-in');
    if (rgg_is_pro) { // pro only
        this.animate_caption_out();
    }
};

Tile.prototype.dummy = function() {
    this.animate_tile_in({});
}
Tile.prototype.undummy = function() {
    this.animate_tile_out({});
}

Tile.prototype.bubble = function() {

    num_px = 0;

    num_px = (this.height*this.par.scale-this.height)/2;
    var t_left = this.left - num_px*this.ratio;
    var t_top = this.top - num_px;
    var t_width = this.width*this.par.scale;
    var t_height = this.height*this.par.scale;

    // some dev code. save it for later:

    // num_px = this.width*this.par.scale-this.width;
    // if (this.ratio < 1) { // landscape -
    //     var t_left = this.left - num_px;
    //     var t_top = this.top - num_px/this.ratio;
    //     var t_width = this.width + num_px * 2;
    //     var t_height = this.height + num_px/this.ratio * 2;
    // } else { // portait |
    //     num_px = this.height*this.par.scale-this.height;
    //     var t_left = this.left - num_px*this.ratio;
    //     var t_top = this.top - num_px;
    //     var t_width = this.width + num_px*this.ratio * 2;
    //     var t_height = this.height + num_px * 2;
    // }


    if (num_px > 0) {
    // pop-out the image a tiny bit before the animation starts, to prevent flickering gap between images
        this.$img.css({
            'left' : this.left-.5,
            'top' : this.top-.5,
            'width' : this.width + 1,
            'height' :this.height + 1
        });
    }

    this.animate_tile_in({
        'left':t_left,
        'top':t_top,
        'width':t_width,
        'height':t_height
    });
};
Tile.prototype.unbubble = function() {
    this.animate_tile_out({
        'left':this.left,
        'top':this.top,
        'width':this.width,
        'height':this.height
    });
};

// PRO ONLY

Tile.prototype.fade = function() {
    this.animate_tile_in({'opacity': .5});
};
Tile.prototype.unfade = function() {
    this.animate_tile_out({'opacity': 1});
};

Tile.prototype.zoom = function() {
    this.animate_tile_in({'background-size': this.par.scale_percent+'%'});
};
Tile.prototype.unzoom = function() {
    this.animate_tile_out({'background-size': '102%'});
};

var overlay_animation = {
    from_css : {'bottom' : 0, 'transform' : 'translate(0,100%)'},
    to_css   : {'bottom' : 0, 'transform' : 'translate(0,0)'}
};

var fade_animation = {
    from_css : { 'opacity' : '0'},
    to_css : { 'opacity' : '1' }
}

Tile.prototype.init_captions_params = function() {

    var cap = this.par.captions;
    var cap = this.par.captions;

    this.$captioncontainer = jQuery('.rgg-caption-container', this.$img);
    this.$caption = jQuery('.rgg-caption', this.$img);
    this.$innercaption = jQuery('.rgg-inner-caption', this.$img);
    this.is_caption_hide_on_hover = (cap == 'overlay-hover-hide');
    this.is_caption_show_on_hover = (cap == 'overlay-hover-show');
    this.is_caption_show_always = (cap == 'overlay');
    this.is_caption_hide_always = (cap == 'off' || cap == 'title' || cap == 'custom');

    var fx = this.par.captions_effect;

    this.is_caption_fx_slide = (fx == 'slide_up' || fx == 'slide_down');
    this.is_caption_fx_fade = (fx == 'fade');
    this.is_caption_fx_none = (fx == 'none');

    this.caption_intime = this.par.captions_intime;
    this.caption_outtime = this.par.captions_outtime;

    this.caption_from_css = overlay_animation.from_css;
    this.caption_to_css = overlay_animation.from_css;

    if (this.is_caption_show_on_hover || this.is_caption_hide_always) {
        if (this.is_caption_fx_slide || this.is_caption_fx_none) {
            this.caption_from_css = overlay_animation.from_css;
            this.caption_to_css = overlay_animation.to_css;
        } else if (this.is_caption_fx_fade) {
            this.caption_from_css = fade_animation.from_css;
            this.caption_to_css = fade_animation.to_css;
        }
    } else if (this.is_caption_hide_on_hover || this.is_caption_show_always) {
        if (this.is_caption_fx_slide || this.is_caption_fx_none) {
            this.caption_from_css = overlay_animation.to_css;
            this.caption_to_css = overlay_animation.from_css;
        } else if (this.is_caption_fx_fade) {
            this.caption_from_css = fade_animation.to_css;
            this.caption_to_css = fade_animation.from_css;
        }
    }

    this.$caption.css(this.caption_from_css);

    if (!this.is_caption_fx_none) {
        this.caption_from_css['transition'] = 'all '+this.caption_outtime+'ms';
        this.caption_to_css['transition'] = 'all '+this.caption_intime+'ms';
    }

}

Tile.prototype.animate_caption_in = function() {
    if (this.is_caption_hide_always || this.is_caption_show_always) return;
    this.$caption.css(this.caption_to_css);
}

Tile.prototype.animate_caption_out = function() {
    if (this.is_caption_hide_always || this.is_caption_show_always) return;
    this.$caption.css(this.caption_from_css);
}
