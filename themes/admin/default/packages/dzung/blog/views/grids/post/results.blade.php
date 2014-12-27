<script type="text/template" data-grid="post" data-template="results">

	<% _.each(results, function(r) { %>

		<tr>
			{{--<td><input content="id" name="entries[]" type="checkbox" value="<%= r.id %>"></td>--}}
			<td><a href="{{ URL::toAdmin('blog/posts/<%= r.id %>/edit') }}"><%= r.id %></a></td>
			<td><%= r.title %></td>
			<td><%= r.content %></td>
			<td><%= r.slug %></td>
			<td><%= r.category_id %></td>
			<td><%= r.created_at %></td>
			<td><a href="{{ URL::toAdmin('blog/posts/<%= r.id %>/edit') }}">Edit</a> | <a href="{{ URL::toAdmin('blog/posts/<%= r.id %>/delete') }}">Delete</a></td>
		</tr>

	<% }); %>

</script>
