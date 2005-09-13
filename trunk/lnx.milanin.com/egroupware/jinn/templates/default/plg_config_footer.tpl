    </table>
</form>
<script type="text/JavaScript">
<!--
	
	function fake_submit()
          {
	         window.opener.document.frm.{fld_name}.value={newconfig};
	         self.close();
		  }
		  

//-->
</script>
<div align="center">
<input type="button" value="{save}" onClick="fake_submit()" />
<input type="button" value="{cancel}" onClick="self.close()" />
</div>
</div>
</div>
</body>
</html>
