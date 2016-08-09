<html>
	 <head> 
	 <title>Send mail html example</title>  
	 	 <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	 </head>
	<body>
 		<form action="WebSendMail.php" method="post" enctype="multipart/form-data">
		  <p>api_user: <input type="text" name="api_user" /></p>
		  <p>api_key: <input type="text" name="api_key" /></p>
		  <p>to: <input type="text" name="to" /></p>
		  <p>from: <input type="text" name="from" /></p> 
		  <p>fromname: <input type="text" name="fromname" /></p>
		  <p>replyto: <input type="text" name="replyto" /></p>
		  <p>cc: <input type="text" name="cc" /></p>
		  <p>bcc: <input type="text" name="bcc" /></p>
		  <p>subject: <input type="text" name="subject" /></p>
		  <p>html: <textarea rows="10" cols="50" name="html" ></textarea></p>
		  <p>text: <textarea rows="10" cols="50" name="text" ></textarea></p>
		  <p>x-smtpapi: <textarea rows="30" cols="50" name="x-smtpapi"></textarea></p>
		  <p>file1: <input type="file" name="files1" /></p> 
		  <p>file2: <input type="file" name="files2" /></p> 
		  <input type="submit" value="Submit" />
		</form>
	</body>
</html>