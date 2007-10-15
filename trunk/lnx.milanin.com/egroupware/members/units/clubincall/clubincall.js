var urls = { 
  'base'           : 'http://members.egw.milanin.eu/',
  'api_prefix'     : 'units/clubincall/',
  'get_call_form'  : 'clubincall_get_callform.php',
  'do_call'        : 'clubincall_do_call.php',
  'get_settings'   : 'clubincall_get_settingsform.php',
  'post_actions'   : 'clubincall_actions.php',
  'template'       : '_templates/default/'
};

var xmlHttp=null;

function indicate(){
  props=client.getPage();
  indicator=document.getElementById('loading_indicator');
  if (document.getElementById('control_panel')){
    indicator.className="loading_indicator_centered";
    indicator.style.height=props.pageY*0.8;
    indicator.style.width=props.pageW*0.8;
  }else{
    indicator.className="loading_indicator";
  }
//   alert(indicator.style.display);
  if (indicator.style.display != ""){
    indicator.style.display="";
  }else{
    indicator.style.display='block';
  }
}
var client = { //
    getPage:  function() { 
                var pageWidth = 720; 
                var pageHeight = 576; 
                var scrollArr = this.getScroll(); 
                var winArr = this.getWindow(); 
                pageWidth = winArr.width + scrollArr.left; 
                pageHeight = winArr.height + scrollArr.top; 
                return { 
                  scrollX: scrollArr.left, 
                  scrollY: scrollArr.top, 
                  winW: winArr.width, 
                  winH: winArr.height, 
                  pageW: pageWidth, 
                  pageY: pageHeight 
                }; 
    }, 
    getScroll: function() {
                return { 
                  left: this.scrollLeft(),
                  top: this.scrollTop() 
                }; 
               },
    getWindow: function() {
                return { 
                  width: this.windowWidth(),
                  height: this.windowHeight()
                }; 
               }, 
    scrollLeft: function() { 
                  var xScroll = 0; 
                  if (self.pageXOffset) xScroll = self.pageXOffset; 
                  else if (document.documentElement && document.documentElement.scrollLeft)
                   xScroll = document.documentElement.scrollLeft;
                  else if (document.body) 
                   xScroll = document.body.scrollLeft; 
                  return xScroll; 
                },
    scrollTop: function() { 
                  var yScroll = 0; 
                  if (self.pageYOffset) 
                   yScroll = self.pageYOffset; 
                  else if (document.documentElement && document.documentElement.scrollTop) 
                   yScroll = document.documentElement.scrollTop; 
                  else if (document.body) 
                   yScroll = document.body.scrollTop; return yScroll; 
               },
   windowWidth: function() { 
                  var xWin = 720; 
                  if (self.innerHeight) 
                    xWin = self.innerWidth; 
                  else if (document.documentElement && document.documentElement.clientWidth)
                    xWin = document.documentElement.clientWidth;
                  else if (document.body) 
                    xWin = document.body.clientWidth; 
                  return xWin; 
                }, 
   windowHeight: function() { 
                  var yWin = 576; 
                  if (self.innerHeight) 
                    yWin = self.innerHeight; 
                  else if (document.documentElement && document.documentElement.clientHeight)
                    yWin = document.documentElement.clientHeight;
                  else if (document.body)
                    yWin = document.body.clientHeight; 
                  return yWin; 
                 }
};

function clubincall(id,url,sid){
  
//   for (prop in props){
//     props+=" "+prop;
//   }
//   alert(props.pageW+"X"+props.pageY);
  
  if (xmlHttp==null) { xmlHttp=ajaxFunction(); }
  var clubincall_wrapper=document.getElementById("clubincall_wrapper");
  if (!document.getElementById('loading_indicator')){
    loading_indicator=createElementAll('loading_indicator','div');
    loading_indicator_img=createElementAll('loading_indicator_img','img');
    loading_indicator_text=createElementAll('loading_indicator_text','span');
    loading_indicator_img.setAttribute('src',urls['base']+urls['template']+'ajax-loader.gif');
    loading_indicator.appendChild(loading_indicator_img);
    loading_indicator_text.innerHTML="Loading...";
    loading_indicator.appendChild(loading_indicator_text);
    var body=document.getElementsByTagName('body');
    body[0].appendChild(loading_indicator);
  }
  if (!document.getElementById('clubincall_dropdown')){
    var clubincall_dropdown=document.createElement('div');
    clubincall_dropdown.id='clubincall_dropdown';
    clubincall_dropdown.className='clubincall_dropdown';
    
    clubincall_wrapper.appendChild(clubincall_dropdown);
  //   alert("calling "+url+'/'+urls['api_prefix']+urls['get_call_form']+'?id='+id+','+sid);
    xmlHttp.open('GET',
      url+'/'+urls['api_prefix']+urls['get_call_form']+'?id='+id, true);
      indicate();
      xmlHttp.send(null);
      xmlHttp.onreadystatechange = render_call_form;
      
  }else{
    removeElement(document.getElementById('clubincall_dropdown'));
  }
}

