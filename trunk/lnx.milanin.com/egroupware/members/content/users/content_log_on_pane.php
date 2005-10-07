<form action="/egroupware/login.php" method="post">

	<table width="350" class="actionbox">
	
		<caption align="top">
			Log On
		</caption>
		<tr>
			<td>
				<label for="username">Username</label>
			</td>
			<td>
				<input type="text" name="login" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="password">Password</label>
			</td>
			<td>
				<input type="password" name="passwd" />
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<label><input type="checkbox" name="remember" checked="checked" /> Remember Login</label>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="right">
				<label>Log on: <input type="submit" name="submit" value="Go" /></label>
			</td>
		</tr>
	
	</table>
<input type="hidden" name="passwd_type" value="text"/>
<input type="hidden" name="account_type" value="u"/>
<input type="hidden" name="phpgw_forward" value="<?=$_SERVER['PHP_SELF']?>"/>
</form>
