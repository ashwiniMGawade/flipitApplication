/* Default class modification */
$.extend( $.fn.dataTableExt.oStdClasses, {
	"sWrapper": "dataTables_wrapper form-inline"
} );

/* API method to get paging information  1 2 3 ... 5 6 7 8 9 ... 12 13 14 */ 
$.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings )
{
	return {
		"iStart":         oSettings._iDisplayStart,
		"iEnd":           oSettings.fnDisplayEnd(),
		"iLength":        oSettings._iDisplayLength,
		"iTotal":         oSettings.fnRecordsTotal(),
		"iFilteredTotal": oSettings.fnRecordsDisplay(),
		"iPage":          Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),
		"iTotalPages":    Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )
	};
}

/* Bootstrap style pagination control */
$.extend( $.fn.dataTableExt.oPagination, {
	"bootstrap": {
		"fnInit": function( oSettings, nPaging, fnDraw ) {
			var oLang = oSettings.oLanguage.oPaginate;
			var fnClickHandler = function ( e ) {
				e.preventDefault();
				if ( oSettings.oApi._fnPageChange(oSettings, e.data.action) ) {
					fnDraw( oSettings );
				}
			};

			$(nPaging).addClass('pagination').append(
				'<ul>'+
					'<li class="prev disabled"><a href="#">&larr; '+oLang.sPrevious+'</a></li>'+
					'<li class="next disabled"><a href="#">'+oLang.sNext+' &rarr; </a></li>'+
				'</ul>'
			);
			var els = $('a', nPaging);
			$(els[0]).bind( 'click.DT', { action: "previous" }, fnClickHandler );
			$(els[1]).bind( 'click.DT', { action: "next" }, fnClickHandler );
		},

		"fnUpdate": function ( oSettings, fnDraw ) {
			
			
			var iListLength = 7;
			var oPaging = oSettings.oInstance.fnPagingInfo();
			var an = oSettings.aanFeatures.p;
			var i, j, sClass, iStart, iEnd, iHalf=Math.floor(iListLength/2);

			if ( oPaging.iTotalPages < iListLength) {
				iStart = 1;
				iEnd = oPaging.iTotalPages;
			}
			else if ( oPaging.iPage <= iHalf ) {
				iStart = 1;
				
				iEnd = iListLength;
			} else if ( oPaging.iPage >= (oPaging.iTotalPages-iHalf) ) {
				iStart = oPaging.iTotalPages - iListLength + 1;
				iEnd = oPaging.iTotalPages;
			} else {
				iStart = oPaging.iPage - iHalf + 1;
				iEnd = iStart + iListLength - 1;
			}

			for ( i=0, iLen=an.length ; i<iLen ; i++ ) {
				// Remove the middle elements
				$('li:gt(0)', an[i]).filter(':not(:last)').remove();

				
				 
				// Add the new list items and their event handlers
				for ( j=iStart ; j<=iEnd ; j++ ) {
						
					sClass = (j==oPaging.iPage+1) ? 'class="active"' : '';
					$('<li '+sClass+'><a href="#">'+j+'</a></li>')
						.insertBefore( $('li:last', an[i])[0] )
						.bind('click', function (e) {
							e.preventDefault();
							oSettings._iDisplayStart = (parseInt($('a', this).text(),10)-1) * oPaging.iLength;
							
							
							fnDraw( oSettings );
						} );
					
				}
				
				if(iStart>=2 ){
					$('<li class="startDots"><a href="javascript:void(0)">...</a></li>')
					.insertAfter( $('li:first', an[i])[0] );
				
				
						$('<li><a href="#">1</a></li>')
						.insertBefore( $('li.startDots', an[i])[0] ).bind('click', function (e) {
							e.preventDefault();
							oSettings._iDisplayStart = 0 ;
					 		fnDraw( oSettings );
						});
					
					
				}
				if(iEnd > 1){
					
				if(oPaging.iPage <  oPaging.iTotalPages - iHalf -1  )
				{
						$('<li class="endDots"><a href="javascript:void(0)">...</a></li>')
						.insertBefore( $('li:last', an[i])[0] );
						
						$('<li><a href="#">'+ oPaging.iTotalPages  +'</a></li>')
						.insertAfter( $('li.endDots', an[i])[0] ).bind('click', function (e) {
							e.preventDefault();
							oSettings._iDisplayStart = (parseInt($('a', this).text(),10)-1) * oPaging.iLength;
							fnDraw( oSettings );
						} );
						
					}
				}
				// Add remove disabled classes from the static elements
				if ( oPaging.iPage === 0 ) {
					$('li:first', an[i]).addClass('disabled');
				} else {
					$('li:first', an[i]).removeClass('disabled');
				}

				if ( oPaging.iPage === oPaging.iTotalPages-1 || oPaging.iTotalPages === 0 ) {
					$('li:last', an[i]).addClass('disabled');
				} else {
					$('li:last', an[i]).removeClass('disabled');
				}
			}
		}
	}
} );

$.fn.dataTableExt.oApi.fnCustomRedraw = function(oSettings, page, sortCol, sortDir) {
	  oSettings._iDisplayStart = $.bbq.getState( 'iStart' , true );
	  oSettings.aaSorting[0][0] = $.bbq.getState( 'iSortCol' , true );
	  oSettings.aaSorting[0][1] = $.bbq.getState( 'iSortDir' , true );
	  oSettings.oApi._fnCalculateEnd(oSettings);
	  oSettings.oApi._fnDraw(oSettings);
	  
};