function docall(){
  id=document.getElementById('dstid_input').value;
  prefix=document.getElementById('prefixes_select').value;
  number=document.getElementById('caller_input').value;
//   alert("calling" + url+'/'+urls['api_prefix']+urls['do_call']+'?id='+id)
  xmlHttp.open('GET',
      url+'/'+urls['api_prefix']+urls['do_call']+'?id='+id+
      '&prefix='+prefix+'&number='+number, true);
      indicate();
      xmlHttp.send(null);
      xmlHttp.onreadystatechange = get_call;
}
function get_call(){
  if (xmlHttp.readyState===4){
    if (xmlHttp.status == 200) {
      indicate();
      var xmldoc = xmlHttp.responseXML;
      var result = xmldoc.getElementsByTagName('result')[0].childNodes[0].nodeValue;
      if (result==0){
        alert("Will call !!!");
      }else{
        alert("Failed to setup the call: "+xmldoc.getElementsByTagName('error')[0].childNodes[0].nodeValue);
      }
    }else{
      alert("Error getting xmlHttp response: "+xmlHttp.status);
    }
  }
}

function render_call_form(){
  if (xmlHttp.readyState===4){
    if (xmlHttp.status == 200) {
    indicate();
//    alert('response: '+xmlHttp.responseText);
    var clubincall_dropdown=document.getElementById("clubincall_dropdown");
    var xmldoc = xmlHttp.responseXML;
    var debug = xmldoc.getElementsByTagName('debug');
    var dstid = xmldoc.getElementsByTagName('dstid');
    var mname = xmldoc.getElementsByTagName('name');
    var prefixes = xmldoc.getElementsByTagName('prefix');
    var dstnumber = xmldoc.getElementsByTagName('dstnumber');
    var caller_input=document.createElement('input');
    var dstid_input=document.createElement('input');
    var call_button=document.createElement('input');
    var cancel_button=document.createElement('input');
    var caller_input_label=document.createElement('input');
    var dstnumberspan_pre=document.createElement('span');
    var dstnumberspan_post=document.createElement('span');
    var callformcontrols=document.createElement('div');
    var prefixes_select=document.createElement('select');
    var settings_link=0;
    if (xmldoc.getElementsByTagName('settings').length>0){
      settings_id=xmldoc.getElementsByTagName('settings')[0].childNodes[0].nodeValue;
      var settings_link=document.createElement('a');
      settings_link.id='settings_link';
      settings_link.className='settings_link';
      settings_link.setAttribute('href','javascript:openSettings('+settings_id+')');
      settings_link.innerHTML="Settings";
    }
    dstnumberspan_pre.id='dstnumberspan_pre';
    dstnumberspan_post.id='dstnumberspan_post';
    caller_input_label.id='caller_input_label';
    dstid_input.id='dstid_input';
    prefixes_select.id='prefixes_select';
    dstnumberspan_pre.className='dstnumberspan';
    dstnumberspan_post.className='dstnumberspan';
    caller_input.className='clubincall_dropdown_input';
    prefixes_select.className='clubincall_dropdown_select';
    call_button.setAttribute('type','button');
    dstid_input.setAttribute('type','hidden');
    call_button.className='clubincall_dropdown_button call_button';
    dstid_input.setAttribute('value',dstid[0].childNodes[0].nodeValue);
    cancel_button.setAttribute('type','button');
    caller_input_label.setAttribute('type','button');
    cancel_button.className='clubincall_dropdown_button cancel_button';
    addEvent(cancel_button, 'click', close_callform);
    addEvent(call_button,'click',docall);
    addEvent(prefixes_select,'change',function () {
                                                  document.getElementById('caller_input_label').setAttribute('value',"+"+document.getElementById('prefixes_select').value); }
      )
//     cancel_button.setAttribute('value','Cancel');
    caller_input_label.setAttribute('value',"+0000");//"+&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
    caller_input_label.className="clubincall_dropdown_button label_button";
    dstnumberspan_pre.innerHTML="Enter your number";
    if (dstnumber[0].childNodes[0].nodeValue!="voicemail") {
      dstnumberspan_post.innerHTML="to get in call with "+mname[0].childNodes[0].nodeValue+" @ "+dstnumber[0].childNodes[0].nodeValue;
    }else{
      dstnumberspan_post.innerHTML="to leave a message to "+mname[0].childNodes[0].nodeValue;
    }
    caller_input.id='caller_input';
    for (i=0;i<prefixes.length;i++){
      option = document.createElement("option");
      if (prefixes[i].getAttribute("selected")==1){
        option.setAttribute('selected','true');
        caller_input.value=prefixes[i].getAttribute("last_number");
        caller_input_label.setAttribute('value',"+"+prefixes[i].getAttribute("value"));
      }
      option.text=prefixes[i].getAttribute("country_name")+
                  " / "+prefixes[i].getAttribute("value");
      option.value=prefixes[i].getAttribute("value");
      prefixes_select.options.add(option);
    }
    if (settings_link!=0)
       clubincall_dropdown.appendChild(settings_link);
    clubincall_dropdown.appendChild(dstnumberspan_pre);
    clubincall_dropdown.appendChild(callformcontrols);
    callformcontrols.appendChild(dstid_input);
    callformcontrols.appendChild(prefixes_select);
    callformcontrols.appendChild(caller_input_label);
    callformcontrols.appendChild(caller_input);
    callformcontrols.appendChild(call_button);
    callformcontrols.appendChild(cancel_button);
    clubincall_dropdown.appendChild(dstnumberspan_post);
//    alert('response'+xmlHttp.responseText);
    }else{
      alert("Error getting xmlHttp response: "+xmlHttp.status);
    }
  }
}
function close_callform (){
  var clubincall_dropdown=document.getElementById("clubincall_dropdown");
  removeElement(clubincall_dropdown);
}

