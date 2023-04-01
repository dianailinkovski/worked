'use strict';

angular.module('AutoFontSize', [])
    .directive('autoFontSize', ['$window','$timeout', 
        function($window, $timeout) {

            return {
                template: '<div data-role="inner" ng-transclude></div>',
                transclude: true,
                link: {
                    post: function(scope, elem, attrs) {
                    
                        var providedOptions = scope.$eval(attrs.autoFontSize) || {};
                        var options = angular.extend({
                            shrink: true,
                            grow: true,
                            minSize: 1,
                            defaultSize: 30
                        }, providedOptions);
                    
                        var inner = angular.element(elem[0].querySelector('div[data-role]'));
                        
                        // on every scope.$digest, check if a resize is needed
                        scope.$watch(attrs.ngModel, waitShrinkOrGrow);
                        function waitShrinkOrGrow(){
                          $timeout(function(){shrinkOrGrow()},50)
                        }
                        function shrinkOrGrow() {
                            var i = 0;
                            // reset size after a change
                            inner[0].style.fontSize = options.defaultSize + 'px';
                            // deal with line-height and images
                            adjustLineHeightAndInlineImages();

                            if (fontTooBig() && options.shrink) {
                                while (fontTooBig() && i < 100 && fontSizeI() >= options.minSize) {
                                    setFontSize(fontSizeI() - 1);
                                    i = i + 1;
                                }
                            } else if (fontTooSmall() && options.grow) {
                                while (fontTooSmall() && i < 100) {
                                    setFontSize(fontSizeI() + 1);
                                    i = i + 1;
                                }
                            } else {
                                return;
                            }
                            
                            scope.$emit('auto-font-size:resized', {
                                fontSize: fontSizeI(),
                                elem: elem
                            });
                        }

                        function css(el, prop) {
                            if($window.getComputedStyle) return $window.getComputedStyle(el[0]).getPropertyValue(prop);
                        }
                        
                        function fontSizeI() {
                            var fontSize = css(inner, 'font-size');
                            return Number(fontSize.match(/\d+/)[0]);
                        }
                    
                        function setFontSize(size) {
                            inner[0].style.fontSize = size + 'px';
                            adjustLineHeightAndInlineImages();
                        }

                        function adjustLineHeightAndInlineImages() {
                            if (!fontSizeAdjusted()) { return; }
                            var size = fontSizeI();
                            inner[0].style.lineHeight = options.defaultSize + 'px';
                            var images = inner[0].querySelectorAll('img');
                            angular.forEach(images, function(img) {
                              img.style.height((size + 2) + 'px');
                            });
                        }

                        function fontSizeAdjusted() {
                            return !!inner[0].style.fontSize;
                        }
                    
                        function fontTooBig() {
                            return (inner[0].offsetWidth >= elem[0].offsetWidth - 20 || inner[0].offsetHeight >= elem[0].offsetHeight);
                        }
                    
                        function fontTooSmall() {
                            return (inner[0].offsetWidth < elem[0].offsetWidth || inner[0].offsetHeight < elem[0].offsetHeight);
                        }
                    
                    }
                }
            };
        }
    ]);