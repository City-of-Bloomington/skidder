<h2>Skidder brings logs to you</h2>
<p>Skidder listens for log postings from various applications.  When there's something
happening on an application you've subscribed to, skidder will notify you.  You can browse
the full details of the logs here.</p>

<h2>How to POST logs</h2>
<p>Once you've added an application here, that application will be allowed to POST log entries
here.  Log entries should be posted to:</p>
<h3><?php echo BASE_URL; ?>/home.php</h3>
<p>Log entry POSTs must contain these fields:</p>
<table>
	<tr><th>application_id</th>
		<td>The ID issued to that application</td>
	</tr>
	<tr><th>script</th>
		<td>The file the error occurred in.  This will be used to organize the logs</td>
	</tr>
	<tr><th>type</th>
		<td>The type of error</td>
	</tr>
	<tr><th>message</th>
		<td>The full message body of the error.</td>
	</tr>
</table>