function openSettings(id){
  var props=client.getPage();
  var body=document.getElementsByTagName('body');
  body=body[0];
  var background_div=document.createElement('div');
  var control_panel=document.createElement('div');
  background_div.id='background_div';
  control_panel.id='control_panel';
  control_panel.className='control_panel';
  background_div.className='background_div';
  background_div.style.height=props.pageY;
  background_div.style.width=props.pageW;
  control_panel.style.height=props.pageY*0.8;
  var clubincall_dropdown=document.getElementById("clubincall_dropdown");
  body.appendChild(background_div);
  body.appendChild(control_panel);
  if (xmlHttp==null) { xmlHttp=ajaxFunction(); }
  xmlHttp.open('GET',
  url+'/'+urls['api_prefix']+urls['get_settings']+'?id='+id, true);
  indicate();
  removeElement(clubincall_dropdown);//.style.display='none';
  body.style["overflow"]="hidden";
  body.style["height"]=props.pageY;
  if (document.body.scroll){
    alert(document.body.scroll);
    document.body.scroll="no";
  }
  xmlHttp.send(null);
  xmlHttp.onreadystatechange = renderSettings;
}
function render_titlebar(id,close_handler){
  var titlebar=createElementAll(id,'div');
  var close_link=document.createElement('a');
  close_link.setAttribute('href','javascript:'+close_handler+'');
  close_link.innerHTML='<img alt="Close" class="close_img" src="'+url+'_templates/default/close.png" />';
  titlebar.appendChild(renderSettingsMenu());
  titlebar.appendChild(close_link);
  return titlebar;
}

