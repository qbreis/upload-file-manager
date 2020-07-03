<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link crossorigin="anonymous" media="all" rel="stylesheet" href="css/styles.css" />
<title>Sube tu archivo</title>
</head>
<body><pre><?php print_r($_SERVER);?></pre>
	<div class="form-container">
		<form enctype="multipart/form-data" action="upload" method="POST" class="form">
			<input type="hidden" name="Authorization-ud157" value="AdminUser-ud_878" />
			<a href="./"><img src="img/logo.png" class="logo" /></a>
			<div class="form-card">
				<h2>Sube tu archivo</h2>
				<input type="file" name="uploaded_file"></input><br />
				<input type="submit" value="Subir"></input>
				<div class="requirements">
					&bull; Solamente se pueden subir archivos PDF cuyo tamaño no supere los 10M.
				</div><!-- .requirements -->
			</div><!-- .form-card -->
		</form>
	</div><!-- .form-container -->
</body>
</html>