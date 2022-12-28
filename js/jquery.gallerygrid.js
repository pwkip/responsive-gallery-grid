/*
    Gallery Grid jQuery Plugin
    (c) 2013 bdwm.be
    For any questions please email me at jules@bdwm.be
    Version: 1.3.1
*/

;(function($){
    
    $.fn.gallerygrid = function(settings){
        settings = $.extend({}, $.fn.gallerygrid.defaults, settings);
        $.fn.gallerygrid.settings = settings;
        
        var maxrowheight = settings.maxrowheight;
        var margin = settings.margin;
        var items = settings.items;
        var lastrowbehavior = settings.lastrowbehavior;
        
        this.each(function() {
            var $container = $(this);
            var ratios = []; // deprecated
            var $tiles = $();
            
            var to;

            //$container.imagesLoaded().done(function() {
                $tiles = $container.find(items);
                
                makeGrid($container, $tiles);
                
                // call custom functions after the initial grid is set up.
                settings.after_init();
                settings.after();
                
                $(window).resize(function(){
                    clearTimeout(to);
                    to = setTimeout(function(){
                        
                        // call custom functions before the scaling takes place
                        settings.before();
                        
                        makeGrid($container, $tiles);
                        
                        // call custom function every time teh grid is rescaled.
                        settings.after();
                    }, 200);
                });
                
            //});
        });
        
        function makeGrid($container, $tiles) {
            
            $tiles.css({'display':'block', 'float':'left', 'margin-bottom' : margin });
            $tiles.css({'margin-right' : margin, 'height':maxrowheight, position:'relative'}); // reset the margins.
            
            $container.width('100%');
            var containerwidth = Math.floor($container.width());
            
            var rows = [];
            var currentgroupwidth = 0;
            var $row = $();
            var newHeight;
            
            $.each($tiles, function(i,tile) {

                var $tile = $(tile);

                $tile.on('load', function() {
                    $(this).parent().children().css('background-color','transparent');
                });

                var width = Math.floor(maxrowheight * $tile.data('ratio'));

                $tile.width(width);

                var extraWidth = parseInt($tile.css("border-left-width")) + parseInt($tile.css("border-right-width")) +
                    parseInt($tile.css("marginLeft")) + parseInt($tile.css("marginRight")) +
                    parseInt($tile.css("paddingLeft")) + parseInt($tile.css("paddingRight"));

                //var extraWidth = $tile.outerWidth() - width;
                
                $row = $row.add(tile);
                
                currentgroupwidth += width; // the image width gets added to the current group
                containerwidth -= extraWidth; // the borders and padding get substracted from the container width
                                
                if (currentgroupwidth >= containerwidth) { 
                    
                    // Get the right height for all images, so the width nicely fits the container.
                    
                    var ratio2 = currentgroupwidth / containerwidth ;
                    
                    newHeight = Math.floor(maxrowheight/ratio2);
                                        
                    $row.height(newHeight);
                    
                    var totalWidth = 0;
                    $row.each(function(i) {
                        if ($row.length-1 == i) return; //skip last one
                        var realwidth = Math.floor($(this).data('ratio')*newHeight);
                        totalWidth += realwidth-margin;
                        $(this).width(realwidth);
                    });
                    
                    $tile.css({'margin-right' : 0, 'width': containerwidth - totalWidth});
                    
                    // reset everything for the next row.
                    $row = $();
                    currentgroupwidth = 0;
                    containerwidth = $container.width();

                } else {

                    containerwidth -= margin; // the margin gets substracted from the container width
                    
                    // Do some special stuff if it's the last image
                    if ($tiles.length-1 == i) {
                        // Last image of the grid should never have a right margin
                        $tile.css('margin-right',0);

                        // TODO: Make this dependend on settings.lastrowbehavior
                        // 1. last_row_same_height: make last row same height as previous one (good if all tiles are the same size)
                        // 2. force_justified: force last row to fill up remaining space (good if it's important to have no gap at the right bottom.)
                        // 3. center: center last row if it doesn't take up the remaining space.
                        // 4. align_right: align last row right if it doesn't take up remaining space.
                        // 5. optional_images: add optional images to include if last row doesn't take up all space.
                        //      -- needs extra setting: settings.optional_ids

                        console.log(lastrowbehavior);

                        if (lastrowbehavior == 'last_row_same_height') {

                            // Last row should have the same height as the previous row, to get prettier results, if all images have the same size :)
                            $row.height(newHeight);
                            $row.each(function(i) { // because we changed the row height, all new widths need to be calculated
                                var realwidth = Math.floor($(this).data('ratio')*newHeight);
                                totalWidth += realwidth-margin;
                                $(this).width(realwidth);
                            });

                        } else if (lastrowbehavior == 'force_justified') {

                            containerwidth += $row.size()*margin;

                            console.log($row.size()*margin);

                            var ratio2 = currentgroupwidth / containerwidth;

                            newHeight = Math.floor(maxrowheight/ratio2);

                            $row.height(newHeight);
                            $row.each(function(i) { // because we changed the row height, all new widths need to be calculated
                                var realwidth = Math.floor($(this).data('ratio')*newHeight);
                                totalWidth += realwidth-margin;
                                $(this).width(realwidth);
                            });

                            $tile.css({'width': (containerwidth - totalWidth + margin*$row.size())});

                        } else if (lastrowbehavior == 'normal') {
                            // do nothing special.
                        } else if (lastrowbehavior == 'center') {

                            containerwidth += $row.size()*margin;


                            $row.eq(0).parent().css('margin-left', (containerwidth/2)-(currentgroupwidth/2));
                        }

                    }
                }
            });
        } 
        return this;
    };
    
    /*  Default Settings  */
    $.fn.gallerygrid.defaults = {
        'maxrowheight' : 200,
        'margin' : 10,
        'before' : function() { },
        'after' : function() { },
        'after_init' : function() { },
        'items' : 'img'
    };
    $.fn.gallerygrid.settings = {};
})(jQuery);