function renderSettingsMenu (){
  var menu=createElementAll('settings_menu','ul');
  items={'numbers_wrapper':'Numbers',
         'dsts_wrapper'   :'Rules',
         'check_wrapper'  :'Tests',
         'help_wrapper'   :'Help'
        };
  for (var key in items){
    var item=document.createElement('li');
    item.id='menu_li_'+key;
    item.className='settings_menu_li';
    var item_a=document.createElement('a');
    item_a.innerHTML=items[key];
    item_a.setAttribute('href','javascript:openwrapper("'+key+'")');
    item.appendChild(item_a);
    menu.appendChild(item);
  }
  return menu;
}

function openwrapper(wrapper){
  var settings_wrapper=document.getElementById('settings_wrapper');
  var cur_settings=document.getElementById(wrapper);
  for (var i in settings_wrapper.childNodes){
    var myid=settings_wrapper.childNodes[i].id;
    if (myid){
      if (myid.indexOf('_wrapper')>1){
        if (myid!=wrapper){
          settings_wrapper.childNodes[i].style.display='none';
        }
      }
    }
  }
  cur_settings.style.display='block';
  var ul=document.getElementById('settings_menu');
  for (var li in ul.childNodes){
      if (ul.childNodes[li].id=='menu_li_'+wrapper){
        ul.childNodes[li].className="settings_menu_li_active";
      }else{
        ul.childNodes[li].className="settings_menu_li";
      }
  }
}

