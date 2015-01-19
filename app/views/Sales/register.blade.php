 <!DOCTYPE HTML>
<html>
<body>
	Test
	{{ Form::open(array('action' => 'DemoController@create','method' => 'POST')) }}
 <div id='content'>
  <!-- This is our model data: {{ $data }}! -->
  <table>
  	<tr><td>First Name</td><td>{{ Form::text('first_name') }}</td></tr>
  	<tr><td>Last Name </td><td>{{ Form::text('last_name') }}</td></tr>
  	<tr><td></td><td>{{ Form::submit('Submit') }}</td></tr>
  </table>
 </div>
{{ Form::close() }}
</body>
</html> 