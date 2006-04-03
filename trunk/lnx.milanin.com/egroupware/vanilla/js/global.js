/*
* Copyright 2003 - 2005 Mark O'Sullivan
* This file is part of Lussumo's Software Library.
* Lussumo's Software Library is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
* Lussumo's Software Library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Vanilla; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* The latest source code is available at www.lussumo.com
* Contact Mark O'Sullivan at mark [at] lussumo [dot] com
* 
* Description: Non-application specific utility functions
*/

if(document.all && !document.getElementById) {
    document.getElementById = function(id) {
         return document.all[id];
    }
}

var request = false;

function toggleLayer(whichLayer)
          {
            if (document.getElementById)
            {
          // this is the way the standards work
              var style2 = document.getElementById(whichLayer).style;
              alert(style2.display);
              style2.display = style2.display? "":"block";
            }
            else if (document.all)
            {
          // this is the way old msie versions work
              var style2 = document.all[whichLayer].style;
              style2.display = style2.display? "":"block";
            }
            else if (document.layers)
            {
          // this is the way nn4 works
              var style2 = document.layers[whichLayer].style;
              style2.display = style2.display? "":"block";
            }
}
function BlockSubmit(evt, Handler) {
	 var Key = evt.keyCode || evt.which;
	 if (Key == 13) {
		  Handler();
		  return false;
	 } else {
		  return true;
	 }
}

function ChangeElementText(ElementID, NewText) {
	var Element = document.getElementById(ElementID);
	if (Element) Element.innerHTML = NewText;
}

function CheckBox(BoxID) {
	var Box = document.getElementById(BoxID);
	if (Box) Box.checked = !Box.checked;
}

function ClearContents(Container) {
	if (Container) Container.innerHTML = "";
}

function GetElements(ElementName, ElementIDPrefix) {
	var Elements = document.getElementsByTagName(ElementName);
	var objects = new Array();
	for (i = 0; i < Elements.length; i++) {
		if (Elements[i].id.indexOf(ElementIDPrefix) == 0) {
			objects[objects.length] = Elements[i];			
		}
	}
	return objects;
}

function HideElement(ElementID, ClearElement) {
	var Element = document.getElementById(ElementID);
	if (Element) {
		Element.style.display = "none";
		if (ClearElement == 1) ClearContents(Element);
	}
}

function SetRadioValue(InputID) {
	 var Radio = document.getElementById(InputID);
	 if (Radio) Radio.checked = true;
}

function SubmitForm(FormName, Sender) {
	Sender.disabled = true;
	Sender.value = "Wait";
        var Element = document.getElementById("CommentBox");
//         var inst=tinyMCE.getInstanceById("mce_editor_0");
 	document[FormName].submit();
}

