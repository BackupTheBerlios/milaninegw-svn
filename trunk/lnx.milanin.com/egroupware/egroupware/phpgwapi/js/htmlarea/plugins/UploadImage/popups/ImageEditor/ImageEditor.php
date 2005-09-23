<?
/***********************************************************************
** Title.........:  Online Image Editor
** Version.......:  1.0
** Author........:  Xiang Wei ZHUO <wei@zhuo.org>
** Filename......:  ImageEditor.php
** Last changed..:  31 Aug 2003  
** Notes.........:  Configuration in config.inc.php 
**/ 
    $image = '';

    if(isset($_GET['img']))
    {
        $image = $_GET['img'];
        $path_info = pathinfo(urldecode($image));
    }
?>
<html style="width:700px; height:550;">
<HEAD>
<TITLE> Editing: <? echo $path_info['basename']; ?></TITLE>
<script type="text/javascript" src="../popup.js"></script>
<script type="text/javascript" src="../../dialog.js"></script>
<link href="ImageEditor.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript">
<!--

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}


function pviiClassNew(obj, new_style) { //v2.6 by PVII
  obj.className=new_style;
}

function toggleMarker() 
{
    //alert("Toggle");
    var marker = MM_findObj("markerImg");
    
    //alert(marker.src);
    if(marker != null && marker.src != null) {
        //alert(marker.src);
        if(marker.src.indexOf("t_black.gif")>0) {
            marker.src = "t_white.gif";
        }
        else
            marker.src = "t_black.gif";
        //alert(marker.src);
        editor.toggleMarker();
    }
}

function updateMarker(mode) 
{
    if (mode == 'crop')
    {
        var t_cx = MM_findObj('cx');
        var t_cy = MM_findObj('cy');
        var t_cw = MM_findObj('cw');
        var t_ch = MM_findObj('ch');

        editor.setMarker(parseInt(t_cx.value), parseInt(t_cy.value), parseInt(t_cw.value), parseInt(t_ch.value));
    }
    else if(mode == 'scale') {
        var s_sw = MM_findObj('sw');
        var s_sh = MM_findObj('sh');
        editor.setMarker(0, 0, parseInt(s_sw.value), parseInt(s_sh.value));
    }
}


var current_action = null;
var actions = ['crop', 'scale', 'rotate', 'measure', 'save'];
function toggle(action) 
{
    if(action != current_action) {
        var toolbar = MM_findObj('bar_'+action);
        var icon = MM_findObj('icon_'+action);
        var btn = MM_findObj('btn_'+action);
        btn.className='iconsSel';
        current_action = action;
        toolbar.style.display = "block";
        icon.style.display = "block";
        
        for (var i in actions)
        {
            if(current_action != actions[i]) {
                var tool = MM_findObj('bar_'+actions[i]);
                tool.style.display = "none";
                var icon = MM_findObj('icon_'+actions[i]);
                icon.style.display = "none";
                var btn =  MM_findObj('btn_'+actions[i]);
                btn.className = 'icons';
            }
        }

        editor.setMode(current_action);
    }
    //alert(action);
}

function changeClass(obj,action) 
{
    if(action == current_action) {
        obj.className = 'iconsSel';
    }
    else
    {
        obj.className = 'icons';
    }
}

function rotatePreset(selection) 
{
    var value = selection.options[selection.selectedIndex].value;
    
    if(value.length > 0 && parseInt(value) != 0) {
        var ra = MM_findObj('ra');
        ra.value = parseInt(value);
    }
}

function updateFormat(selection) 
{
    var selected = selection.options[selection.selectedIndex].value;

    var values = selected.split(",");
    //alert(values.length);
    if(values.length >1) {
        updateSlider(parseInt(values[1]));
    }

}

function onCancel() {
  __dlg_close(null);
  return false;
};

function onOK() 
{
  __dlg_close(null);
  return false;
}
function Init() {
  __dlg_init();
}
    
//-->
</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</HEAD>

<BODY onLoad="Init();" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" height="100%" cellspacing="1" >
    <tr bgcolor="#EEEEFF" height="40">
    <td class="topBar" width="60" ><table width="100%" border="0" cellspacing="8" cellpadding="2">
        <tr> 
          <td class="icons">
          <div id="icon_crop" style="display:none">
            <img src="crop.gif" alt="Crop" width="20" height="20" border="0">
          </div>
          <div id="icon_scale" style="display:none">
            <img src="scale.gif" alt="Resize" width="20" height="20" border="0">
          </div>
          <div id="icon_rotate" style="display:none">
            <img src="rotate.gif" alt="Rotate" width="20" height="20" border="0">
          </div>
          <div id="icon_measure" style="display:none">
            <img src="ruler.gif" alt="Measure" width="20" height="20" border="0">
          </div>
          <div id="icon_save" style="display:none">
            <img src="save.gif" alt="Save" width="20" height="20" border="0">
          </div>
          </td>
        </tr>
      </table></td>
      
    <td>