function renderSettings(){
  if (xmlHttp.readyState===4){
    if (xmlHttp.status == 200) {
      indicate();
      var weekdays={ 1:"Monday",
                     2:"Tuesday",
                     3:"Wednesday",
                     4:"Thursday",
                     5:"Friday",
                     6:"Saturday",
                     7:"Sunday"
                    };
      var settings_wrapper=createElementAll('settings_wrapper','div');
      var xmldoc=xmlHttp.responseXML;
      var dsts=xmldoc.getElementsByTagName('dst');
      var numbers=xmldoc.getElementsByTagName('number');
      var numbers_wrapper=createElementAll('numbers_wrapper','div');
      var dsts_wrapper=createElementAll('dsts_wrapper','div');
//       var control_panel_toolbar=createElementAll('control_panel_toolbar','div');
      var control_panel_titlebar=render_titlebar('control_panel_titlebar','closeSettings()');
      settings_wrapper.appendChild(control_panel_titlebar);
      for (i=0;i<numbers.length;i++){
        numbers_wrapper.appendChild(renderNumber(numbers[i]));
      }
      
      for (i=0;i<dsts.length;i++){
        var dst_start_fset=document.createElement('fieldset');
        var dst_start_fset_l=document.createElement('legend');
        dst_start_fset_l.innerHTML="Starting from :";
        var dst_end_fset=document.createElement('fieldset');
        var dst_end_fset_l=document.createElement('legend');
        dst_end_fset_l.innerHTML="Until :";
        var dst_number_fset=document.createElement('fieldset');
        var dst_number_fset_l=document.createElement('legend');
        dst_number_fset_l.innerHTML="Ring this number :";
        var dst_row=document.createElement('div');
        dst_row.className='dst_row';
        var dst_row_label=document.createElement('label');
        dst_row_label.className='dst_row_label';
        dst_row_label.setAttribute('for','wstart_select');
        dst_row_label.innerHTML='Calling rule number '+dsts[i].getAttribute('id');
        var numbers_select=document.createElement('select');
        numbers_select.id='numbers_select'+row_id;
        numbers_select.className='numbers_select';
        for (ni=0;ni<numbers.length;ni++){
          var option=document.createElement('option');
          option.text=numbers[ni].getAttribute('value')+'/'+numbers[ni].getAttribute('description');
          option.value=numbers[ni].getAttribute('id');
          if (numbers[ni].getAttribute('id')==dsts[i].getAttribute('nid'))
              option.setAttribute('selected','true');
          numbers_select.options.add(option);
        }
          
        var row_id=dsts[i].getAttribute('id');
        for (j=0;j<dsts[i].childNodes.length;j++){
          var dst_child=dsts[i].childNodes[j];
          switch(dst_child.tagName){
            case 'wstart' :
              var wstart_select=document.createElement('select');
              wstart_select.className='week_select';
              wstart_select.id='wstart_select_'+row_id;
              for (var wd in weekdays){
                var option=document.createElement('option');
                option.text=weekdays[wd];
                option.value=wd
                if (wd==dst_child.getAttribute('value'))
                    option.setAttribute('selected','true');
                wstart_select.options.add(option);
              }
            break
            case 'wend' :
              var wend_select=document.createElement('select');
              wend_select.className='week_select';
              wend_select.id='wend_select_'+row_id;
              for (var wd in weekdays){
                var option=document.createElement('option');
                option.text=weekdays[wd];
                option.value=wd
                if (wd==dst_child.getAttribute('value'))
                    option.setAttribute('selected','true');
                wend_select.options.add(option);
              }
            break
            case 'hstart' :
              var hstart_select=document.createElement('select');
              hstart_select.className='hour_select';
              hstart_select.id='hstart_select_'+row_id;
              for (var h in range(0,24)){
                var option=document.createElement('option');
                option.text=h+":00";
                option.value=h;
                if (h==dst_child.getAttribute('value'))
                    option.setAttribute('selected','true');
                hstart_select.options.add(option);
              }
            break
            case 'hend' :
              var hend_select=document.createElement('select');
              hend_select.className='hour_select';
              hend_select.id='hend_select_'+row_id;
              for (var h in range(0,24)){
                var option=document.createElement('option');
                option.text=h+":00";
                option.value=h;
                if (h==dst_child.getAttribute('value'))
                    option.setAttribute('selected','true');
                hend_select.options.add(option);
              }
            break  
          }
        }
        dst_row.appendChild(dst_row_label);
        dst_row.appendChild(dst_start_fset);
          dst_start_fset.appendChild(dst_start_fset_l);
          dst_start_fset.appendChild(wstart_select);
          dst_start_fset.appendChild(hstart_select);
        dst_row.appendChild(dst_end_fset);
          dst_end_fset.appendChild(dst_end_fset_l);
          dst_end_fset.appendChild(wend_select);
          dst_end_fset.appendChild(hend_select);
        dst_row.appendChild(dst_number_fset);
          dst_number_fset.appendChild(dst_number_fset_l);
          dst_number_fset.appendChild(numbers_select);
        dst_row.appendChild(renderDstNmbrBtns(row_id,'dst'));
        
        dsts_wrapper.appendChild(dst_row);
      }
      var control_panel=document.getElementById('control_panel');
      settings_wrapper.appendChild(numbers_wrapper);
      settings_wrapper.appendChild(dsts_wrapper);
      control_panel.appendChild(settings_wrapper);
    }else{
      alert("Error getting xmlHttp response: "+xmlHttp.status);
    }
  }
}
function renderNumber(number){
  number_id=number.getAttribute('id');
  number_used=number.getAttribute('used');
  var number_row=createElementAll('number_row','div');
  var number_fset=document.createElement('fieldset');
  number_fset.className='number_fset';
  var number_input=document.createElement('input');
  number_input.id='number_input_'+number_id;
  number_input.className='number_input';
  number_input.value=number.getAttribute('value');
  var number_desc_input=document.createElement('input');
  number_desc_input.value=number.getAttribute('description');
  number_desc_input.className='number_desc_input';
  var number_use=document.createElement('div');
  number_use.className='number_use';
  number_desc_input.id='number_desc_input_'+number_id;
  var number_controls_fset=renderDstNmbrBtns(number_id,'number');
   
  if (number_used>0){
    number_row.className='number_row number_row_used';
    number_input.className='number_input number_input_used';
    number_desc_input.className='number_desc_input number_input_used';
    number_use.innerHTML='The number is in use by '+number_used+' calling rules';
  }else{
    number_row.className='number_row';
    number_input.className='number_input';
    number_desc_input.className='number_desc_input';
    number_use.innerHTML='The number is not in use';
  }
  number_fset.appendChild(number_input);
  number_fset.appendChild(number_desc_input);
  number_row.appendChild(number_fset);
  number_row.appendChild(number_use)
  number_row.appendChild(number_controls_fset);
  return number_row;
}

