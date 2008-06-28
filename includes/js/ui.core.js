(function(C){C.ui={plugin:{add:function(E,F,H){var G=C.ui[E].prototype;for(var D in H){G.plugins[D]=G.plugins[D]||[];G.plugins[D].push([F,H[D]])}},call:function(D,F,E){var H=D.plugins[F];if(!H){return }for(var G=0;G<H.length;G++){if(D.options[H[G][0]]){H[G][1].apply(D.element,E)}}}},cssCache:{},css:function(D){if(C.ui.cssCache[D]){return C.ui.cssCache[D]}var E=C('<div class="ui-gen">').addClass(D).css({position:"absolute",top:"-5000px",left:"-5000px",display:"block"}).appendTo("body");C.ui.cssCache[D]=!!((!(/auto|default/).test(E.css("cursor"))||(/^[1-9]/).test(E.css("height"))||(/^[1-9]/).test(E.css("width"))||!(/none/).test(E.css("backgroundImage"))||!(/transparent|rgba\(0, 0, 0, 0\)/).test(E.css("backgroundColor"))));try{C("body").get(0).removeChild(E.get(0))}catch(F){}return C.ui.cssCache[D]},disableSelection:function(D){D.unselectable="on";D.onselectstart=function(){return false};if(D.style){D.style.MozUserSelect="none"}},enableSelection:function(D){D.unselectable="off";D.onselectstart=function(){return true};if(D.style){D.style.MozUserSelect=""}},hasScroll:function(G,E){var D=/top/.test(E||"top")?"scrollTop":"scrollLeft",F=false;if(G[D]>0){return true}G[D]=1;F=G[D]>0?true:false;G[D]=0;return F}};var B=C.fn.remove;C.fn.remove=function(){C("*",this).add(this).trigger("remove");return B.apply(this,arguments)};function A(E,F,G){var D=C[E][F].getter||[];D=(typeof D=="string"?D.split(/,?\s+/):D);return(C.inArray(G,D)!=-1)}C.widget=function(E,D){var F=E.split(".")[0];E=E.split(".")[1];C.fn[E]=function(J){var H=(typeof J=="string"),I=Array.prototype.slice.call(arguments,1);if(H&&A(F,E,J)){var G=C.data(this[0],E);return(G?G[J].apply(G,I):undefined)}return this.each(function(){var K=C.data(this,E);if(H&&K&&C.isFunction(K[J])){K[J].apply(K,I)}else{if(!H){C.data(this,E,new C[F][E](this,J))}}})};C[F][E]=function(I,H){var G=this;this.widgetName=E;this.widgetBaseClass=F+"-"+E;this.options=C.extend({},C.widget.defaults,C[F][E].defaults,H);this.element=C(I).bind("setData."+E,function(L,J,K){return G.setData(J,K)}).bind("getData."+E,function(K,J){return G.getData(J)}).bind("remove",function(){return G.destroy()});this.init()};C[F][E].prototype=C.extend({},C.widget.prototype,D)};C.widget.prototype={init:function(){},destroy:function(){this.element.removeData(this.widgetName)},getData:function(D){return this.options[D]},setData:function(D,E){this.options[D]=E;if(D=="disabled"){this.element[E?"addClass":"removeClass"](this.widgetBaseClass+"-disabled")}},enable:function(){this.setData("disabled",false)},disable:function(){this.setData("disabled",true)}};C.widget.defaults={disabled:false};C.ui.mouse={mouseInit:function(){var D=this;this.element.bind("mousedown."+this.widgetName,function(E){return D.mouseDown(E)});if(C.browser.msie){this._mouseUnselectable=this.element.attr("unselectable");this.element.attr("unselectable","on")}this.started=false},mouseDestroy:function(){this.element.unbind("."+this.widgetName);(C.browser.msie&&this.element.attr("unselectable",this._mouseUnselectable))},mouseDown:function(F){(this._mouseStarted&&this.mouseUp(F));this._mouseDownEvent=F;var E=this,G=(F.which==1),D=(typeof this.options.cancel=="string"?C(F.target).is(this.options.cancel):false);if(!G||D||!this.mouseCapture(F)){return true}this._mouseDelayMet=!this.options.delay;if(!this._mouseDelayMet){this._mouseDelayTimer=setTimeout(function(){E._mouseDelayMet=true},this.options.delay)}if(this.mouseDistanceMet(F)&&this.mouseDelayMet(F)){this._mouseStarted=(this.mouseStart(F)!==false);if(!this._mouseStarted){F.preventDefault();return true}}this._mouseMoveDelegate=function(H){return E.mouseMove(H)};this._mouseUpDelegate=function(H){return E.mouseUp(H)};C(document).bind("mousemove."+this.widgetName,this._mouseMoveDelegate).bind("mouseup."+this.widgetName,this._mouseUpDelegate);return false},mouseMove:function(D){if(C.browser.msie&&!D.button){return this.mouseUp(D)}if(this._mouseStarted){this.mouseDrag(D);return false}if(this.mouseDistanceMet(D)&&this.mouseDelayMet(D)){this._mouseStarted=(this.mouseStart(this._mouseDownEvent,D)!==false);(this._mouseStarted?this.mouseDrag(D):this.mouseUp(D))}return !this._mouseStarted},mouseUp:function(D){C(document).unbind("mousemove."+this.widgetName,this._mouseMoveDelegate).unbind("mouseup."+this.widgetName,this._mouseUpDelegate);if(this._mouseStarted){this._mouseStarted=false;this.mouseStop(D)}return false},mouseDistanceMet:function(D){return(Math.max(Math.abs(this._mouseDownEvent.pageX-D.pageX),Math.abs(this._mouseDownEvent.pageY-D.pageY))>=this.options.distance)},mouseDelayMet:function(D){return this._mouseDelayMet},mouseStart:function(D){},mouseDrag:function(D){},mouseStop:function(D){},mouseCapture:function(D){return true}};C.ui.mouse.defaults={cancel:null,distance:1,delay:0}})(jQuery)