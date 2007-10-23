var urls = { 
  'base'           : 'http://members.egw.milanin.eu/',
  'api_prefix'     : 'units/clubincall/',
  'get_call_form'  : 'clubincall_get_callform.php',
  'do_call'        : 'clubincall_do_call.php',
  'get_settings'   : 'clubincall_get_settingsform.php',
  'post_actions'   : 'clubincall_actions.php',
  'template'       : '_templates/default/',
  'openwrapper'    : 'clubincall_openwrapper.php'
};

// var xmlHttp=null;
// var xmlHttpAlt=null;
var weekdays={ 1:"Monday",
                     2:"Tuesday",
                     3:"Wednesday",
                     4:"Thursday",
                     5:"Friday",
                     6:"Saturday",
                     7:"Sunday"
                    };
                    
function indicate(){
  props=client.getPage();
  indicator=document.getElementById('loading_indicator');
  if (document.getElementById('control_panel')){
    indicator.className="loading_indicator_centered";
//     indicator.style.height=props.pageY*0.8;
//     indicator.style.width=props.pageW*0.8;
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
  
//   if (xmlHttp==null) { xmlHttp=ajaxFunction(); }
  var clubincall_wrapper=document.getElementById("clubincall_wrapper");
  if (!document.getElementById('loading_indicator')){
    loading_indicator=createElementAll('loading_indicator','div');
    loading_indicator_text=createElementAll('loading_indicator_text','div');
    loading_indicator_img=createElementAll('loading_indicator_img','img');
    loading_indicator_img.setAttribute('src',urls['base']+urls['template']+'ajax-loader.gif');
    loading_indicator_text.innerHTML="Loading...";
    loading_indicator_text.appendChild(loading_indicator_img);
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
  
    var ai = new AJAXInteraction(
      url+'/'+urls['api_prefix']+urls['get_call_form']+'?id='+id, render_call_form);
      indicate();
      ai.doGet();
  }else{
    removeElement(document.getElementById('clubincall_dropdown'));
  }
}

function docall(){
  id=document.getElementById('dstid_input').value;
  prefix=document.getElementById('prefixes_select').value;
  number=document.getElementById('caller_input').value;
//   alert("calling" + url+'/'+urls['api_prefix']+urls['do_call']+'?id='+id)
  var ai = new AJAXInteraction(url+'/'+urls['api_prefix']+urls['do_call']+'?id='+id+
      '&prefix='+prefix+'&number='+number, get_call);
  indicate();
  ai.doGet();
}

function get_call(xmldoc){
      indicate();
      var result = xmldoc.getElementsByTagName('result')[0].childNodes[0].nodeValue;
      if (result==0){
        openCall(xmldoc);
      }else{
        alert("Failed to setup the call: "+xmldoc.getElementsByTagName('error')[0].childNodes[0].nodeValue);
      }
}
function openCall(xmldoc){
  var props=client.getPage();
  var body=document.getElementsByTagName('body');
  body=body[0];
  var background_div=createElementAll('background_div','div');
  var call_panel=createElementAll('call_panel','div');
  background_div.style.height=props.pageY;
  background_div.style.width=props.pageW;
  call_panel.style.height=props.pageY*0.9;
  var clubincall_dropdown=document.getElementById("clubincall_dropdown");
  body.appendChild(background_div);
  body.appendChild(call_panel);
  indicate();
  removeElement(clubincall_dropdown);
  body.style["overflow"]="hidden";
  body.style["height"]=props.pageY;
  if (document.body.scroll){
    alert(document.body.scroll);
    document.body.scroll="no";
  }
  var progress_div=createElementAll('progress_div','div');
  var mysrc=xmldoc.getElementsByTagName('src')[0];
  var mydst=xmldoc.getElementsByTagName('dst')[0];
  var member_div=createElementAll('member_div','div');
  var member_icon=createElementAll('member_icon','img');
  member_icon.setAttribute('src',mydst.getAttribute('icon'));
  var member_name_span=createElementAll('member_name_span','span');
  member_name_span.innerHTML=mydst.getAttribute('name')+
                                " @ "+mydst.getAttribute('desc');
  member_div.appendChild(member_icon);
  member_div.appendChild(member_name_span);
  progress_span=createElementAll('progress_span','span');
  progress_span.innerHTML=progress_div.innerHTML+"Connecting "+mydst.getAttribute('name')+
                         " to +"+mysrc.getAttribute('number');
  member_div.appendChild(progress_span);
  progress_div.appendChild(member_div);
  call_panel.appendChild(progress_div);
  indicate();
}

// function renderCall(xmldoc)
function render_call_form(xmldoc){
  
  
    indicate();
//    alert('response: '+xmlHttp.responseText);
    var clubincall_dropdown=document.getElementById("clubincall_dropdown");
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
    call_button.setAttribute('type','image');
    call_button.setAttribute('src','/'+urls['template']+'/clubincall-g.png');
    cancel_button.setAttribute('src','/'+urls['template']+'/clubincall-r.png');
    dstid_input.setAttribute('type','hidden');
    call_button.className='clubincall_dropdown_button call_button';
    dstid_input.setAttribute('value',dstid[0].childNodes[0].nodeValue);
    cancel_button.setAttribute('type','image');
    caller_input_label.setAttribute('type','text');
    cancel_button.className='clubincall_dropdown_button cancel_button';
    addEvent(cancel_button, 'click', close_callform);
    addEvent(call_button,'click',docall);
    addEvent(prefixes_select,'change',function () {
                                                  document.getElementById('caller_input_label').setAttribute('value',"+"+document.getElementById('prefixes_select').value); }
      )
//     cancel_button.setAttribute('value','Cancel');
    caller_input_label.setAttribute('value',"+0000");//"+&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
    caller_input_label.setAttribute('disabled',"true");
    //caller_input_label.innerHTML="+0000";//"+&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
    caller_input_label.className="clubincall_dropdown_input label_button";
    dstnumberspan_pre.innerHTML="Enter your number";
    if (dstnumber[0].childNodes[0].nodeValue!="voicemail") {
      dstnumberspan_post.innerHTML="to get in call with "+mname[0].childNodes[0].nodeValue+" @ "+dstnumber[0].childNodes[0].nodeValue;
    }else{
      dstnumberspan_post.innerHTML="to leave a message to "+mname[0].childNodes[0].nodeValue;
    }
    caller_input.id='caller_input';
    for (i=0;i<prefixes.length;i++){
      option = document.createElement("option");
      option.text=prefixes[i].getAttribute("country_name")+
                  " / "+prefixes[i].getAttribute("value");
      option.value=prefixes[i].getAttribute("value");
      prefixes_select.options[prefixes_select.options.length]=option;
      if (prefixes[i].getAttribute("selected")==1){
        prefixes_select.selectedIndex=i;
        caller_input.value=prefixes[i].getAttribute("last_number");
        caller_input_label.setAttribute('value',"+"+prefixes[i].getAttribute("value"));
      }
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
  control_panel.style.height=props.pageY*0.9;
  var clubincall_dropdown=document.getElementById("clubincall_dropdown");
  body.appendChild(background_div);
  body.appendChild(control_panel);
//   if (xmlHttp==null) { xmlHttp=ajaxFunction(); }
  var ai = new AJAXInteraction(
  url+'/'+urls['api_prefix']+urls['get_settings']+'?id='+id, renderSettings);
  indicate();
  removeElement(clubincall_dropdown);//.style.display='none';
  body.style["overflow"]="hidden";
  body.style["height"]=props.pageY;
  if (document.body.scroll){
    alert(document.body.scroll);
    document.body.scroll="no";
  }
  ai.doGet();
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
  var default_item='numbers_wrapper';
  for (var key in items){
    var item=document.createElement('li');
    item.id='menu_li_'+key;
    if (key==default_item){
      item.className='settings_menu_li_active'
    }else{
      item.className='settings_menu_li';
    }
    var item_a=document.createElement('a');
    item_a.innerHTML=items[key];
    item_a.setAttribute('href','javascript:openwrapper("'+key+'")');
    item.appendChild(item_a);
    menu.appendChild(item);
  }
  return menu;
}

function openwrapper(wrapper){

  var ai = new AJAXInteraction(
      url+'/'+urls['api_prefix']+urls['openwrapper']+'?wrapper='+wrapper,function () {
//                                                 if (xmlHttp.readyState===4 && 
//                                                     xmlHttp.status == 200) 
//                                                     { 
//                                                       alert(xmlHttp.responseText);
//                                                     }
//                                                 } 
                                                        return;
                                                   }
    );
      ai.doGet();
//       xmlHttp.onreadystatechange = ;
  var settings_wrapper=document.getElementById('settings_wrapper');
  var cur_settings=document.getElementById(wrapper);
  for (var i=0;i<settings_wrapper.childNodes.length;i++){
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

function renderSettings(xmldoc){
//   if (xmlHttp.readyState===4){
//     if (xmlHttp.status == 200) {
      indicate();
      var settings_wrapper = document.getElementById('settings_wrapper');
      if (!settings_wrapper)
       settings_wrapper=createElementAll('settings_wrapper','div');
//       alert(xmlHttp.responseText);
//       var xmldoc=xmlHttp.responseXML;
//       if (!xmldoc) alert("nodoc!!!");
      var dsts=xmldoc.getElementsByTagName('dst');
      var numbers=xmldoc.getElementsByTagName('number');
      var activewrapper=xmldoc.getElementsByTagName('activewrapper');
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
        
        var row_id=dsts[i].getAttribute('id');
        var numbers_select=document.createElement('select');
        numbers_select.id='numbers_select_'+row_id;
        numbers_select.className='numbers_select';
        for (ni=0;ni<numbers.length;ni++){
          var option=document.createElement('option');
          option.text=numbers[ni].getAttribute('value')+'/'+numbers[ni].getAttribute('description');
          option.value=numbers[ni].getAttribute('id');
          if (numbers[ni].getAttribute('id')==dsts[i].getAttribute('nid'))
              option.setAttribute('selected','true');
          numbers_select.options[numbers_select.options.length]=option;
        }
          
        for (j=0;j<dsts[i].childNodes.length;j++){
          var dst_child=dsts[i].childNodes[j];
          switch(dst_child.tagName){
            case 'wstart' :
              var wstart_select=document.createElement('select');
              wstart_select.className='week_select';
              wstart_select.id='wstart_select_'+row_id;
              addEvent(wstart_select,'change',dst_select_update);
              for (var wd in weekdays){
                var option=document.createElement('option');
                option.text=weekdays[wd];
                option.value=wd
                wstart_select.options[wstart_select.options.length]=option;
                if (wd==dst_child.getAttribute('value'))
                    wstart_select.selectedIndex=wd-1;
              }
            break
            case 'wend' :
              var wend_select=document.createElement('select');
              wend_select.className='week_select';
              wend_select.id='wend_select_'+row_id;
              addEvent(wend_select,'change',dst_select_update);
              for (var wd in weekdays){
                var option=document.createElement('option');
                option.text=weekdays[wd];
                option.value=wd
                wend_select.options[wend_select.options.length]=option;
                if (wd==dst_child.getAttribute('value'))
                    wend_select.selectedIndex=wd-1;
              }
            break
            case 'hstart' :
              var hstart_select=document.createElement('select');
              hstart_select.className='hour_select';
              hstart_select.id='hstart_select_'+row_id;
              addEvent(hstart_select,'change',dst_select_update);
              for (var h in range(0,24)){
                var option=document.createElement('option');
                option.text=h+":00";
                option.value=h;
                hstart_select.options[hstart_select.options.length]=option;
                if (h==dst_child.getAttribute('value'))
                    hstart_select.selectedIndex=h;
              }
            break
            case 'hend' :
              var hend_select=document.createElement('select');
              hend_select.className='hour_select';
              hend_select.id='hend_select_'+row_id;
              addEvent(hend_select,'change',dst_select_update);
              for (var h in range(0,24)){
                var option=document.createElement('option');
                option.text=h+":00";
                option.value=h;
                hend_select.options[hend_select.options.length]=option;
                if (h==dst_child.getAttribute('value')){
                    hend_select.selectedIndex=h;
                    
                }
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
//       settings_wrapper.appendChild(render_toolbar('add'));
      control_panel.appendChild(settings_wrapper);
      numbers_wrapper.appendChild(render_toolbar('number'));
      dsts_wrapper.appendChild(render_toolbar('dst'));
      if (activewrapper.length>0){
        activewrapper=activewrapper[0].childNodes[0].nodeValue;
        openwrapper(activewrapper);
      }
        
//     }else{
//       alert("Error getting xmlHttp response: "+xmlHttp.status);
//     }
//   }
}
function render_toolbar(suffix){
  var mytoolbar=createElementAll('toolbar_'+suffix,'div');
  var add_button=document.createElement('input');
  add_button.setAttribute('type','button');
  addEvent(add_button,'click',add_handle);
  add_button.setAttribute('value','Add New');
  add_button.className='add_button';
  add_button.id='add_button_'+suffix;
  mytoolbar.appendChild(add_button);
//   mytoolbar.style["margin-top"]=(getElementHeight('control_panel')*0.8);
//   mytoolbar.style.height=(getElementHeight('control_panel')*0.2);
  return mytoolbar;
}
function renderNumberAdd(){
  var number_row=createElementAll('number_row','div');
  var number_row_label=createElementAll('adding_new_label','div');
  number_row_label.innerHTML="Adding New Number :";
  var number_fset=document.createElement('fieldset');
  number_fset.className='number_fset';
  var number_input=document.createElement('input');
  number_input.id='number_input_';
  number_input.className='number_input';
  var number_desc_input=document.createElement('input');
  number_desc_input.className='number_desc_input';
  number_desc_input.id='number_desc_input_';
  number_row.className='number_row';
  number_input.className='number_input';
  number_desc_input.className='number_desc_input';

  var legend=document.createElement('legend');
  legend.innerHTML="Number and Description";
  number_fset.appendChild(legend);
  number_fset.appendChild(number_input);
  number_fset.appendChild(number_desc_input);
  number_row.appendChild(number_row_label);
  number_row.appendChild(number_fset);
  number_row.appendChild(renderDstNmbrBtns(null,'number'));
  
//   numbers_wrapper=document.getElementById('numbers_wrapper');
//   numbers_wrapper.appendChild(number_row);
  return number_row;
}

function getNumbers(xmldoc){
  numbers=xmldoc.getElementsByTagName('number');
  numbers_select=document.getElementById('numbers_select_');
  for (var ni=0;ni<numbers.length;ni++){
    var option=document.createElement('option');
    option.text=numbers[ni].getAttribute('value')+'/'+numbers[ni].getAttribute('description');
    option.value=numbers[ni].getAttribute('id');
    numbers_select.options[numbers_select.options.length]=option;
  }
  indicate();
}

function renderDstAdd(){
    
    var dst_row=document.createElement('div');
    dst_row.className='dst_row';
    dst_row.id='dst_add_row';
    
    var dst_row_label=createElementAll('adding_new_label','div');
    
    var dst_start_fset=document.createElement('fieldset');
    var dst_start_fset_l=document.createElement('legend');
    var dst_end_fset=document.createElement('fieldset');
    var dst_end_fset_l=document.createElement('legend');
    
    var dst_number_fset=document.createElement('fieldset');
    var dst_number_fset_l=document.createElement('legend');
    
    dst_number_fset_l.innerHTML="Ring this number :";
    dst_start_fset_l.innerHTML="Starting from :";
    dst_end_fset_l.innerHTML="Until :";
    dst_row_label.innerHTML="Adding New Rule :";
    
    var numbers_select=document.createElement('select');
    numbers_select.id='numbers_select_';
    numbers_select.className='numbers_select';
    
    var wstart_select=document.createElement('select');
    wstart_select.className='week_select';
    wstart_select.id='wstart_select_';
    for (var wd in weekdays){
      var option=document.createElement('option');
      option.text=weekdays[wd];
      option.value=wd
      wstart_select.options[wstart_select.options.length]=option;
    }
    wstart_select.selectedIndex=0;
    addEvent(wstart_select,'change',dst_select_update);
    
    var wend_select=document.createElement('select');
    wend_select.className='week_select';
    wend_select.id='wend_select_';
    for (var wd in weekdays){
      var option=document.createElement('option');
      option.text=weekdays[wd];
      option.value=wd
      wend_select.options[wend_select.options.length]=option;
    }
    wend_select.selectedIndex=6;
    addEvent(wend_select,'change',dst_select_update);
        
    var hstart_select=document.createElement('select');
    hstart_select.className='hour_select';
    hstart_select.id='hstart_select_';
    for (var h in range(0,24)){
      var option=document.createElement('option');
      option.text=h+":00";
      option.value=h;
      hstart_select.options[hstart_select.options.length]=option;
    }
    hstart_select.selectedIndex=0;
    addEvent(hstart_select,'change',dst_select_update);
    var hend_select=document.createElement('select');
    hend_select.className='hour_select';
    hend_select.id='hend_select_';
    for (var h in range(0,24)){
      var option=document.createElement('option');
      option.text=h+":00";
      option.value=h;
      hend_select.options[hend_select.options.length]=option;
    }
      hend_select.selectedIndex=h;
      addEvent(hend_select,'change',dst_select_update);
    
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
    dst_row.appendChild(renderDstNmbrBtns(null,'dst'));
    return dst_row;
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
  var legend=document.createElement('legend');
  legend.innerHTML="Number and Description";
  number_fset.appendChild(legend);
  number_fset.appendChild(number_input);
  number_fset.appendChild(number_desc_input);
  number_row.appendChild(number_use);
  number_row.appendChild(number_fset);
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

  
  save_button.id=prefix+'_save_'+id;
  remove_button.id=prefix+'_remove_'+id;
//   add_button.id=prefix+'_add_'+id;
  
  save_button.className=prefix+'_save_button';
  remove_button.className=prefix+'_remove_button';
//   add_button.className=prefix+'_add_button';
  
  save_button.setAttribute('value','Save');
  remove_button.setAttribute('value','Remove');
//   add_button.setAttribute('value','Add');
  var legend=document.createElement('legend');
  legend.innerHTML="Save or Remove";
  fieldset.appendChild(legend);
  fieldset.appendChild(save_button);
  fieldset.appendChild(remove_button);
//   fieldset.appendChild(add_button);
  
  return fieldset;
}

function save_handle(e){
  if (!e) var e = window.event;
  if (e.target) var targ = e.target;
  else if (e.srcElement) var targ = e.srcElement;
  if (targ.nodeType == 3) // defeat Safari bug
      targ = targ.parentNode;
//   if (xmlHttp==null) xmlHttp=ajaxFunction;
  props=targ.id.split('_');
  var inputs=getAllInputs(targ.parentNode.parentNode);
  var xml = '<?xml version="1.0"?>\n' +
          '<clubincall_action action="save" target="'+props[0]+'" id="'+props[2]+'">\n';
  for (var i=0; i<inputs.length;i++){
    xml += '<control id="'+inputs[i].id+'" value="'+inputs[i].value+'" />'
  }
  xml += '</clubincall_action>';
  var ai=new AJAXInteraction(url+'/'+urls['api_prefix']+urls['post_actions'],buttonXMLPosted);
  indicate();
  ai.doPost(xml);
}
function remove_handle(e){
  if (!e) var e = window.event;
  if (e.target) var targ = e.target;
  else if (e.srcElement) var targ = e.srcElement;
  if (targ.nodeType == 3) // defeat Safari bug
      targ = targ.parentNode;
//   if (xmlHttp==null) xmlHttp=ajaxFunction;
  props=targ.id.split('_');
    var xml = '<?xml version="1.0"?>\n' +
          '<clubincall_action action="remove" target="'+props[0]+'" id="'+props[2]+'">\n';
  xml += '</clubincall_action>';
  var ai=new AJAXInteraction(url+'/'+urls['api_prefix']+urls['post_actions'],buttonXMLPosted);
  indicate();
  ai.doPost(xml);
}

function buttonXMLPosted(xmldoc) {
//   if (xmlHttp.readyState != 4) return;
//   if (xmlHttp.status == 200) {
    
//     var result = xmlHttp.responseXML;
     var result_code=xmldoc.getElementsByTagName('result')[0].childNodes[0].nodeValue;
     var msg=xmldoc.getElementsByTagName('msg')[0].childNodes[0].nodeValue;
//     var dbg=xmldoc.getElementsByTagName('debug')[0].childNodes[0].nodeValue;
    if (result_code!=0){
      alert("Error "+result_code+": \n "+msg);
    }
    removeallchilds(document.getElementById('settings_wrapper'));
    var ai=new AJAXInteraction(url+'/'+urls['api_prefix']+urls['get_settings'], renderSettings);
    ai.doGet();
//     indicate();
//   } else {
//     alert("Error getting xmlHttp response: "+xmlHttp.status);
//     indicate();
//   }
}
function getAllInputs(element){
  var inputs = new Array;
  var input_tags = ['input','select'];
  var input_types = ['text','select-one'];
  for (var tag in input_tags){
    var ginputs=element.getElementsByTagName(input_tags[tag]);
//     alert(input_tags[tag]+'s: '+ginputs.length);
    for ( var i=0 ; i<ginputs.length;i++ ){
//        alert(ginputs[i].type +','+ ginputs[i].id);
      if (typeof(ginputs[i])=="object" && isInArray(input_types,ginputs[i].type) ){
        inputs.push(ginputs[i]);
//          alert("pushed into inputs: "+i);
      }
    }
  }
  return inputs;
}


function add_handle(e){
  if (!e) var e = window.event;
  if (e.target) var targ = e.target;
  else if (e.srcElement) var targ = e.srcElement;
  if (targ.nodeType == 3) // defeat Safari bug
      targ = targ.parentNode;
//   if (xmlHttp==null) xmlHttp=ajaxFunction;
  props=targ.id.split('_');
  switch (props[2]){
    case 'number':
      var add_row=renderNumberAdd();
      break;
    case 'dst':
      indicate();
      add_row=renderDstAdd();
      var ai = new AJAXInteraction( url+'/'+urls['api_prefix']+urls['get_settings'],
                                    getNumbers
                                  );
      ai.doGet();
      break;
  }
  targ.parentNode.insertBefore(add_row,targ);
  targ.setAttribute('disabled','true');
  remove_button=document.getElementById(props[2]+'_remove_null');
  removeEvent(remove_button,'click',remove_handle);
  addEvent(remove_button,'click',removeAddingNew);
}

function removeAddingNew(e){
  if (!e) var e = window.event;
  if (e.target) var remove_button = e.target;
  else if (e.srcElement) var remove_button = e.srcElement;
  if (remove_button.nodeType == 3) // defeat Safari bug
      remove_button = remove_button.parentNode;
  props=remove_button.id.split("_");
  var mytoolbar=document.getElementById('toolbar_'+props[0]);
  var mydiv=mytoolbar.getElementsByTagName('div')[0];
  mytoolbar.removeChild(mydiv);
  
  var add_button=document.getElementById('add_button_'+props[0]);
  add_button.removeAttribute('disabled');
}

function closeSettings(){
  removeallchilds(document.getElementById('control_panel'));
  removeElement(document.getElementById('control_panel'));
  removeElement(document.getElementById('background_div'));
  var body=document.getElementsByTagName('body');
  body=body[0];
  body.style["overflow"]="visible";
  body.style["height"]="100%";
}
 
function dst_select_update(e){
  if (!e) var e = window.event;
  if (e.target) var updated = e.target;
  else if (e.srcElement) var updated = e.srcElement;
  if (updated.nodeType == 3) // defeat Safari bug
      updated = updated.parentNode;
  var id=updated.id.split('_')[2];
  if (updated.id){
    if (updated.id.indexOf('w')==0){
      if (updated.id.indexOf('wstart')==0){
        wend_select=document.getElementById('wend_select_'+id);
        if (updated.value>wend_select.value 
              ||
             (7-updated.value) > wend_select.options.length 
              ||
             (wend_select.options.length == 7 && updated.value>1)
            ){
          var selected_value=wend_select.value;
          wend_select.options.length=0;
          var j=0;
          for (var i=updated.value;i<=7;i++){
            var option=document.createElement('option');
            option.value=i;
            option.text=weekdays[i];
            wend_select.options[wend_select.options.length]=option;
            if (i==selected_value){
              option.selectedIndex=j;
            }
            j++;
          }
        }
      }
      else if (updated.id.indexOf('wend')==0){
        wstart_select=document.getElementById('wstart_select_'+id);
        if (updated.value<wstart_select.value
            ||
            (7-updated.value) < wstart_select.options.length 
          ){
          var selected_value=wstart_select.value;
          wstart_select.options.length=0;
          var j=0;
          for (var i=1;i<=updated.value;i++){
            var option=document.createElement('option');
            option.value=i;
            option.text=weekdays[i];
            wstart_select.options[wstart_select.options.length]=option;
            if (i==selected_value){
              wstart_select.selectedIndex=j;
            }
            j++;
          }
        }
      }
    }else if (updated.id.indexOf('h')==0){
      if (updated.id.indexOf('hstart')==0){
        hend_select=document.getElementById('hend_select_'+id);
        if (updated.value>hend_select.value 
              ||
             (24-updated.value) > hend_select.options.length 
              ||
             (hend_select.options.length == 24 && updated.value>0)
            ){
          var selected_value=hend_select.value;
          hend_select.options.length=0;
          var j=0;
          for (var i=Number(updated.value)+1;i<=23;i++){
            var option=document.createElement('option');
            option.value=i;
            option.text=i+':00';
            hend_select.options[hend_select.options.length]=option;
            if (i==selected_value){
              hend_select.selectedIndex=j;
            }
            j++;
          }
        }
      }
      else if (updated.id.indexOf('hend')==0){
        hstart_select=document.getElementById('hstart_select_'+id);
        if (updated.value < hstart_select.value
            ||
            (24-updated.value) < hstart_select.options.length 
          ){
          var selected_value=hstart_select.value;
          hstart_select.options.length=0;
          var j=0;
          for (var i=0;i<Number(updated.value);i++){
            var option=document.createElement('option');
            option.value=i;
            option.text=text=i+':00';
            hstart_select.options[hstart_select.options.length]=option;
            if (i==selected_value){
              hstart_select.selectedIndex=j;
            }
            j++;
          }
        }
      }
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

function AJAXInteraction(url, callback) {

    var req = init();
    req.onreadystatechange = processRequest;
    
        
    function init() {
      if (window.XMLHttpRequest) {
        return new XMLHttpRequest();
      } else if (window.ActiveXObject) {
        return new ActiveXObject("Microsoft.XMLHTTP");
      }else{
        alert("Call Huston! No XMLHTTP found!");
      }
    }
    
    function processRequest () {
      if (req.readyState == 4) {
        if (req.status == 200) {
          if (req.responseXML){
            if (callback) callback(req.responseXML);
          }
        }
      }
    }

    this.doGet = function() {
      req.open("GET", url, true);
      req.send(null);
    }
    
    this.doPost = function(body) {
      req.open("POST", url, true);
      req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      req.send(body);
    }
}
function getElementHeight(Elem) {
  if(document.getElementById) {
          var elem = document.getElementById(Elem);
  } else if (document.all){
          var elem = document.all[Elem];
  }
  xPos = elem.offsetHeight;
  return xPos;
}
function getElementWidth(Elem) {

  if(document.getElementById) {
          var elem = document.getElementById(Elem);
  } else if (document.all){
          var elem = document.all[Elem];
  }
  xPos = elem.offsetWidth;
  return xPos;

}