function renderDstNmbrBtns(id,prefix){
  var fieldset=document.createElement('fieldset');
  var save_button=document.createElement('input');
  save_button.setAttribute('type','button');
  addEvent(save_button,'click',save_handle);
  var remove_button=document.createElement('input');
  remove_button.setAttribute('type','button');
  addEvent(remove_button,'click',remove_handle);
  var add_button=document.createElement('input');
  add_button.setAttribute('type','button');
  addEvent(add_button,'click',add_handle);
  
  save_button.id=prefix+'_save_'+id;
  remove_button.id=prefix+'_remove_'+id;
  add_button.id=prefix+'_add_'+id;
  
  save_button.className=prefix+'_save_button';
  remove_button.className=prefix+'_remove_button';
  add_button.className=prefix+'_add_button';
  
  save_button.setAttribute('value','Save');
  remove_button.setAttribute('value','Remove');
  add_button.setAttribute('value','Add');
  fieldset.appendChild(save_button);
  fieldset.appendChild(remove_button);
  fieldset.appendChild(add_button);
  return fieldset;
}

function save_handle(e){
  if (!e) var e = window.event;
  if (e.target) var targ = e.target;
  else if (e.srcElement) var targ = e.srcElement;
  if (targ.nodeType == 3) // defeat Safari bug
      targ = targ.parentNode;
  if (xmlHttp==null) xmlHttp=ajaxFunction;
  props=targ.id.split('_');
  var inputs=getAllInputs(targ.parentNode.parentNode);
  var xml = '<?xml version="1.0"?>\n' +
          '<clubincall_action action="save" target="'+props[0]+'" id="'+props[2]+'">\n';
  for (var i=0; i<inputs.length;i++){
    xml += '<control id="'+inputs[i].id+'" value="'+inputs[i].value+'" />'
  }
  xml += '</clubincall_action>';
  xmlHttp.open('POST', url+'/'+urls['api_prefix']+urls['post_actions']);
  xmlHttp.setRequestHeader('content-type', 'text/xml');
  xmlHttp.onreadystatechange = buttonXMLPosted;
  indicate();
  xmlHttp.send(xml);
}
function remove_handle(e){
  if (!e) var e = window.event;
  if (e.target) var targ = e.target;
  else if (e.srcElement) var targ = e.srcElement;
  if (targ.nodeType == 3) // defeat Safari bug
      targ = targ.parentNode;
  if (xmlHttp==null) xmlHttp=ajaxFunction;
  props=targ.id.split('_');
    var xml = '<?xml version="1.0"?>\n' +
          '<clubincall_action action="remove" target="'+props[0]+'" id="'+props[2]+'">\n';
  xml += '</clubincall_action>';
  xmlHttp.open('POST', url+'/'+urls['api_prefix']+urls['post_actions']);
  xmlHttp.setRequestHeader('content-type', 'text/xml');
  xmlHttp.onreadystatechange = buttonXMLPosted;
  indicate();
  xmlHttp.send(xml);
}

function buttonXMLPosted() {
  if (xmlHttp.readyState != 4) return;
  if (xmlHttp.status == 200) {
    
    var result = xmlHttp.responseXML;
    var result_code=result.getElementsByTagName('result')[0].childNodes[0].nodeValue;
    var msg=result.getElementsByTagName('msg')[0].childNodes[0].nodeValue;
    var dbg=result.getElementsByTagName('debug')[0].childNodes[0].nodeValue;
    alert("Got result: ["+result_code+"] with msg: ["+msg+"]");
    if (dbg && dbg.length>0) alert(dbg);
    removeallchilds(document.getElementById('settings_wrapper'));
    xmlHttp.open('GET',url+'/'+urls['api_prefix']+urls['get_settings'], true);
    xmlHttp.send(null);
    xmlHttp.onreadystatechange = renderSettings;
//     indicate();
  } else {
    alert("Error getting xmlHttp response: "+xmlHttp.status);
    indicate();
  }
}
function getAllInputs(element){
  var inputs = new Array;
  var input_tags = ['input','select'];
  var input_types = ['text','select-one'];
  for (var tag in input_tags){
    var ginputs=element.getElementsByTagName(input_tags[tag]);
//     alert(input_tags[tag]+'s: '+ginputs.length);
    for ( var i=0 ; i<ginputs.length;i++ ){
//       alert(ginputs[i].type +','+ ginputs[i].id);
      if (typeof(ginputs[i])=="object" && isInArray(input_types,ginputs[i].type) ){
        inputs.push(ginputs[i]);
//         alert("pushed into inputs: "+i);
      }
    }
  }
  return inputs;
}


function add_handle(e){alert( e.type)}