function SubmitReply(FormName, Sender) {
  Sender.disabled = true;
  Sender.value = "Wait";
  var Element = document.getElementById("CommentBox");
  var inst=tinyMCE.getInstanceById("mce_editor_0");
        
        //alert(inst.getBody().innerHTML+"\n---\n"+Element.innerHTML+"\n---\n"+tinyMCE.getContent());
//         Element.innerHTML=tinyMCE.getContent();
// 	document[FormName].submit();
//   tinyMCE.triggerSave();
  post_reply(document["frmPostComment"],Sender);
  inst.getBody().innerHTML="";
}
function toggleCommentBody(ID){
  var Element = document.getElementById("CommentBody_"+ID);
  if (Element.innerHTML==""){
    try {
      request = new XMLHttpRequest();
    } catch (trymicrosoft) {
      try {
        request = new ActiveXObject("Msxml2.XMLHTTP");
      } catch (othermicrosoft) {
        try {
          request = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (failed) {
          request = false;
        }
      }
    }
    if (!request)
    alert("Error initializing XMLHttpRequest!");
    request.open("GET", "comment.php?CommentID="+ID, true);
    request.onreadystatechange = function(){
//       alert("called updateCommentBody!");
//       alert("state = "+request.readyState);
      if (request.readyState == 4) {
//         alert("state is 4!");
        if (request.status == 200) {
          var Element = document.getElementById("CommentBody_"+ID);
          var response = request.responseText;
//           alert("answer is " + request.responseText);
          Element.innerHTML = response;
        } else {
          alert("status is " + request.status);
        }
        SwitchElementClass("CommentBody_"+ID, "CommBodySwitcher_"+ID, "CommentBodyHidden", "CommentBody", "Show", "Hide");
      }
    }
    request.send(null);
  } else {
    SwitchElementClass("CommentBody_"+ID, "CommBodySwitcher_"+ID, "CommentBodyHidden", "CommentBody", "Show", "Hide");
  }
}

function addQuoteToCommentBody(ID){
  var Element = document.getElementById("CommentBody_"+ID);
  if (Element.innerHTML!=""){
    try {
      request = new XMLHttpRequest();
    } catch (trymicrosoft) {
      try {
        request = new ActiveXObject("Msxml2.XMLHTTP");
      } catch (othermicrosoft) {
        try {
          request = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (failed) {
          request = false;
        }
      }
    }
    if (!request)
    alert("Error initializing XMLHttpRequest!");
    request.open("GET", "comment.php?CommentID="+ID+"&quote=1", true);
    request.onreadystatechange = function(){
      if (request.readyState == 4) {
        if (request.status == 200) {
          var Element = document.getElementById("CommentBox");
          var response = request.responseText;
          var inst=tinyMCE.getInstanceById("mce_editor_0");
          inst.getBody().innerHTML+=response;
//           alert(inst.getBody());
//           tinyMCE.execInstanceCommand("mce_editor_0","mceInsertContent",false,response);
          Element.innerHTML = Element.innerHTML+response;
//           tinyMCE.updateContent("Body");
//           alert(tinyMCE.getInstanceById("mce_editor_0")+"---"+Element.innerHTML+"---"+tinyMCE.getContent("Body"));
//           tinyMCE.execCommand("mceFocus",false,"Body");
//           tinyMCE.updateContent("Body");
//           tinyMCE.triggerSave();
          
          
        } else {
          alert("status is " + request.status);
        }
//         SwitchElementClass("CommentBody_"+ID, "CommBodySwitcher_"+ID, "CommentBodyHidden", "CommentBody", "Show", "Hide");
      }
    }
    request.send(null);
  } else {
    alert("Nothing to quote!");
  }
}

function formData2QueryString(docForm) {

  var submitContent = '';
  var formElem;
  var lastElemName = '';
  
  for (i = 0; i < docForm.elements.length; i++) {
    
    formElem = docForm.elements[i];
    switch (formElem.type) {
      // Text fields, hidden form elements
      case 'text':
          submitContent += formElem.name + '=' + escape(formElem.value) + '&'
      case 'hidden':
          submitContent += formElem.name + '=' + escape(formElem.value) + '&'
      case 'password':
      case 'textarea':
          submitContent += formElem.name + '=' + escape(formElem.innerHTML) + '&'
      case 'select-one':
          submitContent += formElem.name + '=' + escape(formElem.value) + '&'
          break;
        
      // Radio buttons
      case 'radio':
          if (formElem.checked) {
        submitContent += formElem.name + '=' + escape(formElem.value) + '&'
          }
          break;
        
      // Checkboxes
      case 'checkbox':
          if (formElem.checked) {
          // Continuing multiple, same-name checkboxes
        if (formElem.name == lastElemName) {
            // Strip of end ampersand if there is one
          if (submitContent.lastIndexOf('&') == submitContent.length-1) {
            submitContent = submitContent.substr(0, submitContent.length - 1);
          }
            // Append value as comma-delimited string
          submitContent += ',' + escape(formElem.value);
        }
        else {
          submitContent += formElem.name + '=' + escape(formElem.value);
        }
        submitContent += '&';
        lastElemName = formElem.name;
          }
          break;
        
    }
  }
  // Remove trailing separator
  var Element = document.getElementById("CommentBox");
//   submitContent = submitContent.substr(0, submitContent.length - 1);
  //("Body="+Element.innerHTML+tinyMCE.getContent("mce_editor_0"))
      return submitContent+"Body="+escape(tinyMCE.getContent("mce_editor_0"));
}

function post_reply(commentbox,Sender)
{
  if (!request) {
    try {
      request = new XMLHttpRequest();
    } catch (trymicrosoft) {
      try {
        request = new ActiveXObject("Msxml2.XMLHTTP");
      } catch (othermicrosoft) {
        try {
          request = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (failed) {
          request = false;
        }
      }
    }
  }
  if (!request && window.createRequest) {
    try {
      request = window.createRequest();
    } catch (e) {
      request=false;
    }
  }
  submitContent =  formData2QueryString(commentbox)
      request.open("POST", "post.php", true);
//   alert("["+submitContent+"]");
  request.onreadystatechange=function() {
    if (request.readyState==4) {
      try {
        if(request.status == 200)
//         document.reload();
        /*alert*/(request.responseText);
        var CommentsGrid=document.getElementById("CommentGrid");
        CommentsGrid.innerHTML+=request.responseText;
        Sender.disabled=false;
        Sender.value="Add Your Comment";
      }catch (e){
        alert(e)
      }
	
    }
  }
  request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  request.send(submitContent);
}

function SwitchElementClass(ElementToChangeID, SenderID, StyleA, StyleB, CommentA, CommentB) {
	var Element = document.getElementById(ElementToChangeID);
	Sender = document.getElementById(SenderID);
	if (Element && Sender) {
		if (Element.className == StyleB) {
			Element.className = StyleA;
			Sender.innerHTML = CommentA;
		} else {
			Element.className = StyleB;
			Sender.innerHTML = CommentB;
		}			
	}
}

function WriteEmail(d, n, v) {
	document.write("<a "+"hre"+"f='mai"+"lto:"+n+"@"+d+"'>");
	if (v == '') {
		document.write(n+"@"+d);
	} else {
		document.write(v);
	}
	document.write("</a>");
}

