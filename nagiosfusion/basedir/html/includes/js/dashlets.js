
$(document).ready(function(){jQuery.each(jQuery.browser,function(i){if($.browser.msie){$(".dashboarddashletcontainer").each(function(){$(this).css("background","white");});}});$(".dashboarddashletcontainer").hover(function(){var theclass="dashboarddashlethover";var dragdisabled=$(this).hasClass("ui-state-disabled");if(dragdisabled==true)
theclass+=" dashboarddashlethover-pinned";$(this).addClass(theclass);$(this).children(".dashlettitle").each(function(){$(this).css("visibility","visible");});var p=$(this).children(".dashboarddashletcontrol");$(this).children(".dashboarddashletcontrol").each(function(){$(this).css("visibility","visible");});$(this).children(".ui-resizable-se").each(function(){$(this).css("visibility","visible");});},function(){$(this).removeClass("dashboarddashlethover");$(this).removeClass("dashboarddashlethover-pinned");$(this).children(".dashlettitle").each(function(){$(this).css("visibility","hidden");});var p=$(this).children(".dashboarddashletcontrol");$(this).children(".dashboarddashletcontrol").each(function(){$(this).css("visibility","hidden");});$(this).children(".ui-resizable-se").each(function(){$(this).css("visibility","hidden");});});$(".dashletpin").click(function(){var p=this.parentNode;var gp=p.parentNode;var pinned=0;var dragdisabled=$(gp).hasClass("ui-state-disabled");if(dragdisabled==true){$(gp).draggable("enable");$(this).removeClass("dashlet-pinned");$(gp).resizable("enable");pinned=0;}
else{$(gp).draggable("disable");$(this).addClass("dashlet-pinned");$(gp).resizable("disable");$(gp).addClass("ui-state-disabled");pinned=1;}
var b=$("body");var bid=b[0].id;var board=bid.substr(10);var gp=$(this).parent().parent();var dashletid=$(gp)[0].id;var dashlet=dashletid.substr(17);var zindex=$((gp)[0]).css("z-index");var optsarr={"board":board,"dashlet":dashlet,"props":{"pinned":pinned,"zindex":zindex}};var opts=array2json(optsarr);var result=get_ajax_data("setdashletproperty",opts);});$(".dashletdelete").click(function(){show_child_content_throbber();var b=$("body");var bid=b[0].id;var board=bid.substr(10);if(demo_mode==1&&board=="home"){var txtHeader=get_language_string("CannotDeleteHomepageDashletHeader");var txtMessage=get_language_string("CannotDeleteHomepageDashletMessage");var content="<div id='popup_header'><b>"+txtHeader+"</b></div><div id='popup_data'><p>"+txtMessage+"</p></div>";hide_child_content_throbber();set_child_popup_content(content);display_child_popup();fade_child_popup("red",2000);return;}
var gp=$(this).parent().parent();var dashletid=$(gp)[0].id;var dashlet=dashletid.substr(17);var optsarr={"board":board,"dashlet":dashlet};var opts=array2json(optsarr);var result=get_ajax_data("deletedashlet",opts);show_child_content_throbber();var txtHeader=get_language_string("DashletDeletedHeader");var txtMessage=get_language_string("DashletDeletedMessage");var content="<div id='popup_header'><b>"+txtHeader+"</b></div><div id='popup_data'><p>"+txtMessage+"</p></div>";hide_child_content_throbber();set_child_popup_content(content);display_child_popup();fade_child_popup("red");$(gp).fadeOut("slow");});$(".dashletconfigure").click(function(){var b=$("body");var bid=b[0].id;var board=bid.substr(10);var gp=$(this).parent().parent();var dashletid=$(gp)[0].id;var dashlet=dashletid.substr(17);});$(".dashboarddashletcontainer").each(function(){var c=$(this).children(".dashboarddashlet");var w=$(c).width();var h=$(c).height();var pr=(parseInt($(c).css("padding-right")));var pl=(parseInt($(c).css("padding-left")));var mr=(parseInt($(this).css("margin-right")))+(parseInt($(c).css("margin-right")));var ml=(parseInt($(this).css("margin-left")))+(parseInt($(c).css("margin-left")));var pt=(parseInt($(c).css("padding-top")));var pb=(parseInt($(c).css("padding-bottom")));var mt=(parseInt($(this).css("margin-top")))+(parseInt($(c).css("margin-top")));var mb=(parseInt($(this).css("margin-bottom")))+(parseInt($(c).css("margin-bottom")));if(isNaN(ml))
ml=0;if(isNaN(mr))
mr=0;if(isNaN(mt))
mt=0;if(isNaN(mb))
mb=0;var neww=w+pr+pl+mr+ml;var newh=h+pt+pb+mt+mb;if(neww>0)
$(this).css("width",neww);$(this).resizable({resize:function(event,ui){var x=1;},stop:function(event,ui){var b=$("body");var bid=b[0].id;var board=bid.substr(10);var dashletid=this.id;var dashlet=dashletid.substr(17);var height=ui.size.height;var width=ui.size.width;var zindex=$(this).css("z-index");var c=$(this).children(".dashboarddashlet");var fc=$((c)[0]);var pr=(parseInt($(fc).css("padding-right")));var pl=(parseInt($(fc).css("padding-left")));var mr=(parseInt($(this).css("margin-right")))+(parseInt($(fc).css("margin-right")));var ml=(parseInt($(this).css("margin-left")))+(parseInt($(fc).css("margin-left")));var pt=(parseInt($(fc).css("padding-top")));var pb=(parseInt($(fc).css("padding-bottom")));var mt=(parseInt($(this).css("margin-top")))+(parseInt($(fc).css("margin-top")));var mb=(parseInt($(this).css("margin-bottom")))+(parseInt($(fc).css("margin-bottom")));if(isNaN(ml))
ml=0;if(isNaN(mr))
mr=0;if(isNaN(mt))
mt=0;if(isNaN(mb))
mb=0;var neww=width-pr-pl-mr-ml;var newh=height-pt-pb-mt-mb;if(neww>0)
$(fc).css("width",neww);if(newh>0)
$(fc).css("height",newh);var cif=$(c).children("iframe");var fcif=$(cif)[0];if(fcif){$(fcif).css("width",neww);var ncifh=newh-parseInt(fcif.offsetTop);$(fcif).css("height",ncifh);}
var optsarr={"board":board,"dashlet":dashlet,"props":{"height":newh,"width":neww,"zindex":zindex}};var opts=array2json(optsarr);var result=get_ajax_data("setdashletproperty",opts);}});$(this).draggable({stack:'.dashboarddashletcontainer',snap:true,snapTolerance:10,start:function(event,ui){$(".dashboarddashletcontainer").not(this).addClass('dashboardragborder');var x=1;},stop:function(event,ui){$(".dashboarddashletcontainer").removeClass('dashboardragborder');var x=this;var b=$("body");var bid=b[0].id;var board=bid.substr(10);var dashletid=this.id;var dashlet=dashletid.substr(17);var t=$(ui.helper);var topa=t.css('top');var lefta=t.css('left');var top=topa.replace('px','');var left=lefta.replace('px','');var zindex=$(this).css("z-index");var optsarr={"board":board,"dashlet":dashlet,"props":{"top":top,"left":left,"zindex":zindex}};var opts=array2json(optsarr);var result=get_ajax_data("setdashletproperty",opts);var y=1;}});});$(".dashlet-pinned").each(function(){var p=this.parentNode;var gp=p.parentNode;$(gp).draggable("disable");$(gp).resizable("disable");});$("div.dashifybutton").hover(function(){var p=$(this).parent();$(p).addClass("dashlettablehover");$(p).fadeTo("slow",0.33);},function(){var p=$(this).parent();$(p).removeClass("dashlettablehover");$(p).fadeTo("slow",1.0);});$("a.dashifybutton").click(function(){show_child_content_throbber();var theparent=this.parentNode;var thegp=theparent.parentNode;var thecontent=$(thegp).find("div.dashlettablecontentargs");var params=thecontent[0].innerHTML;var d=get_ajax_data("getadddashletdata",params);var y=1;eval('var dashlet='+d);var boardselect=get_ajax_data("getdashboardselectmenuhtml","");;var theurl="";var txtHeader=get_language_string("AddToDashboardHeader");var txtMessage=get_language_string("AddToDashboardMessage");var t1=get_language_string("AddToDashboardTitleBoxTitle");var t2=get_language_string("AddToDashboardDashboardSelectTitle");var txtSubmitButton=get_language_string("AddItButton");var content="<div id='popup_header'><b>"+txtHeader+"</b></div><div id='popup_data'><p>"+txtMessage+"</p></div><form id='addtodashboard_form' method='get' action='"+ajax_helper_url+"'><input type='hidden' name='cmd' value='addtodashboard'><input type='hidden' name='params' value='"+params+"'><input type='hidden' name='name' value='"+dashlet.name+"'><label for='addToDashboardTitleBox'>"+t1+"</label><br class='nobr' /><input type='text' size='30' name='title' id='addtoDashboardTitleBox' value='"+dashlet.title+"' class='textfield' /><br class='nobr' /><label for='addToDashboardBoardSelect'>"+t2+"</label><br class='nobr' /><select name='board' id='addToDashboardBoardSelect'>"+boardselect+"</select><br class='nobr' />"+dashlet.confightml+"<div id='addToDashboardFormButtons'><input type='submit' class='submitbutton' name='submitButton' value='"+txtSubmitButton+"' id='submitAddToDashboardButton'></div></form>";hide_child_content_throbber();set_child_popup_content(content);display_child_popup("250px");var x=1;$("#addtodashboard_form").submit(function(){hide_throbber();var params={};$(this).find(":input, :password, :checkbox, :radio, :submit, :reset").each(function(){params[this.name||this.id||this.parentNode.name||this.parentNode.id]=this.value;});params["nsp"]=nsp_str;$.ajax({type:"POST",url:this.getAttribute("action"),data:params,beforeSend:function(XMLHttpRequest){$("#child_popup_container").each(function(){this.origHTML=this.innerHTML;txtHeader=get_language_string("AjaxSendingHeader");txtMessage=get_language_string("AjaxSendingMessage");this.innerHTML="<div id='child_popup_header'><b>"+txtHeader+"</b></div><div id='child_popup_data'><p>"+txtMessage+"</p><div id='child_popup_throbber'></div></div>";});},success:function(msg){$("#child_popup_container").each(function(){txtHeader=get_language_string("AddToDashboardSuccessHeader");txtMessage=get_language_string("AddToDashboardSuccessMessage");this.innerHTML="<div id='child_popup_header'><b>"+txtHeader+"</b></div><div id='child_popup_data'><p>"+txtMessage+"</p></div>";fade_child_popup("green");});},error:function(msg){$("#child_popup_container").each(function(){txtHeader=get_language_string("AjaxErrorHeader");txtMessage=get_language_string("AjaxErrorMessage");this.innerHTML="<div id='child_popup_header'><b>"+txtHeader+"</b></div><div id='child_popup_data'><p>"+txtMessage+"</p></div>";});}});return false;});});});