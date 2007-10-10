var urls = { 
  'api_prefix'     : 'units/clubincall/',
  'get_call_form'  : 'clubincall_get_callform.php',
  'do_call'        : 'clubincall_do_call.php',
  'get_settings'   : 'clubincall_get_settingsform.php'
};
var xmlHttp=null;

function clubincall(id,url,sid){
  if (xmlHttp==null) { xmlHttp=ajaxFunction(); }
  if (!document.getElementById('clubincall_dropdown')){
    var clubincall_dropdown=document.createElement('div');
    clubincall_dropdown.id='clubincall_dropdown';
    clubincall_dropdown.className='clubincall_dropdown';
    
    var clubincall_wrapper=document.getElementById("clubincall_wrapper")
    clubincall_wrapper.appendChild(clubincall_dropdown);
  //   alert("calling "+url+'/'+urls['api_prefix']+urls['get_call_form']+'?id='+id+','+sid);
    xmlHttp.open('GET',
      url+'/'+urls['api_prefix']+urls['get_call_form']+'?id='+id, true);
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
      xmlHttp.send(null);
      xmlHttp.onreadystatechange = get_call;
}
function get_call(){
  if (xmlHttp.readyState===4){
    if (xmlHttp.status == 200) {
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
  var background_div=document.createElement('div');
  var control_panel=document.createElement('div');
  background_div.id='background_div';
  control_panel.id='control_panel';
  control_panel.className='control_panel';
  background_div.className='background_div';
  var clubincall_dropdown=document.getElementById("clubincall_dropdown");
  clubincall_dropdown.appendChild(background_div);
  clubincall_dropdown.appendChild(control_panel);
  if (xmlHttp==null) { xmlHttp=ajaxFunction(); }
  xmlHttp.open('GET',
  url+'/'+urls['api_prefix']+urls['get_settings']+'?id='+id, true);
  xmlHttp.send(null);
  xmlHttp.onreadystatechange = renderSettings;
}

function renderSettings(){
  if (xmlHttp.readyState===4){
    if (xmlHttp.status == 200) {
      var weekdays={ 1:"Monday",
                     2:"Tuesday",
                     3:"Wednesday",
                     4:"Thursday",
                     5:"Friday",
                     6:"Saturday",
                     7:"Sunday"
                    };
      var settings_wrapper=createElementAll('settings_wrapper');
      var xmldoc=xmlHttp.responseXML;
      var dsts=xmldoc.getElementsByTagName('dst');
      var numbers=xmldoc.getElementsByTagName('number');
      for (i=0;i<numbers.length;i++){
        var nmrs_child=numbers[i];
        var number_row=document.createElement('div');
        number_row.id='number_row_'+nmrs_child.getAttribute('id');
        number_row.innerHTML='Number: id='+nmrs_child.getAttribute('id')+
                             ' number='+nmrs_child.getAttribute('value')+
                             ' used='+nmrs_child.getAttribute('used');
        settings_wrapper.appendChild(number_row);
      }
      
      for (i=0;i<dsts.length;i++){
        var dst_row=document.createElement('div');
        dst_row.className='dst_row';
        var dst_row_label=document.createElement('label');
        dst_row_label.className='dst_row_label';
        dst_row_label.setAttribute('for','wstart_select');
        dst_row_label.innerHTML='id='+dsts[i].getAttribute('id')+
                                ' nid='+dsts[i].getAttribute('nid')+
                                ' number='+dsts[i].getAttribute('number')+
                                ' ndescription='+dsts[i].getAttribute('ndescription');
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
        dst_row.appendChild(wstart_select);
        dst_row.appendChild(hstart_select);
        dst_row.appendChild(wend_select);
        dst_row.appendChild(hend_select);
        settings_wrapper.appendChild(dst_row);
      }
      var control_panel=document.getElementById('control_panel');
      control_panel.appendChild(settings_wrapper);
    }else{
      alert("Error getting xmlHttp response: "+xmlHttp.status);
    }
  }
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
  element.id=tag;
  element.className=tag;
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