<!-- crop -->
<div id="bar_crop" style="display:none">
    <table border="0" cellspacing="5" cellpadding="2">
        <tr> 
          <td class="topBar">Start X: 
            <INPUT TYPE="text" id="cx" style="width:3em" NAME="cx" onChange="updateMarker('crop')">
            Start Y: 
            <INPUT TYPE="text" id="cy" style="width:3em" NAME="cy" onChange="updateMarker('crop')">
            Width: 
            <INPUT TYPE="text" id="cw" style="width:3em" NAME="cw" onChange="updateMarker('crop')">
            Height: 
            <INPUT TYPE="text"  id="ch" style="width:3em" NAME="ch" onChange="updateMarker('crop')"> </td>
          <td><img src="div.gif" width="2" height="30"></td>
          <td class="icons" onMouseOver="pviiClassNew(this,'iconsOver')" onMouseOut="pviiClassNew(this,'icons')"><a href="#" onClick="editor.reset();"><img src="btn_cancel.gif" width="30" alt="Cancel" height="30" border="0"></a></td>
          <td class="icons" onMouseOver="pviiClassNew(this,'iconsOver')" onMouseOut="pviiClassNew(this,'icons')"><a href="#" onClick="editor.doSubmit('crop');"><img src="btn_ok.gif" alt="Apply" width="30" height="30" border="0"></a></td>
        </tr>
      </table> 
</div>
<!-- //crop -->
<!-- measure -->
<div id="bar_measure" style="display:none">
      <table border="0" cellspacing="5" cellpadding="2">
        <tr> 
          <td class="topBar">X: 
            <span id="sx" class="measureStats"></span>
            Y: 
            <span id="sy" class="measureStats"></span></td>
          <td class="topBar"><img src="div.gif" width="2" height="30"></td>
          <td class="topBar"> W: 
            <span id="mw" class="measureStats"></span>
            H: 
            <span id="mh" class="measureStats"></span>
          </td>
          <td><img src="div.gif" width="2" height="30"></td>
          <td class="topBar">A: 
            <span id="ma" class="measureStats"></span>
            D: <span id="md" class="measureStats"></span>
          </td>
          <td class="icons"><img src="div.gif" width="2" height="30"></td>
          <td class="icons"><input type="button" name="Button" value="Clear" onClick="editor.reset()"></td>
        </tr>
      </table> 
</div>
<!-- //measure -->
<!-- scale -->
<div id="bar_scale" style="display:none">
      <table border="0" cellspacing="5" cellpadding="2">
        <tr> 
          <td class="topBar">Width: 
            <input type="text" id="sw" style="width:3em" name="sw" onChange="updateMarker('scale')"> 
          </td>
          <td class="topBar"><img src="locked.gif" width="8" height="14"></td>
          <td class="topBar"> Height: 
            <INPUT TYPE="text"  id="sh" style="width:3em" NAME="sh" onChange="updateMarker('scale')"> 
          </td>
          <td><img src="div.gif" width="2" height="30"></td>
          <td class="icons" onMouseOver="pviiClassNew(this,'iconsOver')" onMouseOut="pviiClassNew(this,'icons')"><a href="#" onClick="editor.reset();"><img src="btn_cancel.gif" alt="Cancel" width="30" height="30" border="0"></a></td>
          <td class="icons" onMouseOver="pviiClassNew(this,'iconsOver')" onMouseOut="pviiClassNew(this,'icons')"><a href="#" onClick="editor.doSubmit('scale');"><img src="btn_ok.gif" alt="Apply" width="30" height="30" border="0"></a></td>
        </tr>
      </table>
</div>
<!-- //scale -->
<!-- rotate -->
<div id="bar_rotate" style="display:none">
      <table border="0" cellspacing="5" cellpadding="2">
        <tr>
          <td width="115" class="topBar"><select id="flip" name="flip">
              <option selected>Flip Image</option>
              <option>-----------------</option>
              <option value="hoz">Flip Horizontal</option>
              <option value="ver">Flip Virtical</option>
            </select></td>
          <td width="115" class="topBar"><select name="rotate" onChange="rotatePreset(this)">
              <option selected>Rotate Image</option>
              <option>-----------------</option>
              <option value="180">Rotate 180 &deg;</option>
              <option value="90">Rotate 90 &deg; CW</option>
              <option value="-90">Rotate 90 &deg; CCW</option>
            </select></td>
          <td width="87" class="topBar"> Angle: 
            <INPUT TYPE="text"  id="ra" style="width:3em" NAME="ra" onChange="updateMarker('rotate')"> 
          </td>
          <td width="2"><img src="div.gif" width="2" height="30"></td>
          <td width="32" class="icons" onMouseOver="pviiClassNew(this,'iconsOver')" onMouseOut="pviiClassNew(this,'icons')"><a href="#" onClick="editor.reset();"><img src="btn_cancel.gif" alt="Cancel" width="30" height="30" border="0"></a></td>
          <td width="32" class="icons" onMouseOver="pviiClassNew(this,'iconsOver')" onMouseOut="pviiClassNew(this,'icons')"><a href="#" onClick="editor.doSubmit('rotate');"><img src="btn_ok.gif" alt="Apply" width="30" height="30" border="0"></a></td>
        </tr>
      </table>
