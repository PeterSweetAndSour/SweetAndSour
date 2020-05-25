		(function() {
    
            var initPhotoSwipeFromDOM = function(gallerySelector) {
    
                var parseThumbnailElements = function(el) {
                    var figureElements = el.querySelectorAll("figure"),
				
                        numNodes = figureElements.length,
                        items = [],
                        el,
                        childElements,
                        thumbnailEl,
                        size,
                        item,
						figure,
						figCaptionThumbnail,
						figCaptionFullSize,
						linkToPageWithPhoto,
						anchor,
						thumbnailImage;
    
                    for(var i = 0; i < numNodes; i++) {
                        figure = figureElements[i];
    
                        /* include only element nodes */
                        if(figure.nodeType !== 1) {
                          continue;
                        }
    
                        anchor = figure.querySelector("a.in-gallery");
                        if(!anchor) {
                          continue;
                        }

						thumbnailImage = anchor.querySelector("img");
   
                        size = anchor.dataset.size.split('x');
    
                        /* create slide object */
                        item = {
                            src: anchor.dataset.linkedImageSrc, /* anchor.getAttribute('href'),*/
                            w: parseInt(size[0], 10),
                            h: parseInt(size[1], 10),
                            author: anchor.dataset.dataAuthor
                        };
    
                        item.el = anchor; /* save link to element for getThumbBoundsFn */
						item.msrc = thumbnailImage.getAttribute('src'); /* thumbnail url */

						// Get the content for the caption which will be the contents of the two figcaptions 
						// PLUS a link to a standalone page with the photo.
						figCaptionThumbnail = figure.querySelector("figcaption.thumbnail");
						figCaptionFullSize = figure.querySelector("figcaption.fullsize");
						linkToPageWithPhoto = '<p class="linkToFullSizePhoto"><a href="' + anchor.getAttribute('href') + '" target="_blank">Printable page with this photo and caption</a></p>';
						item.title = "<p>"+ figCaptionThumbnail.innerHTML + "</p>" + figCaptionFullSize.innerHTML + linkToPageWithPhoto;
    
                        var mediumSrc = anchor.getAttribute('data-med');
                          if(mediumSrc) {
                            size = anchor.getAttribute('data-med-size').split('x');
                            /* "medium-sized" image */
                            item.m = {
                                  src: mediumSrc,
                                  w: parseInt(size[0], 10),
                                  h: parseInt(size[1], 10)
                            };
                          }
                          /* original image */
                          item.o = {
                              src: item.src,
                              w: item.w,
                              h: item.h
                          };
    
                        items.push(item);
                    }
    
                    return items;
                };
    
                /* find nearest parent element */
                var closest = function closest(el, fn) {
                    return el && ( fn(el) ? el : closest(el.parentNode, fn) );
                };
    
                var onThumbnailsClick = function(e) {
                    e = e || window.event;
                    var eTarget = e.target || e.srcElement; // img.thumbnail

                    if(!eTarget.parentNode.classList.contains("in-gallery")) {
                        return;
                    }

                    e.preventDefault ? e.preventDefault() : e.returnValue = false;
    
                    
    
                    var clickedAnchor = closest(eTarget, function(el) {
                        return el.tagName === 'A';
                    });
    
                    if(!clickedAnchor) {
                        return;
                    }
    
                    var clickedGallery = closest(eTarget, function(el) {
                        return el.classList.contains("content"); // was "photo-gallery"
                    });
    
                    var anchors = clickedGallery.querySelectorAll("figure > a.in-gallery"), // was ".container > .item > figure > a"
                        numChildNodes = anchors.length,
                        nodeIndex = 0,
                        index;
    
                    for (var i = 0; i < numChildNodes; i++) {
                        if(anchors[i].nodeType !== 1) { 
                            continue; 
                        }
    
                        if(anchors[i] === clickedAnchor) {
                            index = nodeIndex;
                            break;
                        }
                        nodeIndex++;
                    }
    
                    if(index >= 0) {
                        openPhotoSwipe( index, clickedGallery );
                    }
                    return false;
                };
    
                var photoswipeParseHash = function() {
                    var hash = window.location.hash.substring(1),
                    params = {};
    
                    if(hash.length < 5) { /* pid=1 */
                        return params;
                    }
    
                    var vars = hash.split('&');
                    for (var i = 0; i < vars.length; i++) {
                        if(!vars[i]) {
                            continue;
                        }
                        var pair = vars[i].split('=');  
                        if(pair.length < 2) {
                            continue;
                        }           
                        params[pair[0]] = pair[1];
                    }
    
                    if(params.gid) {
                        params.gid = parseInt(params.gid, 10);
                    }
    
                    return params;
                };
    
                var openPhotoSwipe = function(index, galleryElement, disableAnimation, fromURL) {
                    var pswpElement = document.querySelectorAll('.pswp')[0],
                        gallery,
                        options,
                        items;
    
                    items = parseThumbnailElements(galleryElement);
    
                    /* define options (if needed) */
                    options = {
                        galleryUID: galleryElement.getAttribute('data-pswp-uid'),
    
                        getThumbBoundsFn: function(index) {
                            /* See Options->getThumbBoundsFn section of docs for more info */
                            var thumbnail = items[index].el.children[0],
                                pageYScroll = window.pageYOffset || document.documentElement.scrollTop,
                                rect = thumbnail.getBoundingClientRect(); 
    
                            return {x:rect.left, y:rect.top + pageYScroll, w:rect.width};
                        },
                        allowLongCaptions: true,      /* NEW! */                 
                        barsSize: {top:44, bottom:60} /* Allow a little extra space at the bottom. */
                    };
    
    
                    if(fromURL) {
                        if(options.galleryPIDs) {
                            /* parse real index when custom PIDs are used 
                               https://photoswipe.com/documentation/faq.html#custom-pid-in-url */
                            for(var j = 0; j < items.length; j++) {
                                if(items[j].pid == index) {
                                    options.index = j;
                                    break;
                                }
                            }
                        } else {
                            options.index = parseInt(index, 10) - 1;
                        }
                    } else {
                        options.index = parseInt(index, 10);
                    }
    
                    /* exit if index not found */
                    if( isNaN(options.index) ) {
                        return;
                    }
    
    
   
                    /* Pass data to PhotoSwipe and initialize it */
                    gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
    
                    /* see: https://photoswipe.com/documentation/responsive-images.html */
                    var realViewportWidth,
                        useLargeImages = false,
                        firstResize = true,
                        imageSrcWillChange;
    
                    gallery.listen('beforeResize', function() {
    
                        var dpiRatio = window.devicePixelRatio ? window.devicePixelRatio : 1;
                        dpiRatio = Math.min(dpiRatio, 2.5);
                        realViewportWidth = gallery.viewportSize.x * dpiRatio;
    
    
                        if(realViewportWidth >= 1200 || (!gallery.likelyTouchDevice && realViewportWidth > 800) || screen.width > 1200 ) {
                            if(!useLargeImages) {
                                useLargeImages = true;
                                imageSrcWillChange = true;
                            }
                            
                        } else {
                            if(useLargeImages) {
                                useLargeImages = false;
                                imageSrcWillChange = true;
                            }
                        }
    
                        if(imageSrcWillChange && !firstResize) {
                            gallery.invalidateCurrItems();
                        }
    
                        if(firstResize) {
                            firstResize = false;
                        }
    
                        imageSrcWillChange = false;
    
                    });
    
                    gallery.listen('gettingData', function(index, item) {
                        /*if( useLargeImages ) { */
                            item.src = item.o.src;
                            item.w = item.o.w;
                            item.h = item.o.h;
                        /* } else {
                               item.src = item.m.src;
                               item.w = item.m.w;
                               item.h = item.m.h;
                           } */
                    });
    
                    gallery.init();
                };
    
                /* select all gallery elements */
                var galleryElements = document.querySelectorAll( gallerySelector );
                for(var i = 0, l = galleryElements.length; i < l; i++) {
                    galleryElements[i].setAttribute('data-pswp-uid', i+1);
                    galleryElements[i].onclick = onThumbnailsClick;
                }
    
                /* Parse URL and open gallery if it contains #&pid=3&gid=1 */
                var hashData = photoswipeParseHash();
                if(hashData.pid && hashData.gid) {
                    openPhotoSwipe( hashData.pid,  galleryElements[ hashData.gid - 1 ], true, true );
                }
            };
    
            initPhotoSwipeFromDOM('.content'); // was '.photo-gallery'
    
        })();
