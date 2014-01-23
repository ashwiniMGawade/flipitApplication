$(document).ready(init);
function init(){
	
} 
function addFavoriteShops(shopId, userId){
	html = "";
	___addOverLay();
	    $.ajax({
			 url:HOST_PATH_LOCALE+"myfavorite/addfavoriteshop/userID/"+userId+"/rnd/"+Math.random()*99999,
			 dataType:"json",
			 type:"post",
			 data:{'shopid' : shopId, 'userid' : userId},
			 success:function(json){
				 if(parseInt(json.id) > 0){
					 getsuggestionsshops(userId,'add',json.name);
						
				}
			 }
		 });	 
	
}
var html = "";
function getsuggestionsshops(userId,flag,shopName){

	$.ajax({
		 url:HOST_PATH_LOCALE+"myfavorite/getsuggestionsshops/userID/"+userId+"/rnd/"+Math.random()*99999,
		 dataType:"json",
		 type:"post",
		 success:function(json){
			for(i in json){
				
				 var imgSrc = HOST_PATH_PUBLIC +'/'+ json[i].shop.logo.imgpath + "thum_medium_store_" + json[i].shop.logo.imgname; 
				 
				if(i == 0){
					 html += "<h3 class='text-blue'>" +__('Favorieten')+ "</h3>" +
				 			 "<div class='sub_hdr'>"+
								"<img src='"+HOST_PATH+"public/images/front_end/fav_icon.png' alt='' />"+
								" " +__('Onze suggesties voor favorieten winkels')+
							 "</div>"+
							 "<div class='shops_fav' id='suggestedShops' style='margin-top:0;'>";
				} 
				
				html += "<div class='shops_fav_txt mr18'>"+
	        				"<div class='shops_fav_top'><a href='"+HOST_PATH_LOCALE + json[i].shop.permaLink+"'><img src='"+imgSrc+"' alt='"+json[i].shop.name+"' /></a></div>"+
	        				"<div class='shops_fav_btm' onclick='addFavoriteShops("+json[i].shop.id+","+userId+");'>"+__('Toevoegen')+"</div>"+
	        			"</div>";
			}
			 html += "</div>";
			 getfavoriteselected(userId,flag,shopName);
	    }
	 });

}

function getfavoriteselected(userId,flag,shopName){

	$.ajax({
		 url:HOST_PATH_LOCALE+"myfavorite/getfavoriteselected/userID/"+userId+"/rnd/"+Math.random()*99999,
		 dataType:"json",
		 type:"post",
		 success:function(json){
			 for(j in json){
				var imgSrc = HOST_PATH_PUBLIC +'/'+ json[j].shops[0].logo.path + "thum_medium_store_" + json[j].shops[0].logo.name;
				if(j==0){
					 html += "<div class='sub_hdr'>"+
								"<img src='"+HOST_PATH+"public/images/front_end/fav_icon.png' alt='' />"+
				 				" "+__('Jouw geselecteerde favorieten')+
				 			"</div>"+
				 			"<div class='shops_fav' id='favoriteShops' style='margin-top:0;'>";
				}
				html += "<div class='shops_fav_txt mr18'>"+
			                 "<div class='shops_fav_top'><a href='"+ HOST_PATH_LOCALE + json[j].shops[0].permaLink +"' ><img src='"+imgSrc+"' alt='"+json[j].shops[0].name+"' /></a></div>"+
			                 "<div class='shops_fav_btmnew' onclick='deleteFavoriteShops("+json[j].shops[0].id+","+userId+");'>"+__('verwijderen')+"</div>"+
			            "</div>";
				
			 }
			 
			 html += "</div>";
			 getfavoriteoffer(flag,shopName);
			
	    }
	 });

}

function getfavoriteoffer(flag,shopName){

	$.ajax({
		 url:HOST_PATH_LOCALE+"myfavorite/getfavoriteoffer/rnd/"+Math.random()*99999,
		 type:"post",
		 success:function(json){
			 html += json;
			$('div.fav_txt_rt').html(html);
			___removeOverLay();
			if(flag == 'add'){
			    flashMessage( shopName + " " + __('is toegevoegd aan uw favorieten'));
			}else{
				flashMessage(shopName + " " + __('is verwijderd uit uw favorieten'));
			}
	     }
	 });

}

function deleteFavoriteShops(shopId,userId)
{
	html = "";
	___addOverLay();
	$.ajax({
		 url:HOST_PATH_LOCALE+"myfavorite/deletefavorite/userID/"+userId+"/rnd/"+Math.random()*99999,
		 dataType:"json",
		 type:"post",
		 data:{'shopid' : shopId, 'userid' : userId},
		 success:function(json){
			 if(parseInt(json.id) > 0){
				 getsuggestionsshops(userId,'delete',json.name);
			 }
		 }
	 });
}

function flashMessage(msg)
{
	$("div#messageDiv").fadeIn()
					   .find('strong')
					   .html(msg);
}