</div>
<!-- //rotate -->
<!-- save -->
<div id="bar_save" style="display:none">
      <table border="0" cellspacing="5" cellpadding="2">
        <tr>
          <td class="topBar">Filename: 
            <input type="filename" id="save_filename" value="<? echo $path_info['basename']; ?>" name="textfield"></td>
          <td class="topBar"> <select name="format" id="save_format" onChange="updateFormat(this)">
              <option value="" selected>Image Format</option>
              <option value="">---------------------</option>
              <option value="jpeg,85">JPEG High</option>
              <option value="jpeg,60">JPEG Medium</option>
              <option value="jpeg,35">JPEG Low</option>
              <option value="png">PNG</option>
              <option value="gif">GIF</option>
            </select></td>
          <td class="topBar">Quality: 
          </td>
          <td width="120">
    <div id="slidercasing"> 
<div id="slidertrack" style="width:100px"><IMG SRC="spacer.gif" WIDTH="1" HEIGHT="1" BORDER="0" ALT="track"></div>
                <div id="sliderbar" style="left:50px" onmousedown="captureStart()"><IMG SRC="spacer.gif" WIDTH="1" HEIGHT="1" BORDER="0" ALT="track"></div></div>
          </td>
          <td class="topBar"> 
<INPUT TYPE="text" id="quality" NAME="quality" onChange="updateSlider(this.value)" style="width:2em">
<script type="text/javascript" src="jscripts/slider.js"></script>
<script language="JavaScript1.2">
<!--

updateSlider(85);

//-->
</script>


          </td>
          <td><img src="div.gif" width="2" height="30"></td>
          <td class="icons" onMouseOver="pviiClassNew(this,'iconsOver')" onMouseOut="pviiClassNew(this,'icons')"><a href="#" onClick="editor.reset();"><img src="btn_cancel.gif" alt="Cancel" width="30" height="30" border="0"></a></td>
          <td class="icons" onMouseOver="pviiClassNew(this,'iconsOver')" onMouseOut="pviiClassNew(this,'icons')"><a href="#" onClick="editor.doSubmit('save');"><img src="btn_ok.gif" alt="Apply" width="30" height="30" border="0"></a></td>
        </tr>
      </table>
</div>
<!--//save -->
      </td>
    </tr>
    <tr>
        
    <td bgcolor="#EEEEFF" width="60" valign="top" align="center" nowrap><table width="100%" border="0" cellspacing="8" cellpadding="2">
        <tr> 
          <td class="icons" id='btn_crop' onMouseOver="pviiClassNew(this,'iconsOver')" onMouseOut="changeClass(this,'crop')"><a href="#" class="iconText" onClick="javascript:toggle('crop')"><img src="crop.gif" alt="Crop" width="20" height="20" border="0"><br>
            Crop</a> </td>
        </tr>
        <tr> 
          <td class="icons" id='btn_scale' onMouseOver="pviiClassNew(this,'iconsOver')" onMouseOut="changeClass(this,'scale')"><a href="#" class="iconText" onClick="javascript:toggle('scale')"><img src="scale.gif" alt="Resize" width="20" height="20" border="0"><br>
            Resize</a> </td>
        </tr>
        <tr> 
          <td class="icons" id='btn_rotate' onMouseOver="pviiClassNew(this,'iconsOver')" onMouseOut="changeClass(this,'rotate')"><a href="#" class="iconText"  onClick="javascript:toggle('rotate')"><img src="rotate.gif" alt="Rotate" width="20" height="20" border="0"><br>
            Rotate</a> </td>
        </tr>
        <tr> 
          <td class="icons" id='btn_measure' onMouseOver="pviiClassNew(this,'iconsOver')" onMouseOut="changeClass(this,'measure')"><a href="#" class="iconText"  onClick="javascript:toggle('measure')"><img src="ruler.gif" alt="Measure" width="20" height="20" border="0"><br>
            Measure</a></td>
        </tr>
        <tr> 
          <td class="icons" onMouseOver="pviiClassNew(this,'iconsOver')" onMouseOut="pviiClassNew(this,'icons')"><a class="iconText" href="#" onClick="toggleMarker();"><img src="t_black.gif" name="markerImg" id="markerImg" alt="Marker" width="20" height="20" border="0"><br>
            Marker</a></td>
        </tr>
        <tr>
          <td class="icons" id='btn_save' onMouseOver="pviiClassNew(this,'iconsOver')" onMouseOut="changeClass(this,'save')"><a href="#" class="iconText"  onClick="javascript:toggle('save')"><img src="save.gif" alt="Save" width="20" height="20" border="0"><br>
            Save</a> </td>
        </tr>
      </table>
    </td>
        <td width="99%" >
        <iframe width="100%" height="100%" id="editor" name="editor" src="load_image.php?img=<? echo $image; ?>" marginwidth="0" marginheight="0" align="top" scrolling="auto" frameborder="0" hspace="0" vspace="0" background="gray">
        </iframe>
        </td>
    </tr>
</table>
</BODY>
</HTML>
