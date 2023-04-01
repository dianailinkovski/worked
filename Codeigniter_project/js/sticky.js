// jquery.tablesorter.pack.js
eval(function(p,a,c,k,e,d){e=function(c){return(c<a?"":e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--){d[e(c)]=k[c]||e(c)}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('(8($){$.1V({I:D 8(){7 C=[],1d=[];k.2v={28:"3Z",2b:"48",29:"49",2P:"4a",2D:"4b",1I:1E,1w:"25",C:{},1d:[],1m:{S:["2c","2L"]},x:{},2u:K,2Q:13,u:[],1y:[],1l:"2O",J:K};8 17(s,d){1h(s+","+(D T().1e()-d.1e())+"4c")}k.17=17;8 1h(s){q(1u 1P!="2S"&&1u 1P.J!="2S"){1P.1h(s)}N{2V(s)}}8 1T(6,$x){q(6.f.J){7 1O=""}7 G=6.L[0].G;q(6.L[0].G[0]){7 Z=[],12=G[0].12,l=12.w;y(7 i=0;i<l;i++){7 p=K;q($.1o&&($($x[i]).16()&&$($x[i]).16().1g)){p=1K($($x[i]).16().1g)}N q((6.f.x[i]&&6.f.x[i].1g)){p=1K(6.f.x[i].1g)}q(!p){p=22(6.f,12[i])}q(6.f.J){1O+="1D:"+i+" 1B:"+p.B+"\\n"}Z.R(p)}}q(6.f.J){1h(1O)}m Z};8 22(f,V){7 l=C.w;y(7 i=1;i<l;i++){q(C[i].O($.1M(1Q(f,V)))){m C[i]}}m C[0]}8 1K(1x){7 l=C.w;y(7 i=0;i<l;i++){q(C[i].B.14()==1x.14()){m C[i]}}m K}8 1U(6){q(6.f.J){7 23=D T()}7 19=(6.L[0]&&6.L[0].G.w)||0,2r=(6.L[0].G[0]&&6.L[0].G[0].12.w)||0,C=6.f.C,F={1c:[],1j:[]};y(7 i=0;i<19;++i){7 c=6.L[0].G[i],1p=[];F.1c.R($(c));y(7 j=0;j<2r;++j){1p.R(C[j].H(1Q(6.f,c.12[j]),6,c.12[j]))}1p.R(i);F.1j.R(1p);1p=1E};q(6.f.J){17("2W F y "+19+" G:",23)}m F};8 1Q(f,V){q(!V)m"";7 t="";q(f.1w=="25"){q(V.1L[0]&&V.1L[0].2X()){t=V.1L[0].1S}N{t=V.1S}}N{q(1u(f.1w)=="8"){t=f.1w(V)}N{t=$(V).1i()}}m t}8 1G(6,F){q(6.f.J){7 2U=D T()}7 c=F,r=c.1c,n=c.1j,19=n.w,1J=(n[0].w-1),2h=$(6.L[0]),G=[];y(7 i=0;i<19;i++){G.R(r[n[i][1J]]);q(!6.f.20){7 o=r[n[i][1J]];7 l=o.w;y(7 j=0;j<l;j++){2h[0].32(o[j])}}}q(6.f.20){6.f.20(6,G)}G=1E;q(6.f.J){17("4d 6:",2U)}1z(6)};8 2x(6){q(6.f.J){7 1f=D T()}7 1o=($.1o)?13:K,27=[];y(7 i=0;i<6.1t.G.w;i++){27[i]=0};$1r=$("35 37",6);$1r.1s(8(1F){k.1a=0;k.1D=1F;k.18=2B(6.f.2P);q(2d(k)||2e(6,1F))k.1C=13;q(!k.1C){$(k).1q(6.f.28)}6.f.1y[1F]=k});q(6.f.J){17("3a x:",1f);1h($1r)}m $1r};8 2K(6,G,1c){7 1k=[],r=6.1t.G,c=r[1c].12;y(7 i=0;i<c.w;i++){7 11=c[i];q(11.41>1){1k=1k.3b(2K(6,3c,1c++))}N{q(6.1t.w==1||(11.3e>1||!r[1c+1])){1k.R(11)}}}m 1k};8 2d(11){q(($.1o)&&($(11).16().1g===K)){m 13};m K}8 2e(6,i){q((6.f.x[i])&&(6.f.x[i].1g===K)){m 13};m K}8 1z(6){7 c=6.f.1d;7 l=c.w;y(7 i=0;i<l;i++){1Z(c[i]).H(6)}}8 1Z(1x){7 l=1d.w;y(7 i=0;i<l;i++){q(1d[i].B.14()==1x.14()){m 1d[i]}}};8 2B(v){q(1u(v)!="3i"){i=(v.14()=="3j")?1:0}N{i=(v==(0||1))?v:0}m i}8 2E(v,a){7 l=a.w;y(7 i=0;i<l;i++){q(a[i][0]==v){m 13}}m K}8 1W(6,$x,Z,S){$x.1H(S[0]).1H(S[1]);7 h=[];$x.1s(8(3L){q(!k.1C){h[k.1D]=$(k)}});7 l=Z.w;y(7 i=0;i<l;i++){h[Z[i][0]].1q(S[Z[i][1]])}}8 2z(6,$x){7 c=6.f;q(c.2u){7 1v=$(\'<1v>\');$("2j:3l 3H",6.L[0]).1s(8(){1v.3G($(\'<3o>\').S(\'2l\',$(k).2l()))});$(6).3r(1v)}}8 2M(6,u){7 c=6.f,l=u.w;y(7 i=0;i<l;i++){7 s=u[i],o=c.1y[s[0]];o.1a=s[1];o.1a++}}8 1Y(6,u,F){q(6.f.J){7 2m=D T()}7 Y="7 2k = 8(a,b) {",l=u.w;y(7 i=0;i<l;i++){7 c=u[i][0];7 18=u[i][1];7 s=(2s(6.f.C,c)=="1i")?((18==0)?"2n":"2o"):((18==0)?"2p":"2q");7 e="e"+i;Y+="7 "+e+" = "+s+"(a["+c+"],b["+c+"]); ";Y+="q("+e+") { m "+e+"; } ";Y+="N { "}7 1N=F.1j[0].w-1;Y+="m a["+1N+"]-b["+1N+"];";y(7 i=0;i<l;i++){Y+="}; "}Y+="m 0; ";Y+="}; ";3t(Y);F.1j.3u(2k);q(6.f.J){17("3w 3x "+u.3z()+" 3A 3B "+18+" 1f:",2m)}m F};8 2n(a,b){m((a<b)?-1:((a>b)?1:0))};8 2o(a,b){m((b<a)?-1:((b>a)?1:0))};8 2p(a,b){m a-b};8 2q(a,b){m b-a};8 2s(C,i){m C[i].Q};k.2f=8(2w){m k.1s(8(){q(!k.1t||!k.L)m;7 $k,$3E,$x,F,f,3F=0,3I;k.f={};f=$.1V(k.f,$.I.2v,2w);$k=$(k);$x=2x(k);k.f.C=1T(k,$x);F=1U(k);7 1X=[f.29,f.2b];2z(k);$x.3K(8(e){7 19=($k[0].L[0]&&$k[0].L[0].G.w)||0;q(!k.1C&&19>0){7 $11=$(k);7 i=k.1D;k.18=k.1a++%2;q(!e[f.2D]){f.u=[];q(f.1I!=1E){7 a=f.1I;y(7 j=0;j<a.w;j++){f.u.R(a[j])}}f.u.R([i,k.18])}N{q(2E(i,f.u)){y(7 j=0;j<f.u.w;j++){7 s=f.u[j],o=f.1y[s[0]];q(s[0]==i){o.1a=s[1];o.1a++;s[1]=o.1a%2}}}N{f.u.R([i,k.18])}};$k.1R("3S");1W($k[0],$x,f.u,1X);3T(8(){1G($k[0],1Y($k[0],f.u,F));$k.1R("3U")},0);m K}}).3V(8(){q(f.2Q){k.3X=8(){m K};m K}});$k.1n("3Y",8(){k.f.C=1T(k,$x);F=1U(k)}).1n("2J",8(e,Z){f.u=Z;7 u=f.u;2M(k,u);1W(k,$x,u,1X);1G(k,1Y(k,u,F))}).1n("44",8(){1G(k,F)}).1n("45",8(e,B){1Z(B).H(k)}).1n("47",8(){1z(k)});q($.1o&&($(k).16()&&$(k).16().2N)){f.u=$(k).16().2N}q(f.u.w>0){$k.1R("2J",[f.u])}1z(k)})};k.P=8(1B){7 l=C.w,a=13;y(7 i=0;i<l;i++){q(C[i].B.14()==1B.B.14()){a=K}}q(a){C.R(1B)}};k.2R=8(21){1d.R(21)};k.U=8(s){7 i=2Y(s);m(26(i))?0:i};k.2Z=8(s){7 i=30(s);m(26(i))?0:i};k.33=8(6){q($.34.36){8 2I(){38(k.2a)k.39(k.2a)}2I.3d(6.L[0])}N{6.L[0].1S=""}}}});$.3f.1V({I:$.I.2f});7 M=$.I;M.P({B:"1i",O:8(s){m 13},H:8(s){m $.1M(s.14())},Q:"1i"});M.P({B:"3k",O:8(s){m/^\\d+$/.15(s)},H:8(s){m $.I.U(s)},Q:"W"});M.P({B:"3m",O:8(s){m/^[3n£$3pÇ¨?.]/.15(s)},H:8(s){m $.I.U(s.X(D 1b(/[^0-9.]/g),""))},Q:"W"});M.P({B:"3s",O:8(s){m s.2G(D 1b(/^(\\+|-)?[0-9]+\\.[0-9]+((E|e)(\\+|-)?[0-9]+)?$/))},H:8(s){m $.I.U(s.X(D 1b(/,/),""))},Q:"W"});M.P({B:"3v",O:8(s){m/^\\d{2,3}[\\.]\\d{2,3}[\\.]\\d{2,3}[\\.]\\d{2,3}$/.15(s)},H:8(s){7 a=s.3y("."),r="",l=a.w;y(7 i=0;i<l;i++){7 1A=a[i];q(1A.w==2){r+="0"+1A}N{r+=1A}}m $.I.U(r)},Q:"W"});M.P({B:"3J",O:8(s){m/^(2y?|2A|2C):\\/\\/$/.15(s)},H:8(s){m 2T.1M(s.X(D 1b(/(2y?|2A|2C):\\/\\//),\'\'))},Q:"1i"});M.P({B:"3P",O:8(s){m/^\\d{4}[\\/-]\\d{1,2}[\\/-]\\d{1,2}$/.15(s)},H:8(s){m $.I.U((s!="")?D T(s.X(D 1b(/-/g),"/")).1e():"0")},Q:"W"});M.P({B:"3Q",O:8(s){m/^\\d{1,3}%$/.15(s)},H:8(s){m $.I.U(s.X(D 1b(/%/g),""))},Q:"W"});M.P({B:"3R",O:8(s){m s.2G(D 1b(/^[A-3W-z]{3,10}\\.? [0-9]{1,2}, ([0-9]{4}|\'?[0-9]{2}) (([0-2]?[0-9]:[0-5][0-9])|([0-1]?[0-9]:[0-5][0-9]\\s(40|42)))$/))},H:8(s){m $.I.U(D T(s).1e())},Q:"W"});M.P({B:"43",O:8(s){m/\\d{1,2}[\\/\\-]\\d{1,2}[\\/\\-]\\d{2,4}/.15(s)},H:8(s,6){7 c=6.f;s=s.X(/\\-/g,"/");q(c.1l=="2O"){s=s.X(/(\\d{1,2})[\\/\\-](\\d{1,2})[\\/\\-](\\d{4})/,"$3/$1/$2")}N q(c.1l=="46"){s=s.X(/(\\d{1,2})[\\/\\-](\\d{1,2})[\\/\\-](\\d{4})/,"$3/$2/$1")}N q(c.1l=="2t/24/2g"||c.1l=="2t-24-2g"){s=s.X(/(\\d{1,2})[\\/\\-](\\d{1,2})[\\/\\-](\\d{2})/,"$1/$2/$3")}m $.I.U(D T(s).1e())},Q:"W"});M.P({B:"1f",O:8(s){m/^(([0-2]?[0-9]:[0-5][0-9])|([0-1]?[0-9]:[0-5][0-9]\\s(3g|3h)))$/.15(s)},H:8(s){m $.I.U(D T("3q/2i/2i "+s).1e())},Q:"W"});M.P({B:"3D",O:8(s){m K},H:8(s,6,11){7 c=6.f,p=(!c.2F)?\'3M\':c.2F;m $(11).16()[p]},Q:"W"});M.2R({B:"31",H:8(6){q(6.f.J){7 1f=D T()}$("2j:3C",6.L[0]).2H(\':2c\').1H(6.f.1m.S[1]).1q(6.f.1m.S[0]).3O().2H(\':2L\').1H(6.f.1m.S[0]).1q(6.f.1m.S[1]);q(6.f.J){$.I.17("3N 4e 21",1f)}}})})(2T);',62,263,'||||||table|var|function|||||||config|||||this||return||||if||||sortList||length|headers|for|||id|parsers|new||cache|rows|format|tablesorter|debug|false|tBodies|ts|else|is|addParser|type|push|css|Date|formatFloat|node|numeric|replace|dynamicExp|list||cell|cells|true|toLowerCase|test|data|benchmark|order|totalRows|count|RegExp|row|widgets|getTime|time|sorter|log|text|normalized|arr|dateFormat|widgetZebra|bind|meta|cols|addClass|tableHeaders|each|tHead|typeof|colgroup|textExtraction|name|headerList|applyWidget|item|parser|sortDisabled|column|null|index|appendToTable|removeClass|sortForce|checkCell|getParserById|childNodes|trim|orgOrderCol|parsersDebug|console|getElementText|trigger|innerHTML|buildParserCache|buildCache|extend|setHeadersCss|sortCSS|multisort|getWidgetById|appender|widget|detectParserForColumn|cacheTime|mm|simple|isNaN|tableHeadersRows|cssHeader|cssDesc|firstChild|cssAsc|even|checkHeaderMetadata|checkHeaderOptions|construct|yy|tableBody|01|tr|sortWrapper|width|sortTime|sortText|sortTextDesc|sortNumeric|sortNumericDesc|totalCells|getCachedSortType|dd|widthFixed|defaults|settings|buildHeaders|https|fixColumnWidth|ftp|formatSortingOrder|file|sortMultiSortKey|isValueInArray|parserMetadataName|match|filter|empty|sorton|checkCellColSpan|odd|updateHeaderSortCount|sortlist|us|sortInitialOrder|cancelSelection|addWidget|undefined|jQuery|appendTime|alert|Building|hasChildNodes|parseFloat|formatInt|parseInt|zebra|appendChild|clearTableBody|browser|thead|msie|th|while|removeChild|Built|concat|headerArr|apply|rowSpan|fn|am|pm|Number|desc|integer|first|currency|¬|col|‚|2000|prepend|floating|eval|sort|ipAddress|Sorting|on|split|toString|and|dir|visible|metadata|document|shiftDown|append|td|sortOrder|url|click|offset|sortValue|Applying|end|isoDate|percent|usLongDate|sortStart|setTimeout|sortEnd|mousedown|Za|onselectstart|update|header|AM|colSpan|PM|shortDate|appendCache|applyWidgetId|uk|applyWidgets|headerSortUp|headerSortDown|asc|shiftKey|ms|Rebuilt|Zebra'.split('|'),0,{}));
// jquery.tablehighlighter.js
(function($) {

	$.fx.speeds.slow = 3000; // 'slow' now means 3 seconds
	
	/**
	 * Highlights a column in a table when clicked
	 */
	$.fn.click_to_highlight = function() {
		var hclass = 'datahighlight';
		var headers = $(this).children('thead').children('tr').children('th.header');
		var rows = $(this).children('tbody').children();
		$(this).after('<div id="jqxtooltip"></div>');
		$('#jqxtooltip').jqxTooltip({
			width: 100
		});

		// add the highlighter option to header
		headers.mouseenter(function() {
			$(this).append('<span class="highlightIcon"></span>');
			var header = $(this);


			// add the tooltip to the span button
			$('#jqxtooltip').jqxTooltip('add', $('span.highlightIcon'), 'Toggle Highlight');

			// on click, highlight/unhighlight the column
			$('span.highlightIcon').click(function(event) {
				event.stopPropagation();
				var remove = header.attr('data-selected') == 1;

				headers.removeAttr('data-selected');

				rows.children().removeClass(hclass);

				if ( ! remove) {
					header.attr('data-selected', '1');
					var index = header.prevAll().length;
					rows.find('td:nth-child(' + (index + 1) + ')').addClass(hclass);
				}
			});


		});

		// remove the highlighter option from header
		headers.mouseleave(function() {
			$('span.highlightIcon').remove();
		});
	};

})(jQuery);


$(document).ready(function () {
	// Override IE Google Toolbar Form stylings
	if(window.attachEvent) window.attachEvent("onload",setListeners);

	$('#toggle-user-menu-button').click(function() {
		
		$('#user-profile-menu').toggleClass('open');
		
	});
	
	$('.dropdown').on({
		mouseenter: function() {
			if ($(this).attr('id') !== 'bookmarks' || ! $('#bookmarks').hasClass('empty')) {
				$(this).addClass('active');
				$(this).find('.dropdownMenu').addClass('show');
			}
		},
		mouseleave: function() {
			$(this).removeClass('active');
			$(this).find('.dropdownMenu').removeClass('show');
		}
	});

	$('.selectMenu').on({
		click: function() {
			$(this).addClass('active');
			$(this).find('.selectMenuDropdown').addClass('show');
		},
		mouseleave: function() {
			$(this).removeClass('active');
			$(this).find('.selectMenuDropdown').removeClass('show');
		}
	});
	$('.selectMenu input[type=checkbox]').on('click', function() {
		name = $(this).attr('class');
		if ($(this).attr('value') != 'all') {
			$('.all_'+name).attr('checked', false);
		}
		else if ($(this).attr('value') == 'all') {
			name = name.replace('all_', '');
			if ($(this).not(':checked')) {
				$('.'+name).attr('checked', false);
			}
		}
	});

	$('#qaWidget .button').on({
		mouseenter: function() {
			$(this).addClass('hover');
		},
		mouseleave: function() {
			$(this).removeClass('hover');
		}
	});

	if ($('.tabs').length) {
		$('.tabs').tabs();
	};


	$('.customSelect select').each(function(){
		var title = $(this).attr('title');
		if( $('option:selected', this).val() != ''  ) title = $('option:selected',this).text();
        
        var afterHtml = '<div class="inputL"></div><div class="select">' + title + '</div>';
        if ( $(this).hasClass('hidden') || $(this).attr("disabled") == 'disabled' ) {
            
        } else {
            afterHtml += '<div class="selectArrow"></div>';
        }
        
		$(this)
			.css({'z-index':10,'opacity':0,'-khtml-appearance':'none'})
			.after(afterHtml)
			.change(function(){
				val = $('option:selected',this).text();
				$(this).siblings('.select').text(val);
			})
	});


	//Input pre-fill
	var ptest = document.createElement('input');
	if ("placeholder" in ptest) {
		$('*[placeholder]').focusin( function() {
			$(this).removeClass("prefill");
		}).focusout(function() {
			value = $(this).attr('value');
			if (value == '') {
				$(this).addClass("prefill");
			}
		});
	}
	else {
		$('*[placeholder]').each(function() {
			value = $(this).attr('placeholder');
			$(this).attr('value', value);
		});
		$('*[placeholder]').focusin( function() {
			placeholder = $(this).attr('placeholder');
			value = $(this).attr('value');
			if (value == placeholder) {
				$(this).attr('value', '');
			}
			$(this).removeClass("prefill");
		}).focusout(function() {
			placeholder = $(this).attr('placeholder');
			value = $(this).attr('value');
			if (value == '') {
				$(this).attr('value', placeholder);
				$(this).addClass("prefill");
			}
		});
	}

	// Sortable Tables
	if ($('.sortable').length) {
		$('.sortable').each(function(){
			$(this).tablesorter();
			if ($(this).click_to_highlight != undefined)
				$(this).click_to_highlight();
		});
	}

	// Switch Brands
	$('#switchBrand').on('change', function(e) {
		this.form.submit();
		e.preventDefault();
	});

	// Bookmarks
	$('#bookmarkPage').on('click', bookmark_dialog);
	$('#bookmarkForm').on('submit', add_bookmark);
	$('#bookmarks')
		.on('click', '.bookmarkDelete', remove_bookmark)
		.on('mouseenter', 'a', show_remove_bookmark)
		.on('mouseleave', 'a', hide_remove_bookmark)

	// Save options
	//$('#save_product_report').on('click', save_product_report);
	$('#save_report').on('click', save_report_popup);
	$('#sendEmail').on('click', email_report_info);
    $('#pdf_export').off('click');
	$('#pdf_export').click(function() {
        submitExportForm('reports/pdfExport/');
	});
    $('#excel_export').off('click');
	$('#excel_export').click(function() {
        submitExportForm('reports/excel');
	});
	$('.savedReportDelete').on('click', function(){
        saved_report_delete_popup($(this).attr('data-id'));
	});

	// Autocomplete
	$('.noRecord').click(function(){
		$(this).remove();
	});

	autocomplete_emails('input[name="email_addresses"]');

	// Pricing Over Time
	$('#date_to_a').on('click', function(){
		$('#date_to').focus();
	});
	$('#date_from_a').on('click', function(){
		$('#date_from').focus();
	});

	// WhoIs
	$('#whoisSwitch').on('change', function(e){
		this.form.submit();
		e.preventDefault();
	});

	// Catalog
	$('.container').on('change', '.bulkActions', function(){
		bulkActionsMethod($(this).val());
		$('.bulkActions').val('');
	});
    
	$('.container').on('change', '.showArchived', function(){
		var show = $(this).attr('checked') ? 1 : 0;
        CatGrid.setUrl(catUrl+'&is_archived='+show);
        CatGrid.refresh();
        CatGrid.setUrl(catUrl);
	});

	// Catalog Tabs
	$('#catalogListTab').on('click.ajax', function(e){
		load_catalog_list_tab();
	});
	$('#promotionalPricingTab').on('click.ajax', function(e){
		load_promotional_pricing_tab();
	});
	$('#productGroupsTab').on('click.ajax', function(e){
		load_product_groups_tab();
	});
	$('#competitorAnalysisTab').on('click.ajax', function(e){
        load_competitor_analysis_tab();
	});
	$('#productLookupTab').on('click.ajax', function(e){
		load_product_lookup_tab();
	});
});

/********************************************************************
 *                           SV Library                             *
 *                                                                  *
 *            Common functions used throughout the site             *
 *                                                                  *
 ********************************************************************/

(function($){
	$.fn.hideRemove = function(){
		$(this).hide('slow', function(){$(this).remove()});
	}
})(jQuery);

(function($){
	$.fn.place = function(ref, addMethod){
		switch (addMethod){
			case 'insertAfter':
				$(this).insertAfter(ref);
				break;
			case 'insertBefore':
				$(this).insertBefore(ref);
				break;
			case 'appendTo':
			default:
				$(this).appendTo(ref);
		}
	}
})(jQuery)

/**
 * Retrieve a cookie by its key
 */
function getCookie(name){
	var dc = document.cookie;
	var cname = name + "=";
	if (dc.length > 0) {
		begin = dc.indexOf(cname);
		if (begin != -1) {
			begin += cname.length;
			end = dc.indexOf(";",begin);
			if (end == -1) end = dc.length;
			return unescape(dc.substring(begin, end));
		}
	}
	return null;
}

/**
 * Store a value in a cookie by a key
 */
function setCookie(name, value){
	var exp = new Date(); //set new date object
	exp.setTime(exp.getTime() + (1000 * 60 * 60 * 24 * 30));
	expires = exp;
	document.cookie = name + "=" + escape(value) + "; path=/" + ((expires == null) ? "" : "; expires=" + expires.toGMTString());
}

/**
 * Returns the meridiem of a Date object
 *
 * @return String
 */
Date.prototype.getMeridiem = function() {
	return this.getHours() < 12 ? 'am' : 'pm';
};

/**
 * Returns the date in the format MM/DD/YY h:mm a/pm
 *
 * @return String
 */
Date.prototype.formatLong = function() {
	var curr_hour = this.getHours() % 12;
	if (curr_hour == 0) curr_hour = 12;
	var curr_min = this.getMinutes() + "";
	if (curr_min.length == 1) curr_min = "0" + curr_min;
	var curr_year = this.getFullYear()-2000;
	return (this.getMonth()+1)+'/'+this.getDate()+'/'+curr_year+' '+curr_hour+':'+curr_min+this.getMeridiem();
};

/**
 * Remove an array value or group of values by index
 *
 * @param number from
 * @param number to
 */
Array.prototype.remove = function(from, to) {
  var rest = this.slice((to || from) + 1 || this.length);
  this.length = from < 0 ? this.length + from : from;
  return this.push.apply(this, rest);
};

/**
 * Round a number to a set number of decimals
 *
 * @param number num
 * @param number numOfDec
 *
 * @return number
 */
Math.roundToDec = function(num, numOfDec){
	var pow10s = Math.pow(10, numOfDec || 0);
	return (numOfDec) ? Math.round(pow10s * num) / pow10s : num;
};

/**
 * Perform a callback when enter is pressed on an element
 */
function onEnter(sel, callback, args){
	$(sel).keypress(function(e){
		if(e.which == 13){
			e.preventDefault();
			if ($.isFunction(callback))
				callback(args);
		}
	});
}

/**
 * Check whether a variable has a non empty value
 */
function empty(param){
	return ! Boolean(param);
}

/**
 * Show a dialog with the given HTML
 */
function newDialog(sel, html, width, height){
	$(sel).html(html);
	showDialog(sel, width, height);
}

/**
 * Show a simple modal window with a message and an OK Button
 */
function sv_alert(html) {
	var dialog = '<div class="modalWindow dialog alert-dialog">' + html + '</div>';
	$(dialog).dialog({
		modal: true,
		buttons: {
			'OK': function() {
				$(this).dialog('close');
			}
		}
	});
    
    return dialog;
}

/**
 * Show a simple modal window with a message and an OK and a cancel button.
 * The OK button will execute a callback function.
 */
function sv_confirm(html, callback) {
	var dialog = '<div class="modalWindow dialog confirm-dialoog">' + html + '</div>';
	$(dialog).dialog({
		modal: true,
		buttons: {
			'Cancel': function() {
				$(this).dialog('close');
			},
			'OK': callback
		}
	});
    
    return dialog;
}


/**
 * Create a default dialog
 * Default options are:
 *
 * resizable: false,
 * modal: true,
 * closeOnEscape: false,
 * width: 600,
 * height: 400
 *
 * @param sel String
 * @param options Object
 */
function createDefaultDialog(sel, options) {
	var defaults = {
		resizable: false,
		modal: true,
		closeOnEscape: false
	};
	if(options.length <= 0)
		options = {};

	// override the defaults with specified options
	for (var opt in options)
		defaults[opt] = options[opt];

	$(sel).dialog(defaults);
}

/**
 * Show a dialog
 */
function showDialog(sel, width, height, options) {
	if ( ! $.isPlainObject(options))
		options = {};

	if (width == 'auto' || width > 0)
		options.width = width;
	if (height === 'auto' || height > 0)
		options.height = height;

	createDefaultDialog(sel, options);

	if( ! $(sel).dialog('isOpen'))
		$(sel).dialog('open');
}

/**
 * jQuery Extended svlib for
 * setting errors and success
 */
var sv = function(selector) {
	sv.$el = $(selector);

	return sv;
};
sv.set_error = function(msg, show) {
	this.$el.removeClass().addClass('error').html(msg).show().delay(1000).fadeOut('show', function(){$(this).html('')});
}
sv.set_success = function(msg, show) {
	this.$el.removeClass().addClass('success').html(msg).show().delay(1000).fadeOut('show', function(){$(this).html('')});
}

/**
 * SVGrid
 *
 * A default grid object
 *
 * @param sel String A valid selector to put the grid inside
 * @param url String A url that will return json data
 */
function SVGrid(sel, url){
	var grid = this;

	this.sel = sel;
	this.url = url;

	this.renderer = function(){
		return '<div style="margin-left: 10px; margin-top: 5px;"></div>';
	};
	this.rendered = function(el){
		$(el).jqxCheckBox({ theme: grid.theme, width: 16, height: 16, animationShowDelay: 0, animationHideDelay: 0 });
		columnCheckBox = $(el);
		var rowscount = $(grid.sel).jqxGrid('getrows').length;

		$(el).on('change', function (event){
			var IndexesArray = new Array();
			for (var i = 0; i < rowscount; i++){
				var visibleIndex = $(grid.sel).jqxGrid('getrowvisibleindex', i);
				IndexesArray.push({ bound: i, visible: visibleIndex });
			};
			var paginginformation = $(grid.sel).jqxGrid('getpaginginformation');
			var pagenum = paginginformation.pagenum;
			var pagesize = paginginformation.pagesize;
			var pageArray = new Array();

			for (var j = 0; j < rowscount; j++){
				if (IndexesArray[j].visible >= pagenum * pagesize && IndexesArray[j].visible < (pagenum + 1) * pagesize){
					pageArray.push(IndexesArray[j].bound);
					$(grid.sel).jqxGrid('setcellvalue', IndexesArray[j].bound, 'check', event.args.checked);
				};
			};
			var checked = event.args.checked;

			if (checked == null || grid.updatingCheckState) return;
			$(grid.sel).jqxGrid('beginupdate');
			if (checked == true){
				$(grid.sel).jqxGrid({ selectedrowindexes: pageArray });
			}else if (checked == false){
				$(grid.sel).jqxGrid('clearselection');
			};
			$(grid.sel).jqxGrid('endupdate');
		});

		$(grid.sel).on("pagechanged", function (event){
			var IndexesArray = new Array();
			for (var i = 0; i < rowscount; i++){
				var visibleIndex = $(grid.sel).jqxGrid('getrowvisibleindex', i);
				IndexesArray.push({ bound: i, visible: visibleIndex });
			};
			var args = event.args;
			var pagenum = args.pagenum;
			var pagesize = args.pagesize;
			var selectedNumber = 0;
			for (var k = 0; k < rowscount; k++){
				if (IndexesArray[k].visible >= pagenum * pagesize && IndexesArray[k].visible < (pagenum + 1) * pagesize){
					var value = $(grid.sel).jqxGrid('getcellvalue', IndexesArray[k].bound, 'check');
					if (value == true){
						$(grid.sel).jqxGrid('selectrow', IndexesArray[k].bound);
						selectedNumber += 1;
					}else{
						$(grid.sel).jqxGrid('unselectrow', IndexesArray[k].bound);
					};
				};
			};
			if (selectedNumber == pagesize){
				$(el).jqxCheckBox('check');
			}else if (selectedNumber == 0){
				$(el).jqxCheckBox('uncheck');
			};
		});
	};
	this.ready = function(){
		var func = $.data($(grid.sel)[0]).jqxGrid.instance._mousewheelfunc;
		/*
		if (window.removeEventListener){
			if ($.browser.mozilla){
				$(grid.sel)[0].removeEventListener('DOMMouseScroll', func, false);
			}else{
				$(grid.sel)[0].removeEventListener('mousewheel', func, false);
			}
			return false;
		}
		else $(grid.sel).unbind('mousewheel');
		*/
	};
};
SVGrid.prototype.source;
SVGrid.prototype.sCookieName = 'bps';
SVGrid.prototype.bpS = 10;
SVGrid.prototype.updatingCheckState = false;
SVGrid.prototype.setSource = function(datafields, opts){
	this.source = {
		datatype: 'json',
		datafields: datafields,
		id: 'id',
		url: this.url,
		root: 'data'
	};

	for (var opt in opts){
		this.source[opt] = opts[opt];
	}

	this.url = this.source.url;
};
SVGrid.prototype.setUrl = function(url){
	this.url = url;
	this.source.url = url;
};
SVGrid.prototype.setTheme = function(theme){
	this.theme = theme;
}
SVGrid.prototype.destroy = function(con){
	this.jqxGrid('destroy');
	$(con).html('<div id="' + this.sel + '"></div>');
}
SVGrid.prototype.reset = function(){
	this.destroy();
	var bpS = getCookie(this.sCookieName);
	this.bpS = parseInt(bpS);
};
SVGrid.prototype.defaultEvents = function(){
	var that = this;
	$(this.sel).on('bindingcomplete', function(event){
            $(this).jqxGrid('sortby', 'title', 'asc');
            var total_rows = $(this).jqxGrid('getrows').length;
            if(total_rows > 100)
            {
                //$(this).jqxGrid({rowList:['ALL',30,50,100,200]});
                $(".ui-pg-selbox").append($('<option></option>').val(total_rows).html('All'));
                //$(this).jqxGrid({pagesizeoptions: ['5','10', '20', '30','50','100', '' + total_rows]});
            }
            else
            {
               // $(this).jqxGrid({pagesizeoptions: ['5','10', '20', '30','50','100']});
            }
            //$(this).jqxGrid('updatepagerdetails');
	});

	$(this.sel).on('pagesizechanged', function(event){
		var args = event.args;
		var pagesize = args.pagesize;
		setCookie(that.sCookieName, pagesize) ;
	});

	$(this.sel).on('columnresized', function(event){
		var column = event.args.column;
		var newwidth = event.args.newwidth
		setCookie(column.datafield, newwidth) ;
	});
};
SVGrid.prototype.create = function(){};
SVGrid.prototype.bindEvents = function(){};
SVGrid.prototype.init = function(){
	this.reset();
	this.create();
	this.bindEvents();
};
SVGrid.prototype.reload = function(){
	this.clear();
	var dataAdapter = new $.jqx.dataAdapter(this.source);
	this.jqxGrid({ source: dataAdapter });
};
SVGrid.prototype.refresh = function(){
	this.jqxGrid('updatebounddata');
}
SVGrid.prototype.clear = function(){
    this.jqxGrid('clearselection');
    var rowscount = this.jqxGrid('getdatainformation').rowscount;
    this.jqxGrid('beginupdate');
    for (var i = 0; i < rowscount; i++) {
        this.jqxGrid('setcellvalue', i, 'check', false);
    }
    this.jqxGrid('endupdate');
};
SVGrid.prototype.filter = function(keyword){
	var tmp = this.url;
	var url = $.trim(keyword) ? this.url+'&keyword='+keyword : this.url;
    this.setUrl(url);
    this.refresh();
	this.setUrl(tmp);
}
SVGrid.prototype.getSelect = function(callback){
	var sel = this.getSelectedRowIndexes();
	if (typeof callback == 'function')
		return callback(sel);
}
SVGrid.prototype.getSelectedIds = function(){
	var $grid = $(this.sel);
	var cb = function(sel) {
        var ids = [];
        if ( sel != null && sel !== undefined ) {
            for(var i=0;i<sel.length;i++){
                var curRow = $grid.jqxGrid('getrowdata', sel[i]);
                ids.push(curRow.uid)
            }
        }
		return ids;
	}

	return this.getSelect(cb);
}
SVGrid.prototype.getSelectedRows = function(){
	var $grid = $(this.sel);
	var cb = function(sel) {
		var rows = [];
        if ( sel != null && sel !== undefined ) {
            for(var i=0;i<sel.length;i++){
                rows.push($grid.jqxGrid('getrowdata', sel[i]));
            }
        }
		return rows;
	}

	return this.getSelect(cb);
}
SVGrid.prototype.getSelectedRowIndexes = function(){
	return this.jqxGrid('getselectedrowindexes');
}
SVGrid.prototype.getRow = function(index){
	return this.jqxGrid('getrowdata', index)
}
SVGrid.prototype.jqxGrid = function(){
	return $(this.sel).jqxGrid.apply($(this.sel), arguments);
}
// End SVGrid

/**
 * SVListBox
 *
 * A default listbox object
 *
 * @param sel String A valid selector to put the grid inside
 * @param url String A url that will return json data
 */
function SVListBox(sel, url){
	this.sel = sel;
	this.url = url;
	this.data = {};
	this.selectedItem = null;
}

SVListBox.prototype.init = function(opts){
	this.set_source(opts);
	this.create();
	this.bind_events();
}
SVListBox.prototype.create = function(opts){}
SVListBox.prototype.reload = function(opts){
	this.init(opts);
};
SVListBox.prototype.destroy = function(){
	$(this.sel).jqxListBox('destroy');
}
SVListBox.prototype.set_source = function(opts){
	this.set_datafields();

	this.source = {
		datatype: 'json',
		datafields: this.datafields,
		id: 'id',
		url: this.url,
		root: 'data'
	};

	for (var opt in opts){
		this.source[opt] = opts[opt];
	}

	this.dataAdapter = new $.jqx.dataAdapter(this.source);
}
SVListBox.prototype.on_select = function(){
	var that = this;
	$(this.sel).on('select', function(e){
		if (e.args) {
			var item = e.args.item;
			if (item) {
				that.selectedItem = item;
			}
		}
	});
}
SVListBox.prototype.default_events = function(){
	this.on_select();
}
SVListBox.prototype.set_datafields = function(){}
SVListBox.prototype.bind_events = function(){
	this.default_events();
}
// End SVListBox

/**
 * A default object for autocompleting SV product titles and UPCs.
 * this.get() returns an object that can be passed to JQuery's autocomplete function.
 *
 * @param url (String) - The URL to post to view AJAX
 * @param comp (boolean) - Whether to return competitor products or store products
 * @param upc (boolean) - Whether completing upc or title
 */
function SVProductAutoComplete(url, comp, upc){
	this.url = url;
	this.comp = comp;
	this.upc = upc;
	this.minLength = 1;
	this.deferRequestBy = 0;
	this.appendTo = null;
}
SVProductAutoComplete.prototype.source_success = function(response){
	var that = this;
	return function(items){
		if( ! items || items.length == 0) {
			if(typeof auto_complete_no_result != 'undefined')
				auto_complete_no_result('upc_code');

			return false;
		}
		response($.map(items, that.response()));
	}
}
SVProductAutoComplete.prototype.source = function(){
	var that = this;
	return function(request, response){
		$.post(that.url, {term: request.term, comp: that.comp}, that.source_success(response), 'json');
	}
}
SVProductAutoComplete.prototype.response = function(){
	if (this.upc)
		return function(item){
			return {
				label: item.upc_code + ' - ' + item.title,
				value: item.upc_code,
				title: item.title,
				id: item.id,
				source: item
			};
		}
	else
		return function(item){
			return {
				label: item.title,
				value: item.title,
				upc: item.upc_code,
				id: item.id,
				source: item
			};
		}
}
SVProductAutoComplete.prototype.select = function(){};
SVProductAutoComplete.prototype.get = function(){
	var autocomplete = {
		source: this.source(),
		minLength: this.minLength,
		deferRequestBy: this.deferRequestBy,
		select: this.select,
		appendTo: this.appendTo
	}

	return autocomplete;
}

function autocomplete_emails(sel){
	$(sel).autocomplete({
		source: teamMembers,
		select: function( event, ui ){
			var f = $(this.form);
			var s = ui.item;
			var c = '<div class="reportEmails" id="rptE_'+s.id+'"><input type="hidden" name="email_addresses[]" value="'+s.email+'">'+s.email+'<span class="jsLink" onclick="xRptEmail(this);"><img src="/images/icons/16/69.png" alt="Remove" class="imgIcon"></span></div>';
			if($(f.attr('id')+' .email_container #rptE_'+s.id).length == 0) $('#'+f.attr('id')+' .email_container').append(c);
			$(this).val('');
			return false;
		}
	});
}

function validateEmail(field) {
	var regex=/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i;
	return (regex.test(field)) ? true : false;
}


/********************************************************************
 *                          Reports                                 *
 *                                                                  *
 *       Functions for pricing over time and violations             *
 *                                                                  *
 ********************************************************************/

function makeText(textData){
	var finalText = textData.toUpperCase();
	finalText = finalText.replace('_PRICE','');
	finalText = finalText.replace('IS_','');
	finalText = finalText.replace('PRICE_','');
	finalText = finalText.replace('FLOOR','MAP');
	if(finalText != 'MAP')
		finalText = finalText.toLowerCase().charAt(0).toUpperCase()+finalText.slice(1).toLowerCase();
	return finalText;
}

function showNextBlock(b){
	var el;
	switch (b){
		case 'byproduct':
			el = 'product_container';
			break;
		case 'filters':
			el = 'filter_result';
			break;
		case 'bydate':
			el = 'date_range';
			break;
		case 'show_comparison':
			el = 'show_comparison_container';
			break;
	}
	if($('#'+el).hasClass('hidden')) $('#'+el).removeClass('hidden');
}

function is_date_range_valid()
{
	if ($('#date_to').length == 1)
	{
		if (Date.parse($('#date_from').val()) > Date.parse($('#date_to').val())){
			sv_alert("Start date cannot be after end date.");
			return false;
		}
		/*
		else if ($('#date_to').val() == '')
		{
			sv_alert("Please select an end date.");
			return false;
		}
		else if ($('#date_from').val() == '')
		{
			sv_alert("Please select a start date.");
			return false;
		}
		*/
	}
	
	return true;
}

function clearDates(){
	$('#date_from').val('Start');
	$('#date_to').val('Stop');
}

function submitReportForm(){
	var dateFrom = $('#date_from').val();
	var dateTo  = $('#date_to').val();

	if (dateFrom != 'Start' || dateTo != 'Stop')
		if (dateFrom == 'Start' || dateTo == 'Stop')
			return sv_alert('<div class="error"> Please Select Both Dates </div>');

	$('#reportfrm').submit();

	return true;
}

var AddProductButton = function(ref, addMethod){
	this.element = $('<span class="product-plus button jsLink clear"><span class="buttonCornerL"><\/span><span class="buttonR"><img src="'+image_url+'icons/16/13.png" alt="Add">Add another Product<\/span><\/span>');
	this.element.place(ref, addMethod);
}

var RemoveProductButton = function(ref, addMethod){
	this.element = $('<span class="product-minus button jsLink clear"><span class="buttonCornerL"><\/span><span class="buttonR"><img src="'+image_url+'icons/minus.png" alt="Remove">Remove this Product<\/span><\/span>');
	this.element.place(ref, addMethod);
}

var Product = function(ref, is_competition, addMethod){
	// Create our element and append it to the parent element
	//var clear = $('<div class="clear"></div>').appendTo(parent);
	var element = $('<div class="autoCompleteContainer"><input type="text" class="product product_name ui-autocomplete-input" name="product_name[]" value=""><input type="hidden" name="products[]" value=""><\/div>');
	new RemoveProductButton(element);
	element.place(ref, addMethod);
	var source_url_params = '';
	if (is_competition)
		source_url_params += "competition";

	// Bind autocomplete for just the text element
	element.find('input[type=text]').autocomplete({
		source: function( request, response ){
			$.ajax({
				url: base_url+"schedule/get_products_names/"+request.term+"/"+source_url_params,
				dataType: "json",
				data: {},
				success: function(items){
					if(!items || items.length == 0){
						return false;
					}
					self.auto_item = null;
					response($.map( items, function( item ){
						return {
							label: item.title,
							value: item.title,
							Id: item.id,
							url: item.url
						}
					}));
				}
			});
		},
		minLength: 1,
		select: function( event, ui ){
			var item = ui.item;
			element.find('input[type=hidden]').attr('value', item.Id);
			$('.product-plus').remove();
			new AddProductButton(element);
		}
	});

	this.element = element;
};//end Product


/********************************************************************
 *                    Charts and Graphs                             *
 *                                                                  *
 *            Functions for drawing the report graphs               *
 *                                                                  *
 ********************************************************************/

// Override IE Google Toolbar Form stylings
function setListeners(){
    inputList = document.getElementsByTagName("INPUT");
    for(i=0;i<inputList.length;i++){
        inputList[i].attachEvent("onpropertychange",restoreStyles);
        inputList[i].style.backgroundColor = "";
    }
    selectList = document.getElementsByTagName("SELECT");
    for(i=0;i<selectList.length;i++){
        selectList[i].attachEvent("onpropertychange",restoreStyles);
        selectList[i].style.backgroundColor = "";
    }
}

function restoreStyles(){
    if(event.srcElement.style.backgroundColor != "")
        event.srcElement.style.backgroundColor = "";
}

function merchantsGauge(num, w, h) {
	google.setOnLoadCallback(function(){ drawMerchantsGauge(num, w, h) });
}

function drawMerchantsGauge(num, w, h) {
	// set the max and min
	var max = 500;
	var min = 0;
	if (isNaN(num) || num < 0)
		num = 0;
	else if (num > max)
		max = num;

	// calculate the animation so that it
	// lasts the same amount of time for any number
	var meter_duration = 1500; // 3 second animation
	var meter_period = 80;
	var meter_step = Math.ceil(num * meter_period / meter_duration);

	// set the gauges look
	var gf = Math.floor(max * .55);
	var gt = Math.floor(max * .75);
	var yt = Math.floor(max * .90);

	var options = {
		width: w, height: h,
		min: min, max: max,
		redFrom: yt, redTo: max,
		yellowFrom: gt, yellowTo: yt,
		greenFrom: gf, greenTo: gt,
		minorTicks: 5,
		animation: {
			duration: 1500, easing: 'out'
		}
	};

	// create the gauge
	var data = google.visualization.arrayToDataTable([
		['Label', 'Total Merchants'],
		['Merchants', 0]
	]);
	var gauge = new google.visualization.Gauge(
		document.getElementById('total_merchants_gauge')
	);
	gauge.draw(data, options);

	// animate
	var meter = 0;
	var decel = 0;
	var meter_interval = setInterval(function() {
		if (meter >= num)
			clearInterval(meter_interval);
		data.setValue(0, 1, meter);
		gauge.draw(data, options);
		if (decel == 1) {
			if (meter > (num * .75)) {
				meter_step = Math.ceil(meter_step * .25);
				decel++;
			}
		}
		else if (decel == 0) {
			if (meter > (num * .5)) {
				meter_step = Math.ceil(meter_step * .50);
				decel++;
			}
		}
		meter = Math.min(meter + meter_step, num);
	}, meter_period);
}

function drawChart(){
    if ( globalData.googleData == undefined ) return;
    
	if(globalData.googleData.length == 1){
		$('#default_loader').hide();
		$('#repChartContainer').hide();
	}else{
		if(globalData.type!='scatter'){
			chartdata = google.visualization.arrayToDataTable(globalData.googleData);
		}
		options = {
			title: '',
			pointSize:2,
			legend: {
				position: 'none'
			}
		};
		if(globalData.y_title != undefined){
			options.vAxis = {
				title:globalData.y_title,
				titleTextStyle:{
					color:'black',
					fontStyle:'none',
					fontWeight:'bold',
					italic:false
				}
			};
			if(globalData.type=='column'){
				options.vAxis.minValue = 0;
				if(globalData.maxValue != undefined){
					options.vAxis.maxValue = parseInt(globalData.maxValue)+10;
				}
			}else if(globalData.type=='line'){
				options.vAxis.format = '$0.00';
				options.vAxis.minValue = 0;
				if(globalData.maxValue != undefined){
					options.vAxis.maxValue = parseFloat(globalData.maxValue)+10;
				}
			}
		}
		if(globalData.x_title != undefined){
			options.hAxis = {
				title:globalData.x_title,
				titleTextStyle:{
					color:'black',
					fontStyle:'none',
					fontWeight:'bold',
					italic:false
				}
			};
		}
		if(globalData.googleDataColors != undefined){
			options.colors = globalData.googleDataColors;
		}
		if(globalData.type=='pie'){
			options.legend.position = 'right';

			if(globalData.width != undefined){
				options.backgroundColor = '#F1F2F2';
				options.pieSliceText  = 'value';
				options.legend.position = 'none';
			}
			if(globalData.height != undefined){
			}
			chart = new google.visualization.PieChart(document.getElementById('repChartContainer'));
                        google.visualization.events.addListener(chart, 'select', function() {
                            var selectedItem = chart.getSelection()[0];
                            if ( selectedItem && selectedItem.row != undefined ) {
                                if ( chartdata.getValue(selectedItem.row, 0) == 'Violation' ) {
                                    if ( $(".tabs a.tabViolatedProducts").length ) {
                                        $(".tabs a.tabViolatedProducts").click();
                                    }
                                }
                            }
                        });
		}else if(globalData.type=='column'){
			chart = new google.visualization.ColumnChart(document.getElementById('repChartContainer'));
		}else if(globalData.type=='scatter'){
			options.vAxis.format = '$0.00';
			options.hAxis.gridlines = {
				count:10
			};

			if(globalData.maxValue != undefined){
				options.vAxis.maxValue = parseFloat(globalData.maxValue)+10;
			}
			chartdata = new google.visualization.DataTable();
			for(var ind = 0 ;ind < globalData.googleData.length;ind++){
				if(ind == 0){
					for(var dataInd=0;dataInd < globalData.googleData[ind].length;dataInd++){
						if( dataInd == 0){
							chartdata.addColumn('datetime', 'DateFOO');
						}else{
							chartdata.addColumn('number', globalData.googleData[ind][dataInd]);
						}
					}

				}else{
					for(var temp=0;temp<globalData.googleData[ind].length;temp++){
						var str = globalData.googleData[ind][temp];
						if(temp == 0 )
							str = eval(str);
						if(str==0)str = null;
						globalData.googleData[ind][temp] = str;
					}
					chartdata.addRow(globalData.googleData[ind]);
				}
			}
			chart = new google.visualization.ScatterChart(document.getElementById('repChartContainer'));
		}else{
			chart = new google.visualization.LineChart(document.getElementById('repChartContainer'));
		}
		chart.draw(chartdata, options);
	}
}

/**
 * Draw the Google Chart
 */
function drawGoogleChart(params, outliers){
// Create and populate the data table.
var data = new google.visualization.DataTable();
//colors
var c = [];
var	s = [];

var options = {
	title:'',
	// chartArea:{width:'880',height:'280',left:'75',top:'5',bottom:'10'},
	chartArea:{width:'95%',height:'280',left:'75',top:'5',bottom:'10'},
	pointSize:3,
	// width:'980',
	width:'100%',
	height:'310',
	legend: {position:'right'}
};

// set up specific type settings
switch(googleData.type){
	case 'scatter':

		// if outliers are to be removed we need to preprocess the data
		if(typeof outliers === 'object' && outliers.data != undefined && outliers.data.length > 0 && googleData.data.outliers > 0){
			// reset the data
			googleData = getGoogleData();
			for(var id in outliers.data){
				if(outliers.data[id] === 'true'){
					oIDS = googleData.data.columns[id].stats.outliers;

					// remove outlier data
					for(var index = oIDS.length - 1; index >= 0; index--)
						googleData.data.result[id].remove(oIDS[index]);
				}
			}
		}

		t = googleData.data.size;//total columns to draw (not including time for x-axis)
		dl = false;//flag for drawing horizontal lines

		if(params.type == 'lines'){
			for(var y in params.data){
				$.each(params.data[y], function(type, val){
					if(val == 'true') dl = true;
				});
			}
			if(dl) lineArray = new Object();
		}

		// add the columns
		data.addColumn({type:'datetime', label:'Date', role:'domain'})
		$.each(googleData.data.columns, function(i, val){
			data.addColumn(val.type, val.name);
			s.push({pointSize:4});
			//draw checkbox columns if present
			if(dl && typeof(params.data[i]) == 'object'){
				lineArray[i] = [];
				$.each(params.data[i], function(lt, tf){
					if(tf == 'true'){
						data.addColumn('number', '');
						lineArray[i].push(lt);
						t++;
						s.push({pointSize:0, lineWidth:1, visibleInLegend: false});
					}
				});
			}
		});

		// add the rows
		period = googleData.date.end - googleData.date.start;
		earliest = googleData.date.earliest;
		latest = 0;
		var rowData = [];
		var pInd = 1;//plotIndex starts after date column (x-axis)
		var has_range = false;
		$.each(googleData.data.result, function(id, pricing){
			c.push(googleData.data.columns[id].color.hex);
			for(var i=0; i<pricing.length; i++){
				ts = pricing[i].timestamp;
				has_range = (has_range || (ts != earliest));
				d = new Date(ts*1000);
				if(ts > latest) latest = ts;

				j = [{'v':d, 'f':pricing[i].merchant+' - '+d.formatLong()}];
				for(var x=1; x<=t; x++){
					lbl = pricing[i].price;
					curPriceObject = {v:lbl, f:'$'+lbl.toFixed(2)};
					cInd = (pInd == x) ? curPriceObject: null;
					j[x] = cInd;
				}
				rowData.push(j);
			}
			pInd++;

			//draw checkbox lines if present
			if(dl && (typeof(lineArray) == 'object' && id in lineArray)){
				$.each(lineArray[id], function(i, types){
					c.push(googleData.data.columns[id].color.hex);
					pi = 0;
					curPArray = new Object();
					for(var i=0; i<googleData.data.columns[id].pricing[types].length; i++){
						typeData = googleData.data.columns[id].pricing[types][i];
						if(typeData.stamp != 0){
							curDistance = typeData.stamp-earliest;
							curPlot = Math.round((curDistance/period)*100);
							//points are a percentage of the domain (0-100)
							if(curPlot > 100) curPlot = 100;
							else if(curPlot < 0) curPlot = 0;
						}else{
							curPlot = 0;
						}
						curPArray[curPlot] = typeData;
					}

					switch(types){
						case 'map':
							lbl = 'MAP Price';
							break;
						case 'retail':
							lbl = 'Retail Price';
							break;
						case 'wholesale':
							lbl = 'Wholesale Price';
							break;
					}

					ptsInt = (latest-earliest)/100;
					meter = earliest;
					clI = googleData.data.columns[id].pricing[types][0].price;
					for(var li = 0; li <= 100; li++){
						d = new Date(meter*1000);
						j = [{'v':d, 'f':d.formatLong()}];
						// find if clI is still current;
						if (li in curPArray)
							clI = curPArray[li].price;
						for(var px=1; px<=t; px++){
							curPriceObject = {v:clI, f:'$'+clI.toFixed(2)};
							cInd = (pInd == px) ? curPriceObject: null;
							j[px] = cInd;
						}
						rowData.push(j);
						meter += ptsInt;
					}
					pInd++;
				});
			}
		});//end googleData.data.result

		data.addRows(rowData);

		// draw the chart
		chart = new google.visualization.ScatterChart(document.getElementById('repChartContainer'));
		options.colors = c;
		options.series = s;
		options.vAxis = {format:'$0.00'};
		if ( ! has_range) {
			var hMin = new Date((earliest - 1)*1000);
			var hMax = new Date((earliest + 1)*1000);
			options.hAxis = {
			title:'',
			viewWindowMode:'explicit',
			viewWindow: {min:hMin, max:hMax}};
		}
		else {
			options.hAxis = {title:''};
		}
		break;
        
	case 'line':
		t = googleData.data.size;//total columns to draw (not including time for x-axis)
		dl = false;//flag for drawling horizontal lines

		data.addColumn('date', 'Date');
		$.each(googleData.data.columns, function(i, val){
			for(d in googleData.data.result[i]){
				data.addColumn(val.type, val.name+" "+d);
				s.push({pointSize:3,  lineWidth:2});
				c.push(googleData.data.columns[i].color[0][d]);
			}
		});

		//draw checkbox columns if present
		if(params.type == 'lines'){
			lineArray = new Object;
			for(var y in params.data){
				//$.each(params.data[y], function(type, val, lineArray){
				lineArray[y] = [];
				for(var type in params.data[y]){
					if(params.data[y][type] == 'true'){
						dl = true;
						lbl = '';
						switch(type){
							case 'map':
								lbl = 'MAP Price';
								break;
							case 'retail':
								lbl = 'Retail Price';
								break;
							case 'wholesale':
								lbl = 'Wholesale Price';
								break;
						}
						data.addColumn('number', lbl);
						lineArray[y].push(type);
						t++;
						s.push({pointSize:1, lineWidth:1, visibleInLegend: false});
						c.push(googleData.data.columns[y].color.hex);
					}
				}
			}
		}

		var rowData = [];
		var pInd = 1;//plotIndex starts after date column (x-axis)
        var _date = 0;
		$.each(googleData.data.result, function(id, pricing){
			for(d in pricing){
				for(var i=0; i<pricing[d].length; i++){
					dLbl = 'Unknown';
					if (marketLookup[d])
						dLbl = marketLookup[d];
                    if ( _date == 0 ) _date = pricing[d][i].dt*1000;
					j = [{'v':new Date(pricing[d][i].dt*1000), 'f':dLbl+' - '+pricing[d][i].date}];
					for(var x=1; x<=t; x++){
						lbl = pricing[d][i].price;
						curPriceObject = {v:pricing[d][i].price, f:'$'+lbl.toFixed(2)};
						cInd = (pInd == x) ? curPriceObject: null;
						j[x] = cInd;
					}
					rowData.push(j);
				}
				pInd++;
			}
		});//end googleData.data.result

		//draw checkbox lines if present
		if(dl && (typeof(lineArray) == 'object')){
			$.each(lineArray, function(i, types){
				for(var q = 0; q<types.length; q++){
					//just to get the dates... loop through increments
					for(var id in googleData.data.result[i]){
						for(x=0; x<googleData.data.result[i][id].length; x++){
							lbl = '';
							switch(types[q]){
								case 'map':
									lbl = 'MAP Price';
									break;
								case 'retail':
									lbl = 'Retail Price';
									break;
								case 'wholesale':
									lbl = 'Wholesale Price';
									break;
							}

							j = [{'v':new Date(googleData.data.result[i][id][x].dt*1000), 'f':''}];
							//start the price index for the lines
							sI = 0;
							for(var s=0; s<googleData.data.columns[i].pricing[types[q]].length; s++){
								if(googleData.data.columns[i].pricing[types[q]].stamp > googleData.data.result[i][id][x].dt){
									sI++;
									break;
								}
							}

							for(var px=1; px<=t; px++){
								clI = googleData.data.columns[i].pricing[types[q]][sI].price;
								curPriceObject = {v:clI, f:'$'+clI.toFixed(2)};
								cInd = (pInd == px) ? curPriceObject: null;
								j[px] = cInd;
							}
							rowData.push(j);
						}
						pInd++;
						//so we only do 1 loop
						break;
					}
				}
			});
		}

		data.addRows(rowData);
        chart = new google.visualization.LineChart(document.getElementById('repChartContainer'));
		options.vAxis = {format:'$0.00'};
		
        if ( rowData.length == 1 && _date != '' ) {
            options.hAxis = {
                    title:'', 
                    viewWindow:{
                        min: new Date(_date-86400000),
                        max: new Date(_date+86400000)
                    }
                };
        } else {
            options.hAxis = {title:''};
        }
                
		options.colors = c;
		options.series = s;
		break;
        
    case "empty":
        var chart = new google.visualization.ScatterChart(document.getElementById('repChartContainer'));           
        data = google.visualization.arrayToDataTable([
            ['Date', 'Cost'],
            [new Date(googleData.date.start * 1000), 0]
        ]);
        options.title = "No Records Were Found.";
        options.vAxis = {title:'', minValue:0, maxValue:80, format:"$0.000"};
        options.hAxis = {title:'', minValue:new Date(googleData.date.start * 1000), maxValue:new Date(googleData.date.end * 1000)}
        options.colors = ["#000"];
        break;
    case "pie":
    	// CS: added case for pie; trying to fix JS error (6/28/2105)
    	/*
    	var chart = new google.visualization.PieChart(document.getElementById('repChartContainer'));
    	google.visualization.events.addListener(chart, 'select', function() {
            var selectedItem = chart.getSelection()[0];
            if ( selectedItem && selectedItem.row != undefined ) {
                if ( chartdata.getValue(selectedItem.row, 0) == 'Violation' ) {
                    if ( $(".tabs a.tabViolatedProducts").length ) {
                        $(".tabs a.tabViolatedProducts").click();
                    }
                }
            }
        });
        */
    	return false;
}
    
    if ( googleData.type != "empty" && googleData.date !== undefined ) {
        options.hAxis = {
            title:'', 
            viewWindow:{
                min: new Date(googleData.date.start * 1000),
                max: new Date(googleData.date.end * 1000)
            }
        };
    }
    
    console.log(googleData.type);
    
	chart.draw(data, options);
    
    if ( googleData.type == "empty" ) {
        $("#repChartContainer g circle").remove();
        
        $("#repChartContainer").append('<div style="position:absolute;top:130px;left:440px;font-size:1.1em;color:#000;padding:5px;background:#fff;">No records were found.</div>'); 
    }
}//end drawGoogleChart();

lines = new Object;
lines.type = 'lines';
lines.data = [];
function drawLines(prdId, type, el){
	if(!lines.data[prdId]){
		lines.data[prdId] = {};
	}
	lines.data[prdId][type] = ($(el).is(':checked') == true) ? 'true' : 'false';
	drawGoogleChart(lines);
}

outliers = new Object;
outliers.data = [];
function removeOutliers(prdId, el){
	if(!outliers.data[prdId]){
		outliers.data[prdId] = {};
	}
	outliers.data[prdId] = ($(el).is(':checked') == true) ? 'true' : 'false';
	drawGoogleChart(lines, outliers);
}

/********************************************************************
 *                           Bookmarks                              *
 *                                                                  *
 *            AJAX and script for adding and removing bookmarks     *
 *                                                                  *
 ********************************************************************/

function add_bookmark(e){
	var name = $.trim($("#bookmarkName").val());
	if(name){
		var successCB = function(response){
			if(response.status === 'redirect') {
				window.location = base_url+'dashboard/shortcuts';
			}
			else if(response.status === 'success'){
				if($("#bookmarks .dropdownBg #noBM")) $("#bookmarks .dropdownBg #noBM").remove();
				$("#bookmarks .dropdownBg").append('<a href="'+response.shortcut_url+'" data-id="'+response.id+'">'+name+'</a>');
				$('#bookmarks').removeClass('empty');
				$('#bookmarkName').val('');
				$('#bookmarkMessage').hide('slow');
				sv('#bookmarkMessage').set_success("<p>" + response.html + "</p>", 'slow');
				setTimeout(function(){
					$('#bookmarkMessage').hide('slow');
					$("#bookmarkDialog").dialog('close');
				}, 2500);
			}
			else if (response.status === 'error'){
				sv('#bookmarkMessage').set_error("<p>" + response.html + "</p>", 'slow');
			}
		}
		$.post(base_url+"dashboard/save_shortcut", $('#bookmarkForm').serialize(), successCB, 'json');
	}else{
		sv('#bookmarkMessage').set_error('<p>Please enter a name for the bookmark.</p>', 'slow');
		$("#bookmarkName").focus();
	}
	e.preventDefault();
}

function remove_bookmark(e) {
	var bookmarkId = $(this).parent().attr('data-id');
	if (bookmarkId && bookmarkId > 0) {
		var successCB = function(response) {
			if (response.result) {
				$('a[data-id='+response.id+']').remove();
				if ($('#bookmarks a').length <= 0)
					$('#bookmarks .dropdownBg').append('<div id="noBM">No Active Bookmarks</div>');
			}
		};
		$.get(base_url + '/dashboard/delete_bookmark/' + bookmarkId, successCB, 'json');
	}

	e.preventDefault();
}

function show_remove_bookmark(e) {
	$(this).append($('#bookmarkDeleteTemplate').html());
	e.preventDefault();
}

function hide_remove_bookmark(e) {
	$(this).children('.bookmarkDelete').remove();
	e.preventDefault();
}

function bookmark_dialog(e){
	showDialog('#bookmarkDialog', 300, 200);
	$('#bookmarkName').focus();
	e.preventDefault();
}


/********************************************************************
 *                           Save Options                           *
 *                                                                  *
 *            AJAX and script for saving reports                    *
 *                                                                  *
 ********************************************************************/

// Save Report
function save_report_popup(){
	var url = base_url+'reports/automate';
	var data = {
		id: $('#report_id').val(),
		report_name: $('#report_name').val(),
		controller: $('#controller').val(),
		controller_function: $('#controller_function').val(),
		report_where: $('#report_where').val(),
		emails : ''
	};
	var saveReportPopupCB = function(response){
		var dialog = '<div class="modalWindow dialog">'+response+'</div>';
		$(dialog).dialog({
			modal: true,
			width: 420,
			height: 350,
			buttons: {
				'Cancel': function(){
					$(this).dialog('destroy').remove();
				},
				'Save': save_report_validate
			},
			close: function(){
				$(this).dialog('destroy').remove();
			}
		});
		$('.report_is_recursive_check').change(function(){
			if ($('input[name=report_is_recursive]:checked').val() == 0)
				$('.report_is_recursive_div').toggleClass('hidden');
			else {
				$('.report_is_recursive_div').toggleClass('hidden');
			}
		});
		if($('#report_datetime')) bindDate('#report_datetime');
		autocomplete_emails('input[name="email_addresses"]');
	};
	$.post(url, data, saveReportPopupCB);
}

function save_report_validate(){

	var ferr = false;
	var report_name = $.trim($('#report_name').val());
	var report_datetime = $.trim($('#report_datetime').val());
	var emails = [];
	$('input[name="email_addresses[]"]').each(function() {
		emails.push($(this).val());
	});
	if(empty(report_name)) {
		ferr = true;
		sv('#saveReportMessage').set_error('Please enter the Report Name', 'slow');
	}

	if ( $('#schedule_reports input[name="report_is_recursive"]:checked').val() > 0 ) {
		if(report_datetime == ""){
			ferr = true;
			sv('#saveReportMessage').set_error('Please enter the start Date', 'slow');
		}
	}else if(emails.length === 0 && $('input[name="report_is_recursive"]:checked').val() > 0){
		ferr = true;
		sv('#saveReportMessage').set_error('Please select at least 1 email address', 'slow');
	}

	var ret = false;
	if( ! ferr){
		$('#saveReportMessage').hide('slow');
		save_report();
		ret = true;
	}

	return ret;
}

function save_report(){
	var url = base_url+'schedule/save_report';
	var controller = $('#controller').val();
	var controllerFunction = $('#controller_function').val();
	var data = $("#schedule_reports").serialize()+'&controller='+controller+'&controller_function='+controllerFunction;
	var saveReportCB = function(response){
		if(response.status == 'success'){
			
			$('.ui-dialog').remove();
			$('.ui-widget-overlay').remove();
			
			sv_alert("<p>"+response.message+"</p>");
			
			$('#report_id').val(response.report_id);
			
			
			//$('.modalWindow').dialog('open');
			//$('.modalWindow').dialog('close');
			//$('.ui-dialog').dialog('close');
		}
	}
	$.post(url, data, saveReportCB, 'json');
}

function markRecursive(val){
	if(val=='0'){
		$('#report_schedule_label').text('Report Schedule:');
	}else{
		$('#report_schedule_label').text('Starting:');
	}
}

function initExportForm(){
    var content = '';
	if ( $('#violationoverview_navs li.ui-tabs-selected').length ) {
        var ref = $('#violationoverview_navs li.ui-tabs-selected').attr('ref');
        content = $("#"+ref).html();
        
        $('#exportForm #report_name').val($('#violationoverview_navs li.ui-tabs-selected a').html());
    } else {
        $('.exportable').each(function(){
        	//alert($(this).has("tbody").length);
        	if ($(this).has("tbody").length)
        	{
        		content += $(this)[0].outerHTML;
        	}
        });
    }
	$('#export_content').val(content);
	$('#graph_data').val(getGraphData());
}

function submitExportForm(url){
	initExportForm();
	$('#exportForm').attr('action', base_url+url);
	$('#exportForm').attr('target','_blank');
	$('#exportForm').submit();
}

/* used for adding/removing emails from schedule & saved report pages */
function xRptEmail(e){
	$(e).parent('.reportEmails').remove();
}

function saved_report_delete_popup(id){
	var dialog = '<p>Are you sure you want to delete your saved report?</p>';
	sv_confirm(dialog, function(){
		saved_report_delete(id);
	});

	return false;
}

function saved_report_delete(id){
	var savedReportDeleteCB = function(response){
		if(response.result === 'success'){
			$('.modalWindow').dialog('close');
			$('#tr_list_'+id).remove();
			$('li#report_'+id).remove();
		}
		else
			sv_alert(response.message);
	};
	$.post(base_url+"savedreports/delete", 'id='+id, savedReportDeleteCB, 'json');

	return false;
}

/* Get the proper data to send to exportForm graph_data */
function getGraphData(){
	var type = $('#graph_data').val();
	switch (type){
		case 'chart':
			return getGoogleChart();
			break;
		case 'overview':
			return getOverview();
			break;
		default:
			return '';
	}
}

/* Get the google chart iframe contents */
function getGoogleChart(){
  return $('#repChartContainer').find('iframe').contents().find('html').html();
}

/* Get the overview contents */
function getOverview(){
	return $('div.repricing_overview').contents().filter(function() {
		return $(this).attr('class') == 'current_status';
	}).html();
}

function bindDate(sel){
	$(sel).datepicker({
		dateFormat: 'mm-dd-yy',
		minDate:'mm-dd-yy',
		beforeShow: function(input){
			gl_input = input
			showClose(gl_input);
		},
		onChangeMonthYear: function (year, month, inst){
			showClose(gl_input);
		}
	});
	$(sel).keypress(function(event) {
		event.preventDefault();
	});
}

function showClose(input){
	setTimeout(function() {
		var headerPane = $( input )
		.datepicker( "widget" )
		.find( ".ui-datepicker-header" );
		$("<div>", {
			text: "x",
			click: function() {
				$('#datetime').datepicker('hide');
			}
		}).css({
			cursor:'pointer',
			float:'right',
			margin:' 0 3px 0 0'
		}).appendTo( headerPane );
	}, 1 );
}

function violator_notification(seller_id) {
	var url = base_url + 'schedule/get_violator_notification/' + seller_id;
	$.post(url, function(response) {
		if (response) {
			// prepopulate the form
			if ( ! empty(response.active) && response.active == "1")
				$('#notify_active').prop('checked', 'checked');
			else
				$('#notify_active').prop('checked', false);
			if ( ! empty(response.title))
				$('#notify_title').val(response.title);
			// contact information
			$('#notify_email_to').val( ! empty(response.email_to) ? response.email_to : '');
			$('#notify_name_to').val( ! empty(response.name_to) ? response.name_to : '');
			$('#notification_type').val(response.notification_type);

			if ( ! empty(response.email_from))
				$('#notify_email_from').val(response.email_from);
			if ( ! empty(response.name_from))
				$('#notify_name_from').val(response.name_from);
			if ( ! empty(response.phone))
				$('#notify_phone').val(response.phone);
			// 1st warning frequency
			if ( ! empty(response.days_to_warning1))
				$('#notify_days_to_warning1').val(response.days_to_warning1);
			if ( ! empty(response.warning1_repetitions))
				$('#notify_warning1_repetitions').val(response.warning1_repetitions);
			// 2nd warning frequency
			if ( ! empty(response.days_to_warning2))
				$('#notify_days_to_warning2').val(response.days_to_warning2);
			if ( ! empty(response.warning2_repetitions))
				$('#notify_warning2_repetitions').val(response.warning2_repetitions);
		}
		showDialog('#notify_dialog', 500, 670, {
			buttons: {
				'Cancel': function(){
					$(this).dialog('close');
				},
				'Save': function(){
					save_violator_notification(seller_id);
				}
			}
		});
	})
}

function save_violator_notification(seller_id) {
	// validate the form
	var $form = $('#violator_notification_form');
	var email_to = $('#notify_email_to').val();
	var email_from = $('#notify_email_from').val();
	var name_from = $('#notify_name_from').val();
	var phone = $('#notify_phone').val();
	var days_to_warning1 = $('#notify_days_to_warning1').val();
	var warning1_repetitions = $('#notify_warning1_repetitions').val();
	var days_to_warning2 = $('#notify_days_to_warning2').val();
	var warning2_repetitions = $('#notify_warning2_repetitions').val();

	var errors = [];
	if (empty(email_to))
		errors.push('Please provide the violator\'s email address.');
	if (empty(email_from))
		errors.push('Please provide a reply email address.');
	if (empty(name_from))
		errors.push('Please provide your company\'s name.');
	if (empty(phone))
		errors.push('Please provide your company\'s phone number.');
	if (empty(days_to_warning1))
		errors.push('Please provide the # of days in violation before sending the first warning.');
	if (empty(warning1_repetitions))
		errors.push('Please provide the # of times to send the first warning.');
	if (empty(days_to_warning2))
		errors.push('Please provide the # of days after the first warning before sending the second warning.');
	if (empty(warning2_repetitions))
		errors.push('Please provide the # of times to send the second warning.');

	if (errors.length > 0) {
		var error = '<p>' + errors.join('</p><p>') + '</p>';
		sv('#notify_message_fb').set_error(error, 'slow');

		return false;
	}

	// everything is valid, post to schedule/violator_notification
	var data = $form.serialize();
	var url = base_url + 'schedule/violator_notification/' + seller_id;
	$.post(url, data, function(response) {
		if (response.status) { // request was successful
			sv('#notify_message_fb').set_success(response.html, 'slow');

			setTimeout(function() {
				$('#notify_dialog').dialog('close');
				$('#notify_message_fb').removeClass().hide();
			}, 5000);

			if (NoteGrid !== undefined)
				NoteGrid.reload();
		}
		else { // request failed
			sv('#notify_message_fb').set_error(response.html, 'slow');
		}
	});

	return true;
}

function email_report_info() {
	var options = {
		buttons:{
			'Close':function(){
				$(this).dialog('close');
			},
			'Send':function(){
				var emails = [];
                                if ( $('input[name="email_addresses[]"]').length ) {
                                    $('input[name="email_addresses[]"]').each(function() {
					emails.push($(this).val());
                                    });
                                }
				if ( $('input[name="email_addresses"]').length ) {
                                    $('input[name="email_addresses"]').each(function() {
					emails.push($(this).val());
                                    });
                                }                                
				
				if(emails.length) {
					$("#sendEmailDialog .error").html('');
					
					initExportForm();
					var data = $('#sendEmailForm').serialize()+'&'+$('#exportForm').serialize();
					$(".modalWindow button:contains('Send'), .modalWindow button:contains('Close')").button("disable");
					$.post(base_url+"reports/email/", data, function(){
						sv('#report_email_success_message').set_success('Email has been sent successfully.', 'slow');
						$(".modalWindow button:contains('Send'), .modalWindow button:contains('Close')").button("enable");
						setTimeout(function(){
							$('.modalWindow').dialog('close')
						},700);
					}, "html");
				} else {
					$("#sendEmailDialog .error").html("Please enter an email address.");
				}
				return false;
			}
		}
	}
	showDialog('#sendEmailDialog', 380, 540, options);
}

/********************************************************************
 *                           Catalog                                *
 *                                                                  *
 *            Functions for setting and getting                     *
 *            Catalog page info via AJAX                            *
 *                                                                  *
 ********************************************************************/

function load_catalog_list_tab(){
	$.get(base_url + 'catalog/catalog_list', function(response){
		$('#catalogList').html(response);
		catalog_list_init();
	});
	//$('#catalogListTab').off('click.ajax');
}

function catalog_list_init(){
	CatGrid.init();
}

function load_promotional_pricing_tab(type){
	type = type ? type : 'price_floor';
	$.get(base_url + 'catalog/promotional_pricing/' + type, function(response){
		$('#promotionalPricing').html(response);
		promotional_pricing_init(type);
	});
	$('#promotionalPricingTab').off('click.ajax');
}

function promotional_pricing_init(type){
	promotional_pricing_grid(type);
	PriceGrid.init();
}

function load_product_groups_tab(){
	$.get(base_url + 'catalog/product_groups', function(response){
        $('#productGroups').html(response);
		product_groups_init();
	});
	//$('#productGroupsTab').off('click.ajax');

	$('.container').on('click', '.deleteGroup', function(e){
		group_delete(ProductGroups.selectedItem);
	});

    $('.container').on('click', '.deleteGroupProduct', function(e){
		group_product_delete(GroupProducts.selectedItem);
	});
}

function product_groups_init(){
	CatGrid.init();
	ProductGroups.url = base_url + 'catalog/get_product_groups';
	ProductGroups.init();

	GroupProducts.base_url = base_url + 'catalog/get_group_products';
}

function load_competitor_analysis_tab(){
	$.get(base_url + 'catalog/competitor_analysis', function(response){
		$('#competitorAnalysis').html(response);
		competitor_analysis_init();
	});
	$('#competitorAnalysisTab').off('click.ajax');
}

function competitor_analysis_init(){
	competitor_analysis_grid();
	CompGrid.init();
}

function load_product_lookup_tab(){
	$.get(base_url + 'catalog/product_lookup/' , function(response){
		$('#productLookup').html(response);
		product_lookup_init();
	});
	$('#productLookupTab').off('click.ajax');
}

function product_lookup_init(){
	product_lookup_grid();
	LookupGrid.init();
}

function product_tracking(ids, markas){
	document.body.style.cursor = 'wait';
	var data = {
		ids: ids,
		action: markas
	};
	var trackingCB = function(response){
		document.body.style.cursor = '';
		CatGrid.reload();
	}
	$.post(base_url+"catalog/product_tracking", data, trackingCB, 'json');

	return false;
}

/**
 * An SVListBox object for interacting with
 * product groups
 */
GroupsListBox.prototype = new SVListBox();
function GroupsListBox(sel, url){
	SVListBox.apply(this, arguments)
}
/**
 * Get a list of the current users product groups
 */
GroupsListBox.prototype.get_product_groups = function(){
	var that = this;
	var getGroupsCB = function(response) {
		that.data = response.data;
	};
	$.get(this.url, getGroupsCB, 'json');
}
GroupsListBox.prototype.set_datafields = function(){
	this.datafields = [
		{ name: 'id', type: 'int' },
		{ name: 'label' },
	];
}
GroupsListBox.prototype.create = function(){
    $(this.sel).on('bindingComplete', function (event) {
        var items = $(this).jqxListBox('getItems');
        if (items.length == 0) {
            $('#productGroupList').hide();
            $('#groupProductList').hide();
            $('#noGroupSelected').hide();
        } else {
            $('#productGroupList').show();
            $('#groupProductList').show();
            $('#noGroupSelected').show();
        }
    });
	$(this.sel).jqxListBox({
		source: this.dataAdapter,
		displayMember: 'label',
		valueMember: 'id',
		width: '100%',
		height: 120
	});
}
GroupsListBox.prototype.clear = function(){
	$(this.sel).jqxListBox('clear');
}
GroupsListBox.prototype.on_select = function(){
	var that = this;
	$(this.sel).on('select', function(e){
		if (e.args) {
			var item = e.args.item;
			if (item) {
				that.selectedItem = item;
				GroupProducts.reload(item.value);
			}
		}
	});
}
GroupsListBox.prototype.selectItem = function(value){
    var item = $(this.sel).jqxListBox('getItemByValue', value);
    $(this.sel).jqxListBox('selectItem', item);
}

/**
 * An SVListBox object for interacting with
 * group products
 */
ProductsListBox.prototype = new SVListBox();
function ProductsListBox(sel, url) {
	SVListBox.apply(this, arguments);
}
ProductsListBox.prototype.get_products = function(gid){
	this.id = gid;
	var that = this;
	var getProductsCB = function(response) {
		that.data = response.data;
	};
	$.get(this.url + '/' + gid, getProductsCB, 'json');
}
ProductsListBox.prototype.set_datafields = function(){
	this.datafields = [
		{ name: 'product_id', type: 'int' },
		{ name: 'group_id', type: 'int' },
		{ name: 'label' },
	];
}
ProductsListBox.prototype.create = function(){
    $(this.sel).on('bindingComplete', function (event) {
        var items = $(this).jqxListBox('getItems');
        if (items.length == 0) {
            $('#noGroupSelected').show();
            $('#groupProductList').hide();
        } else {
            $('#noGroupSelected').hide();
            $('#groupProductList').show();
        }
    });
	$(this.sel).jqxListBox({
		source: this.dataAdapter,
		displayMember: 'label',
		valueMember: 'product_id',
		width: '100%',
		height: 120
	});
}
ProductsListBox.prototype.clear = function(){
	$(this.sel).jqxListBox('clear');
}
ProductsListBox.prototype.reload = function(gid){
	if (+gid > 0)
		this.id = gid;
	this.url = this.base_url + '/' + this.id;
	this.init();
}
ProductsListBox.prototype.selectItem = function(item){
    $(this.sel).jqxListBox('selectItem', item);
}

var ProductGroups = new GroupsListBox('#productGroupList');
var GroupProducts = new ProductsListBox('#groupProductList');

// Promotional Pricing
function promo_pricing_add(e){
	$('#price_start,#price_end').datepicker({
		dateFormat: 'yy-mm-dd'
	});
    
	showDialog('#addPriceHistory', 'auto', 'auto', {
		buttons: {
			'Cancel': function() {
				$('#addPriceMessage').hide('slow');
				$(this).dialog('close');
			},
			'Save': function() {
				promo_pricing_save(document.getElementById('addPriceForm'));
			}
		}
	});

	e.preventDefault();
}

function promo_pricing_save(form){

	var t = $('#' + form.id + ' input[name=pricing_type]').val();
	var v = $('#' + form.id + ' input[name=price_value]').val();
	var s = $('#' + form.id + ' input[name=price_start]').val();
	var e = $('#' + form.id + ' input[name=price_end]').val();
	var i = $('#' + form.id + ' input[name=associated_product_id]').val();

	var data = {
		pricing_type: t,
		value: v,
		start_date: s,
		end_date: e,
		product_id: i,
		column: 'start_date'
	};

	// validate
	if (empty(i)){
		sv('#addPriceMessage').set_error('Please select a product.', 'slow');
		return false;
	}
	if(empty(v)){
		sv('#addPriceMessage').set_error('Please enter a price value.', 'slow');
		return false;
	}
	sObj = new Date(s);
	eObj = new Date(e);
	if(eObj.getTime()<=sObj.getTime()){
		sv('#addPriceMessage').set_error('End date must be after start date.', 'slow');
		return false;
	}

	// everything is valid, post request
	$('#addPriceMessage').hide('slow');
	var addPriceCB = function(response){
		if(response.status){
			PriceGrid.reload();
			sv('#addPriceMessage').set_success('Promotional pricing saved successfully.', 'slow');
			setTimeout(function() {
				$('#addPriceHistory').dialog('close');
				$('#' + form.id + ' input[name=associated_product_title]').val('');
				$('#' + form.id + ' input[name=associated_upc]').val('');
				$('#' + form.id + ' input[name=associated_product_id]').val('');
				$('#' + form.id + ' input[name=price_value]').val('');
				$('#' + form.id + ' input[name=price_start]').val('');
				$('#' + form.id + ' input[name=price_end]').val('');
			}, 3000);
		}
		else{
			sv('#addPriceMessage').set_error(response.msg, 'slow');
		}
	};
	$.post(base_url+'catalog/update_promotional_pricing/', data, addPriceCB, 'json');

	return true;
}

function promo_pricing_delete(e){
	var rowindexes = PriceGrid.getSelectedIds();

	if(rowindexes.length <= 0){
		var dialog = '<p>Please select a promotional pricing period to delete.<\/p>';
		sv_alert(dialog);
		return false;
	}

	var data = { ids: rowindexes, pricing_type: PriceGrid.type };

	var deletePromoPricingCB = function(response){
		var dialog;
		if(response.status){
			PriceGrid.reload();
			dialog = '<p>Pricing deleted successfully.<\/p>';
		}
		else{
			dialog = '<p>Pricing could not be deleted.<\/p>';
		}
		sv_alert(dialog);
	};
	$.post(base_url+'catalog/delete_promotional_pricing', data, deletePromoPricingCB, 'json');

	e.preventDefault();

	return true;
}

function validateManualProducts() {
    var numeric_fields = ['wholesale_price[]','retail_price[]','price_floor[]'];
	var validator = true;
    $(".inputBlockContainer").each(function(){
        if($(this).find("input[type='text']").val() == '') {	
            $(this).find('.error').remove();
            if($(this).find("input[type='text']").attr("name") == 'title[]' ) {
                $(this).append("<span class='error'>Please enter the product title.</span>");	
            }
            else if($(this).find("input[type='text']").attr("name") == 'upc_code[]' ) {
                $(this).append("<span class='error'>Please enter the product UPC code.</span>");	
            }
            else if($(this).find("input[type='text']").attr("name") == 'sku[]' ) {
                $(this).append("<span class='error'>Please enter the product SKU.</span>");	
            }
            else if($(this).find("input[type='text']").attr("name") == 'retail_price[]' ) {
                $(this).append("<span class='error'>Please enter the product retail price.</span>");	
            }
            else if($(this).find("input[type='text']").attr("name") == 'wholesale_price[]' ) {
                $(this).append("<span class='error'>Please enter the product wholesale price.</span>");	
            }
            else if($(this).find("input[type='text']").attr("name") == 'price_floor[]' ) {
                $(this).append("<span class='error'>Please enter the product map price.</span>");	
            }
            validator = false;
        }
        else {
            if($.inArray($(this).find("input[type='text']").attr('name'),numeric_fields) !== -1 && !IsNumeric($(this).find("input[type='text']").val())) {
                $(this).find('.error').remove();
                if($(this).find("input[type='text']").attr("name") == 'retail_price[]' ) {
                    $(this).append("<span class='error'>Please enter correct product retail price.</span>");	
                }
                else if($(this).find("input[type='text']").attr("name") == 'wholesale_price[]' ) {
                    $(this).append("<span class='error'>Please enter correct product wholesale price.</span>");	
                }
                else if($(this).find("input[type='text']").attr("name") == 'price_floor[]' ) {
                    $(this).append("<span class='error'>Please enter correct product map price.</span>");	
                }  
                validator = false;
           }
           else {
                $(this).find(".error").remove();
           }
        }
    });
    return validator;
}
function IsNumeric(sText){
    var ValidChars = "0123456789.";
    var IsNumber = true;
    var Char;
    for (i = 0; i < sText.length && IsNumber == true; i++) {
        Char = sText.charAt(i);
        if (ValidChars.indexOf(Char) == -1) {
            IsNumber = false;
        }
    }
    var dots = sText.split('.');
    if(dots.length > 2) {
        IsNumber = false;
    }
    return IsNumber;
}
function alphaNumeric(str) {
    var letters = /^[a-zA-Z0-9]+$/;
    return letters.test(str);
}

/**
 * @deprecated Just use .dialog('close')
 */
//function hideJModalDialog(element) {
//  if(!element)
//    element = 'dialogData';
//  $('#'+element).dialog('close');
//}
Number.prototype.format = function(n, x) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
};