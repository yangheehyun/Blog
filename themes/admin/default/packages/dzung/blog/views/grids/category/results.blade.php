<script type="text/template" data-grid="category" data-template="results">

	<% _.each(results, function(r) { %>

		<tr>
			<td><input content="id" name="entries[]" type="checkbox" value="<%= r.id %>"></td>
			<td><a href="{{ URL::toAdmin('blog/categories/<%= r.id %>/edit') }}"><%= r.id %></a></td>
			<td><%= r.name %></td>
			<td><%= r.num_post %></td>
			<td><%= r.created_at %></td>
		</tr>

	<% }); %>

</script>
