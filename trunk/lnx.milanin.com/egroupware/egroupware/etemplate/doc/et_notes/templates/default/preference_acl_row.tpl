<!-- $Id: preference_acl_row.tpl,v 1.1 2003/04/03 01:02:31 ralfbecker Exp $ -->
	<tr bgcolor="{row_color}">
		<td>{user}</td>
		<td align="center"><input type="checkbox" name="{read}" value="Y"{read_selected}></td>
		<td align="center"><input type="checkbox" name="{add}" value="Y"{add_selected}></td>
		<td align="center"><input type="checkbox" name="{edit}" value="Y"{edit_selected}></td>
		<td align="center"><input type="checkbox" name="{delete}" value="Y"{delete_selected}></td>
		<td align="center"><input type="checkbox" name="{private}" value="Y"{private_selected}></td>
	</tr>