function closeSettings(){
  removeallchilds(document.getElementById('control_panel'));
  removeElement(document.getElementById('control_panel'));
  removeElement(document.getElementById('background_div'));
  var body=document.getElementsByTagName('body');
  body=body[0];
  body.style["overflow"]="visible";
  body.style["height"]="100%";
} 
function findPos(obj) {
	var curleft = curtop = 0;
	if (obj.offsetParent) {
		curleft = obj.offsetLeft
		curtop = obj.offsetTop
		while (obj = obj.offsetParent) {
			curleft += obj.offsetLeft
			curtop += obj.offsetTop
		}
	}
	return [curleft,curtop];
}
function ajaxFunction(){
 try{ 
   xmlHttp=new XMLHttpRequest();
 }catch (e){
    try {
      xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }catch (e){
      try{
          xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
      }catch (e){
        alert("Your browser does not support AJAX!");
        return false;
      }
    }
  }
 return xmlHttp;
}
function getAbsPos(el)
{
  //return an element absolute position
  var elmnt=el;
  if(!elmnt) return;
  var pos =new Object;
  //get width and height
  pos.width=elmnt.offsetWidth;
  pos.height=elmnt.offsetHeight;
  //get left and top
  pos.left = 0;
  pos.top = 0;
  /*
          ie 5.0 counts the body as well with the full win width...
          so we need to stop on body
  */
  while(elmnt!=null && elmnt.nodeName!="BODY"){
          pos.left += elmnt.offsetLeft;
          pos.top += elmnt.offsetTop;
          elmnt=elmnt.offsetParent;
  }		
  //right and bottom
  pos.right = (pos.left + pos.width);
  pos.bottom = (pos.top + pos.height);
  return pos;
}
function createElementAll(id,tag){
  var element=document.createElement(tag);
  element.id=id;
  element.className=id;
  return element;
}
function removeElement(el){
  try{
    el.parentNode.removeChild(el);
  }catch(e){}
}

function removeallchilds(element){
  while (element.childNodes[0]){
    element.removeChild(element.childNodes[0]);
  }
}

function addEvent(elem, eventType, handler) {
//   alert("addEvent: "+elem+","+eventType+","+handler);
  if (elem.addEventListener) {
    if (!elem.eventHandlers) elem.eventHandlers = [];
    if (!elem.eventHandlers[eventType]) {
      elem.eventHandlers[eventType] = [];
      if (elem['on' + eventType]) elem.eventHandlers[eventType].push(elem['on' + eventType]);
      elem['on' + eventType] = handleEvent; 
    } 
      elem.eventHandlers[eventType].push(handler);
  //     for (i=0;i<elem.eventHandlers[eventType].length;i++){
  //       alert(elem.id+" handler "+i+": "+elem.eventHandlers[eventType][i]);
  //     } 
  } else {
            // IE
        elem.attachEvent("on"+eventType,handler);
  }
}

function removeEvent(elem, eventType, handler) { 
  if (elem.addEventListener) {
    var handlers = elem.eventHandlers[eventType];
      for (var i in handlers) if (handlers[i] == handler) delete handlers[i];
  }else{
    elem.detachEvent("on"+eventType,handler);
  }
}
function handleEvent(e)
{
//   alert("Handle event: "+e);
	var returnValue = true;
	if (!e) e = fixEvent(event);
	var handlers = this.eventHandlers[e.type]
	for (var i in handlers)
	{
		this.$$handleEvent = handlers[i];
		returnValue = !((returnValue && this.$$handleEvent(e)) === false);
	}
	return returnValue;
}

function fixEvent(event)
{
	// add W3C standard event methods
	event.preventDefault = fixEvent.preventDefault;
	event.stopPropagation = fixEvent.stopPropagation;
	return event;
};

fixEvent.preventDefault = function() {
	this.returnValue = false;
};

fixEvent.stopPropagation = function() {
	this.cancelBubble = true;
};
function range(a,b){
  var i=a;
  var arr = new Array;
  while (i !=b){
          arr[i]=i;
          i++;
  }
  return arr;
}
function isInArray(arr,thing){
  for (var i=0;i<arr.length;i++){
    if (arr[i]==thing){
//      alert("found "+thing);
     return true;
    }
  }
  return false;
}
