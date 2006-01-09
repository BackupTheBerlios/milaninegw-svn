<?php
/*
* Copyright 2003 - 2005 Mark O'Sullivan
* This file is part of Lussumo's Software Library.
* Lussumo's Software Library is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
* Lussumo's Software Library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Vanilla; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* The latest source code is available at www.lussumo.com
* Contact Mark O'Sullivan at mark [at] lussumo [dot] com
* 
* Description: Manages retrieving and setting global constants
* Applications utilizing this file: Vanilla;
*/

class ConstantManager {
   
   var $Context;
   var $Constants;
   
   function ConstantManager(&$Context) {
      $this->Context = &$Context;
      $this->Constants = array();
   }
   
   function DefineConstantsFromFile($File) {
      $Lines = @file($File);
      if (!$Lines) {
         $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrReadFileConstants").$File);
      } else {
         $CurrentLine = "";
         for ($i = 0; $i < count($Lines); $i++) {
            $CurrentLine = trim($Lines[$i]);
            $Type = "";
            if (substr($CurrentLine, 0, 8) == "define(\"") {
               $CommentPosition = strpos($CurrentLine, "\");");
               if ($CommentPosition !== false) $CurrentLine = substr($CurrentLine, 0, $CommentPosition+3);
               $CurrentLine = str_replace("define(\"", "", $CurrentLine);
               $CurrentLine = str_replace("\");", "", $CurrentLine);
               $Values = explode("\", \"", $CurrentLine);
               if (count($Values) == 2) {
                  $Values[0] = trim(str_replace("\\\"", "\"", $Values[0]));
                  $Values[1] = trim(str_replace("\\\"", "\"", $Values[1]));
                  $this->SetConstant($Values[0], $Values[1]);
               }               
            }
         }
      }
   }

   function EncodeConstantName($Name) {
      $SafeCharacters = explode(",","a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,1,2,3,4,5,6,7,8,9,0");
      $NewName = "";
      for ($i = 0; $i < strlen($Name); $i++) {
         $Character = substr($Name, $i, 1);
         if (!in_array($Character, $SafeCharacters)) {
            $NewName .= "_";
         } else {
            $NewName .= $Character;
         }         
      }
      return $NewName;
   }
   
   function EncodeConstantValue($Value) {
      return htmlentities($Value, ENT_QUOTES);
   }
   
   function EncodeConstantValueForSaving($Value) {
      return str_replace("\"", "\\\"", html_entity_decode($Value, ENT_QUOTES));
//      return addslashes(html_entity_decode($Value, ENT_QUOTES));
   }
   
   function GetConstant($ConstantName) {
      if (array_key_exists($ConstantName, $this->Constants)) {
         return $this->Constants[$ConstantName];
      } else {
         return "";
      }
   }
   
   function UpdateConstantsFileContents($File) {
      $Lines = @file($File);
      if (!$Lines) {
         $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrReadFileConstants").$File);
      } else {
         $CurrentLine = "";
         $CurrentConstant = "";
         for ($i = 0; $i < count($Lines); $i++) {
            $CurrentLine = trim($Lines[$i]);
            $Comments = "";
            if (substr($CurrentLine, 0, 8) == "define(\"") {
               $CommentPosition = strpos($CurrentLine, "\");");
               if ($CommentPosition !== false) {
                  $Comments = substr($CurrentLine, $CommentPosition+3);
                  $CurrentLine = substr($CurrentLine, 0, $CommentPosition+3);
               }
               $CurrentLine = str_replace("define(\"", "", $CurrentLine);
               $CurrentLine = str_replace("\");", "", $CurrentLine);
               $Values = explode("\", \"", $CurrentLine);
               if (count($Values) == 2) {
                  $CurrentConstant = trim(str_replace("\"", "", $Values[0]));
                  if (array_key_exists($CurrentConstant, $this->Constants)) {
                     $Lines[$i] = "define(\"".$CurrentConstant."\", \"".$this->EncodeConstantValueForSaving($this->Constants[$CurrentConstant])."\"); ".$Comments."\r\n";
                  }
               }               
            }
         }
      }      

      return implode("", $Lines);
   }
   
   function GetConstantsFromForm($TemplateFile) {
      // First define the constants again
      $this->DefineConstantsFromFile($TemplateFile);
      while (list($Name, $OriginalValue) = each($this->Constants)) {
         if (isset($_POST[$Name])) {
            $Value = ForceIncomingString($Name, "");
         } else {
            $Value = $OriginalValue;
         }
         $this->SetConstant($Name, $Value, 1);
      }
   }

   function RemoveConstant($Name) {
      $key_index = array_keys(array_keys($this->Constants), $Name); 
		if (count($key_index) > 0) array_splice($this->Constants, $key_index[0], 1);
   }
   
   function SaveConstantsToFile($File) {
      // Open for writing only.
      // Place the file pointer at the beginning of the file and truncate the file to zero length. 
      // If the file does not exist, attempt to create it.
      $FileContents = $this->UpdateConstantsFileContents($File);
      if ($this->Context->WarningCollector->Iif()) {
         $FileHandle = @fopen($File, "wb");
         if (!$FileHandle) {
            $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrOpenFileStart").$File.$this->Context->GetDefinition("ErrOpenFileEnd"));
         } else {
            if (!@fwrite($FileHandle, $FileContents)) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrWriteFile"));
         }
         @fclose($FileHandle);
      }
      return $this->Context->WarningCollector->Iif();
   }
   
   function SetConstant($Name, $Value, $ForSaving = "0") {
      $Name = $this->EncodeConstantName($Name);
      if (!$ForSaving) $Value = $this->EncodeConstantValue($Value);
      $this->Constants[$Name] = $Value;
   }
}
?>